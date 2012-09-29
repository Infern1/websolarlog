<?php
class Worker {
    private $config;
    private $adapter;
    private $aurora;

    function __construct() {
        // Initialize objects
        $this->config = new Config;
        $this->adapter = new PDODataAdapter();
        $this->config = $this->adapter->readConfig();
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
        try {
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
            $dataAdapter->dropLock();
            die;
            }
            */
            R::begin(); // Start a transaction to speed things up
            foreach ($this->config->inverters as $inverter) {
                // Handle inverter
                $this->aurora = new Aurora($this->config->aurorapath, $inverter->comAddress, $this->config->comPort, $this->config->comOptions, $this->config->comDebug);

                $datareturn = $this->aurora->getData();

                // Convert datareturn to live object
                $live = AuroraConverter::toLive($datareturn);

                if ($live == null) {
                    // Offline ?
                    $tstamp = date("Ymd H:i:s");
                    if (Util::isSunDown($this->config)) {
                        /*
                         * instead of continues polling the inverter during the night we give at a 15 minute break
                        * this will greatly reduce the cpu usage and so less power usage
                        */
                        echo $tstamp . ": No response and the sun is probably down. Inverter is probably a sleep, waiting for 15 minutes.";
                        sleep(60 * 15);
                    } else {
                        echo $tstamp . ": No valid response. Inverter is probably busy or down, waiting for 30 seconds";
                        sleep(30);
                    }
                } else {
                    $isAlive = true; // The inverter responded
                }

                if ($isAlive) {
                    // TODO :: THIS IS FOR TESTING ONLY, WE DONT WANT TOO LOSE ANY DATA!!!
                    try {
                        $dumpFile = "dumpdata.csv";
                        $fh = fopen($dumpFile, 'a+');
                        fwrite($fh, $datareturn . "\n");
                        fclose($fh);
                    } catch (Exception $e) {
                        // ignore errors
                    }
                    // /TODO

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
                    }


                    // Energy check if every hour, old situation this was every 5 minutes
                    if (PeriodHelper::isPeriodJob("EnergyJob", 60)) {

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
                            $energy->INV = $inverter->id;
                            $energy->KWH = $production;
                            $energy->KWHT = $productionEnd;
                            $this->adapter->addEnergy($inverter->id, $energy);
                        }

                    }
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
        } catch (Exception $e) {
            $error = $e->getFile() . "(" . $e->getLine() . ") " .  + $e->getMessage() + " TRACE: " . $e->getTraceAsString();
            $OEvent = new Event(0, time(), "Script error", $error);
            $this->adapter->addEvent('', $OEvent);

            $this->dropLock();
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
        $GP2 = round($live->GP * $COEF,2);
        if (!isset($currentMPT) || $GP2 > $currentMPT->GP) {
            // Found a new max power of today
            $Ompt = new MaxPowerToday();
            $Ompt->SDTE = $live->SDTE;
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

            // Only save if there is an real event
            if ($this->isAlarmDetected($OEvent)) {
                $OEvent->alarmSend = $this->sendMailAlert($OEvent);
                $this->adapter->addEvent($inverter->id, $OEvent);
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
        $OLock = new Lock();
        $OLock->SDTE = date('Ymd-H:m:s');
        $OLock->type = 'Lock';
        $this->adapter->writeLock($OLock);
        Util::createLockFile(); // We need this for the bash script!
    }

    /**
     * Remove the lock
     */
    private function dropLock() {
        $this->adapter->dropLock();
        Util::removeLockFile(); // We need this for the bash script!
    }



}
?>