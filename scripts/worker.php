<?php
// Credit Louviaux Jean-Marc 2012
error_reporting(E_ALL);

define('checkaccess', TRUE);
//define('AURORA', '/share/martin/aurora/aurora-1.8.0/aurora');

// TODO :: Create autoloader for worker
$basepath = dirname(dirname(__FILE__));
include($basepath . "/config/config_main.php");
include($basepath . "/classes/Util.php");
include($basepath . "/classes/Config.php");
include($basepath . "/classes/Aurora.php");
include($basepath . "/classes/DataAdapter.php");
include($basepath . "/classes/R.php");
include($basepath . "/classes/PDODataAdapter.php");
include($basepath . "/classes/PeriodHelper.php");
include($basepath . "/classes/objects/Event.php");
include($basepath . "/classes/objects/Inverter.php");
include($basepath . "/classes/objects/Panel.php");
include($basepath . "/classes/objects/Live.php");
include($basepath . "/classes/objects/Energy.php");
include($basepath . "/classes/objects/MaxPowerToday.php");
include($basepath . "/classes/objects/Lock.php");
include($basepath . "/classes/converters/auroraConverter.php");

function tricsv($var) {
    return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

/**
 * Check if the live value is higher then the saved value
 * if true, then save it
 * @param Inverter $inverter
 * @param Live $live
 * @param PDODataAdapter $dataAdapter
 */
function checkMaxPowerValue($inverter, $live, $dataAdapter) {
    // Get the highest value off the day
    $currentMPT = $dataAdapter->readMaxPowerToday($inverter->id);
    $COEF=($live->EFF/100)* $inverter->correctionFactor;
    $GP2 = round($live->GP * $COEF,2);
    if (!isset($currentMPT) || $GP2 > $currentMPT->GP) {
        // Found a new max power of today
        $Ompt = new MaxPowerToday();
        $Ompt->SDTE = $live->SDTE;
        $Ompt->GP = $GP2;
        $dataAdapter->writeMaxPowerToday($inverter->id, $Ompt);
    }
}

/**
 * Check if the line is filled with an real alarm
 * @param Event $event
 * @return boolean
 */
function isAlarmDetected($event) {
    $event_text = trim($event['Event']);
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
 * @param Aurora $aurora
 * @param Inverter $inverter
 * @param PDODataAdapter $dataAdapter
 */
function checkAlarms($aurora, $inverter, $dataAdapter, $config) {
    sleep(2);
    $alarm = $aurora->getAlarms();
    if (trim($alarm) != "") {
        //Alarms and warnings to DB
        $OEvent = new Event();
        $OEvent->INV = $inverter->id;
        $OEvent->event = Util::formatEvent($alarm);
        $OEvent->SDTE = date("Y-m-d H:i");
        $OEvent->type = 'Alarm';

        // Only save if there is an real event
        if (isAlarmDetected($OEvent)) {
            $OEvent->alarmSend = sendMailAlert($OEvent, $config);
            $dataAdapter->addEvent($inverter->id, $OEvent);
        }
    }
}

/**
 *
 * @param Event $event
 */
function sendMailAlert($event, $config) {
    $subject = "WSL :: Event message";
    $body = "Hello, \n\n We have detected an error on your inverter.\n\n";
    $body .= $event->event . "\n\n";
    $body .= "Please check if everything is alright.\n\n";
    $body .= "WebSolarLog";

    $result = Common::sendMail($subject, $body, $config);
    if ( $result === true) {
        return true;
    } else {
        return false;
    }
}

/**
 * Create the lock
 * @param PDODataAdapter $dataAdapter
 */
function createLock($dataAdapter) {
    $OLock = new Lock();
    $OLock->SDTE = date('Ymd-H:m:s');
    $OLock->type = 'Lock';
    $dataAdapter->writeLock($OLock);
    Util::createLockFile(); // We need this for the bash script!
}

/**
 * Remove the lock
 * @param PDODataAdapter $dataAdapter
 */
function dropLock($dataAdapter) {
    $dataAdapter->dropLock();
    /*
    $OEvent->event = "removing lock!";
    $OEvent->SDTE = date('Ymd-H:m:s');
    $OEvent->type = 'Locks';
    $dataAdapter->addEvent('', $OEvent);
    */
    util::removeLockFile(); // We need this for the bash script!
}
/*
 * Start main script
*/

$isAlive = false;
try {

    // We only call functions from the interface, so we can easily switch this to mysql or any other adapter
    $dataAdapter = new PDODataAdapter();
    $config = $dataAdapter->readConfig();

    // Create a lock
    createLock($dataAdapter);

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
    foreach ($config->inverters as $inverter) {
        // Handle inverter
        $aurora = new Aurora($config->aurorapath, $inverter->comAddress, $config->comPort, $config->comOptions, $config->comDebug);

        $datareturn = $aurora->getData();
        if ($datareturn == "") {
            // Offline ?
            $tstamp = date("Ymd H:i:s");
            if (Util::isSunDown($config)) {
                /*
                 * instead of continues polling the inverter during the night we give at a 15 minute break
                 * this will greatly reduce the cpu usage and so less power usage
                 */
                echo $tstamp . " : No response and the sun is probably down. Inverter is probably a sleep, waiting for 15 minutes.";
                sleep(60 * 15);
            } else {
                echo $tstamp . " : No response. Inverter is probably busy or down, waiting for 30 seconds";
                sleep(30);
            }
        } else {
            $isAlive = true; // The inverter responded
        }


        if ($isAlive) {
            // TODO :: THIS IS FOR TESTING ONLY, WE DONT WANT TOO LOSE ANY DATA!!!
            $dumpFile = "dumpdata.csv";
            $fh = fopen($dumpFile, 'a+');
            fwrite($fh, $datareturn . "\n");
            fclose($fh);
            // /TODO

            // Convert datareturn to
            $live = auroraConverter::toLive($datareturn);

            // Write the current live value
            $dataAdapter->writeLiveInfo($inverter->id, $live);

            // Check the Max value
            checkMaxPowerValue($inverter, $live, $dataAdapter);

            // Check if there are alarms
            if (PeriodHelper::isPeriodJob("EventJob", 2)) {
                checkAlarms($aurora, $inverter, $dataAdapter, $config);
            }
        }

        /*
         * Do period stuff below
        */

        // History
        if ($isAlive && PeriodHelper::isPeriodJob("HistoryJob", 5)) {
            $dataAdapter->addHistory($inverter->id, $live);

            $arHistory = $dataAdapter->readHistory($inverter->id, null);

            // Fist line means inverter awake
            if(count($arHistory) == 1) {
                // log 'Interver awake' to DB
                $OEvent = new Event();
                $OEvent->INV = $inverter->id;
                $OEvent->event = 'Inverter awake';
                $OEvent->type = 'Notice';
                $OEvent->SDTE = date("Y-m-d H:i");
                $dataAdapter->addEvent($inverter->id, $OEvent);
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
                    $energy->KWHT = $production;
                    $dataAdapter->addEnergy($inverter->id, $energy);
                }

            }
        }

        // Info every 12 hours
        if ($isAlive && PeriodHelper::isPeriodJob("InfoJob", 12 * 60)) {
            sleep(2);
            $info = $aurora->getInfo();
            if (trim($info) != "") {
                // Write InverterInfo (firmware,model,etc) to DB
                $OEvent = new Event();
                $OEvent->INV = $inverter->id;
                $OEvent->SDTE = date("Ymd H:i:s");
                $OEvent->type = 'Info';
                $OEvent->event = $info;
                $dataAdapter->addEvent($inverter->id, $OEvent);
            }

            // Do we want to synchronize the time off the inverter
            if ($inverter->syncTime == true) {
                sleep(2);
                $info = $aurora->syncTime();
            }
        }
    }
    R::commit(); // Commit the transaction

    // Give cpu a little break, this really improves your cpu time
    sleep(2);

    dropLock($dataAdapter);

} catch (Exception $e) {
    $error = $e->getFile() . "(" . $e->getLine() . ") " .  + $e->getMessage() + " TRACE: " . $e->getTraceAsString();
    $OEvent = new Event();
    $OEvent->INV = '';
    $OEvent->event = $error;
    $OEvent->SDTE = date('Ymd-H:m:s');
    $OEvent->type = 'Script error';
    $dataAdapter->addEvent('', $OEvent);

    dropLock($dataAdapter);
}
?>