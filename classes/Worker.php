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

        // Check if we are in automode and if the sun is down
        // AUTOMODE do we still need it?
        /*
        if ($AUTOMODE==true && Util::isSunDown($config)) {
        // Remove live files for all inverters
        for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
        if (file_exists(Util::getLiveTXT($invtnum))) {
        unlink(Util::getLiveTXT($invtnum));
        }
        }
        //sleep (60); // he's alive ?
        die;
        }
        */
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

            if ($live == null) {
                // Offline ?
                if (Util::isSunDown($this->config)) {
                	// Fire an shutDown hook once a day (20 hours)
                	if (PeriodHelper::isPeriodJob("ShutDownJobINV" . $inverter->id, (20 * 60))) {
                		HookHandler::getInstance()->fire("onInverterShutdown", $inveter);                		
                	}
                	
                    /*
                     * instead of continues polling the inverter during the night we give at a 15 minute break
                    * this will greatly reduce the cpu usage and so less power usage
                    */
                	HookHandler::getInstance()->fire("onDebug", "No response and the sun is probably down. Inverter is probably a sleep, waiting for 15 minutes.");
                    sleep(60);
                } else {
                	HookHandler::getInstance()->fire("onDebug", "No valid response. Inverter is probably busy or down, waiting for 30 seconds.");
                    sleep(30);
                }
            } else {
                $isAlive = true; // The inverter responded
            }

            if ($isAlive) {
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
                if(count($arHistory) == 1) {
                    // log 'Interver awake' to DB
                    $OEvent = new Event($inverter->id, time(), 'Notice', 'Inverter awake');
                    $this->adapter->addEvent($inverter->id, $OEvent);
                    HookHandler::getInstance()->fire("onInverterStartup", $OEvent->event);
                }


                // Energy check if every hour, old situation this was every 5 minutes
                if (PeriodHelper::isPeriodJob("EnergyJob", 5)) {

                    // The first hour we dont get much kwh, so wait for at least ten history lines
                    if (count($arHistory) > 10) {
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

        // Give cpu a little break, this really improves your cpu time
        sleep(2);

        // free the aurora object
        $this->aurora = null;

        $this->dropLock();

        R::commit(); // Commit the transaction
        
        // This will also run if the inverter is down
        if (PeriodHelper::isPeriodJob("10minJob", 2)) {
        	HookHandler::getInstance()->fire("on10minJob");
        }

        $inactiveCheck = new InactiveCheck();
        $inactiveCheck->check();
        
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
            $parts = explode(":", $line);
            if (trim($parts[1]) != "No Alarm") {
                $alarmFound = true;
                break;
            }
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
		            	HookHandler::getInstance()->fire("onInverterWarning", $OEvent->event);
	            	}
	            	if (strpos($OEvent->event, 'Error') !== false ) {
		            	HookHandler::getInstance()->fire("onInveterError", $OEvent->event);
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
     * Send an email alert
     * @param Event $event
     */
    private function sendMailAlert($event) {
    	$subject = "WSL :: Event message";
    	$body = "Hello, \n\n We have detected an error on your inverter.\n\n";
    	$body .= $event->event . "\n\n";
    	$body .= "Please check if everything is alright.\n\n";
    	$body .= "WebSolarLog";
    
    	$result = Common::sendMail($subject, $body, $this->config);
    	if ( $result === true) {
    		return true;
    	} else {
    		return false;
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