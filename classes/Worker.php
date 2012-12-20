<?php
class Worker {
    private $config;
    private $adapter;
    private $aurora;

    function __construct() {
        // Initialize objects
        $this->adapter = PDODataAdapter::getInstance();
        $this->config = Session::getConfig();
    }

    function __destruct() {
        // Release objects
        $this->aurora = null;
        $this->adapter = null;
        $this->config = null;
    }

    public function start() {
        /*
         * Start main script
        */

        $isAlive = false;
        // Create a lock
        $this->createLock();
        
        R::begin(); // Start a transaction to speed things up
        foreach ($this->config->inverters as $inverter) {
            // Try to get the selected api class for this inverter
            $this->aurora = $inverter->getApi($this->config);
            if ($this->aurora == null) {
            	// If nothing is received, we will use the aurora class (Compatibility mode)
            	$this->aurora = new Aurora($this->config->aurorapath, $inverter->comAddress, $this->config->comPort, $this->config->comOptions, $this->config->comDebug);
            }

            $datareturn = $this->aurora->getData();

            // Convert datareturn to live object
            $live = AuroraConverter::toLive($datareturn);

            $isSunDown = Util::isSunDown($this->config);
            
            if ($live == null) {
            	// When $live is empty and the sun is down then we probably are down

            	// we going to set the Inverter state to 1;
            	if($isSunDown==true){
            		// The sun is NOT Shining, so we set the inverter state to Sleep=False
	            	$changeStateTo = false;
            	}
                if ($isSunDown) {
                	// Fire an shutDown hook once a day (20 hours)
                	//check to see if the inverter is going to sleep.
                	if (PeriodHelper::isPeriodJob("ShutDownJobINV" . $inverter->id, (2 * 60))) {
                		HookHandler::getInstance()->fire("onInverterShutdown", $inverter);                		
                	}


                    // instead of continues polling the inverter during the night we give at a 2 minute break
                	HookHandler::getInstance()->fire("onDebug", "No response and the sun is probably down. Inverter is probably a sleep, waiting for 2 minutes.");
                    sleep(120);
                } else {
                	// We shouldn't be down yet, so just wait 30 seconds
                	HookHandler::getInstance()->fire("onDebug", "No valid response. Inverter is probably busy or down, waiting for 30 seconds.");
                    sleep(30);
                }
            } else {
                $isAlive = true; // The inverter responded
                // check if the inverter is awaking
				
                // we going to set the Inverter state to 0;
                // if isSunDown === false (sun is Shining!)
                if($isSunDown==false){
                	// The sun is Shining, so we set the inverter state to Active=True
                	$changeStateTo = true;
                }
                
                // Write the current live value
                $this->adapter->writeLiveInfo($inverter->id, $live);

                // Check the Max value
                $this->checkMaxPowerValue($inverter, $live);

                // Check if there are alarms
                if (PeriodHelper::isPeriodJob("EventJob", 2)) {
                    $this->checkAlarms($inverter);
                }
            }

            /*
             * Do period stuff below
            */

            // History
            if ($isAlive && PeriodHelper::isPeriodJob("HistoryJob", 5)) {
            	
            	$this->adapter->addHistory($inverter->id, $live);

                $arHistory = $this->adapter->readHistory($inverter->id, null);

                // Fist line means inverter awake
                
                /**
                 * 
                 * Disabled because of the "new" check
                 * 
                if(count($arHistory) == 1) {
                    // The interver is awake
                    $OEvent = new Event($inverter->id, time(), 'Notice', 'Inverter awake');
                    $this->adapter->addEvent($inverter->id, $OEvent);
                    HookHandler::getInstance()->fire("onInverterStartup", $OEvent->event);
                }
                */
                
                // Energy check every 30 minutes
                if (PeriodHelper::isPeriodJob("EnergyJob", 30)) {

                    // The first hour we dont get much kwh, so wait for at least ten history lines
                    $first = reset($arHistory);
                    $last = end($arHistory);

                    $productionStart = $first['KWHT'];
                    $productionEnd = $last['KWHT'];

                    // Check if we passed 100.000kWh
                    if ($productionEnd < $productionStart) {
                        $productionEnd += 100000;
                    }
                    $production = round($productionEnd - $productionStart, 3);

                    // Set the new values and save it
                    $energy = new Energy();
                    $energy->SDTE = $first['SDTE'];
                    $energy->time = time();
                    $energy->INV = $inverter->id;
                    $energy->KWH = $production;
                    $energy->KWHT = $productionEnd;
                    $energy->co2 = Formulas::CO2kWh($production, $this->config->co2kwh); // Calculate co2
                    $this->adapter->addEnergy($inverter->id, $energy);
                }
                
                HookHandler::getInstance()->fire("onHistory", $inverter, $live);
            }
            
            // Info every 12 hours
            if ($isAlive && PeriodHelper::isPeriodJob("InfoJob", 12 * 60)) {
                sleep(2);
                $info = $this->aurora->getInfo();
                if (trim($info) != "") {
                    // Write InverterInfo (firmware,model,etc) to DB
                    $OEvent = new Event($inverter->id, time(), 'Info', $info);
                    $this->adapter->addEvent($inverter->id, $OEvent);
                }

                // Do we want to synchronize the time off the inverter
                if ($inverter->syncTime == true) {
                    sleep(2);
                    $info = $this->aurora->syncTime();
                }
            }
        }

        // free the inverte api object
        $this->aurora = null;

        $this->dropLock();

        R::commit(); // Commit the transaction

        if (PeriodHelper::isPeriodJob("10MinJob", 10)) {
        	if ($changeStateTo==false){
        		$state = 0;
        		$message = 'Inverter is going to sleep (new worker check)';
        	}
        	if ($changeStateTo==true){
        		$state = 1;
        		$message = 'Inverter is awaking (new worker check)';
        	}
        	$inverterStatus = $this->adapter->changeInverterStatus($state,$inverter->id);
        	if($inverterStatus['changed']==true){
        		if ($changeStateTo==false){
        			HookHandler::getInstance()->fire("sendTweet");
        		}
        		$OEvent = new Event($inverter->id, time(), 'Notice', $message);
        		$this->adapter->addEvent($inverter->id, $OEvent);
        		HookHandler::getInstance()->fire("onInverterShutdown", $OEvent->event);
        	}
        }

        // These hooks will also run if the inverter is down
        if (PeriodHelper::isPeriodJob("1MinJob", 1)) {
        	HookHandler::getInstance()->fire("on1MinJob");
        }
        if (PeriodHelper::isPeriodJob("10MinJob", 10)) {
        	HookHandler::getInstance()->fire("on10MinJob");
        }
        if (PeriodHelper::isPeriodJob("1HourJob", 60)) {
        	HookHandler::getInstance()->fire("on1HourJob");
        }
        

        
        // Make sure te log files are readable and writeable for everyone
        $logPath = dirname(dirname(__FILE__)) . "/log";
        $foldersAndFiles = scandir($logPath);
        $entries = array_slice($foldersAndFiles, 2); // Remove "." and ".." from the list
        // Parse every result...
        foreach($entries as $entry) {
        	if (is_file($logPath . "/" . $entry)) {
	        	chmod($logPath  . "/" . $entry, 0666);
        	}
        }
    }

    /**
     * Check if the live value is higher then the saved value
     * if true, then save it
     * @param Inverter $inverter
     * @param Live $live
     */
    private function checkMaxPowerValue($inverter, $live) {
        // Get the highest value off the day
        $currentMPT = $this->adapter->readMaxPowerToday($inverter->id);
        $COEF=($live->EFF/100)* $inverter->correctionFactor;
        $COEF=($COEF > 1) ? 1 : $COEF;
        $GP2 = round($live->GP * $COEF,2);
        if (!isset($currentMPT) || $GP2 > $currentMPT->GP) {
            // Found a new max power of today
            $Ompt = new MaxPowerToday();
            $Ompt->SDTE = $live->SDTE;
            $Ompt->time = Util::getUTCdate($live->SDTE);
            $Ompt->GP = $GP2;
            $this->adapter->writeMaxPowerToday($inverter->id, $Ompt);
        }
    }

    /**
     * Check if the line is filled with an real alarm
     * @param Event $event
     * @return boolean
     */
    private function isAlarmDetected($event) {
        $event_text = trim($event->event);
        $event_lines = explode("\n", $event_text);

        $alarmFound = false;
        foreach ($event_lines as $line) {
        	// Aurora error
            $parts = explode(":", $line);
            if (count($parts) > 1 && trim($parts[1]) != "No Alarm") {
                $alarmFound = true;
                break;
            }
        }
        
        // SMA
        if (trim($event->event) == "Fehler -------") {
        	$alarmFound = false;
        }

        return $alarmFound;
    }

    /**
     * Checks if there are alarms to save to the database
     * @param Inverter $inverter
     */
    private function checkAlarms($inverter) {
        sleep(2);
        $alarm = $this->aurora->getAlarms();
        if (trim($alarm) != "") {
            //Alarms and warnings to DB
            $OEvent = new Event($inverter->id, time(), 'Alarm', Util::formatEvent($alarm));

            // Only save and send email if there is an real event
            if ($this->isAlarmDetected($OEvent)) {
				try {
	            	if (strpos($OEvent->event, 'Warning') !== false ) {
		            	HookHandler::getInstance()->fire("onInverterWarning", $inverter, nl2br($OEvent->event));
	            	}
	            	if (strpos($OEvent->event, 'Error') !== false ) {
		            	HookHandler::getInstance()->fire("onInverterError", $inverter, nl2br($OEvent->event));
	            	}
	                $OEvent->alarmSend = true;
				} catch (Exception $e) {
					$Oevent->alarmSend = false;
	            	HookHandler::getInstance()->fire("onError", $e->getMessage());
				}
                $this->adapter->addEvent($inverter->id, $OEvent);
            } else {
            	HookHandler::getInstance()->fire("onInfo", $OEvent->event);            	
            }
        }
    }
      
    /**
     * Create the lock
     */
    private function createLock() {
        Util::createLockFile(); // We need this for the bash script!
    }

    /**
     * Remove the lock
     */
    private function dropLock() {
        Util::removeLockFile(); // We need this for the bash script!
    }



}
?>