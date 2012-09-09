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
include($basepath . "/classes/objects/Alarm.php");
include($basepath . "/classes/objects/Live.php");
include($basepath . "/classes/objects/MaxPowerToday.php");

function tricsv($var) {
    return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

$config = new Config();
try {
    $lock = Util::createLockFile(); // Lock port

    // Check if we are in automode and if the sun is down
    if ($AUTOMODE==true && Util::isSunDown($config)) {
        // Remove live files for all inverters
        for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
            if (file_exists(Util::getLiveTXT($invtnum))) {
                unlink(Util::getLiveTXT($invtnum));
            }
        }
        //sleep (60); // he's alive ?
        Util::removeLockFile();
        die;
    }

    for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { //Multi inverters pooling
        include((dirname(dirname(__FILE__))."/config/config_invt$invtnum.php"));

        $aurora = new Aurora($ADR, $PORT, $invtnum, $COMOPTION, $DEBUG);

        $datareturn = $aurora->getData();
        if (trim($datareturn) == "") {
            $tstamp = date("Ymd H:i:s");
            if (Util::isSunDown($config)) {
                /*
                 * instead of continues polling the inverter during the night we give at a 15 minute break
                 * this will greatly reduce the cpu usage and so less power usage
                 */
                echo $tstamp . " : No response and the sun is probably down. Inverter is probably a sleep, waiting for 15 minutes.";
               // sleep(60 * 15);
            } else {
                echo $tstamp . " : No response. Inverter is probably busy or down, waiting for 1 minute";
                //sleep(60);
            }
        }


        $datareturn = str_replace(".", ",", $datareturn);  // Convert dot to comma;
        $array = preg_split("/[[:space:]]+/",$datareturn);
		
        $live = new Live();

        $RET = false;
        if (!empty ($array[22])) {
            $RET = $array[22];
        }
        
        if ($RET=="OK") {
            if (!empty ($array[0])) {
                $live->SDTE = $array[0];
            }
            if (!empty ($array[1])) {
                $live->I1V = $array[1];
            }
            if (!empty ($array[2])) {
                $live->I1A = $array[2];
            }
            if (!empty ($array[3])) {
                $live->I1P = $array[3];
            }
            if (!empty ($array[4])) {
                $live->I2V = $array[4];
            }
            if (!empty ($array[5])) {
                $live->I2A = $array[5];
            }
            if (!empty ($array[6])) {
                $live->I2P = $array[6];
            }
            if (!empty ($array[7])) {
                $live->GV = $array[7];
            }
            if (!empty ($array[8])) {
                $live->GA = $array[8];
            }
            if (!empty ($array[9])) {
                $live->GP = $array[9];
            }
            if (!empty ($array[10])) {
                $live->FRQ = $array[10];
            }
            if (!empty ($array[11])) {
                $live->EFF = $array[11];
            }
            if (!empty ($array[12])) {
                $live->INVT = $array[12];
            }
            if (!empty ($array[13])) {
                $live->BOOT = $array[13];
            }
            if (!empty ($array[19])) {
                $live->KWHT = $array[19];
            }

            // Initialize variables
            $match="";
            $msg="";
            $KWHDtot=0;
            $GPtot=0;

            // We only call functions from the interface, so we can easily switch this to mysql or any other adapter
            $dataAdapter = new PDODataAdapter();

            $dataAdapter->writeLiveInfo($invtnum, $live);

            $currentMPT = $dataAdapter->readMaxPowerToday($invtnum);

            $GP2 = str_replace(",", ".", $live->GP);
            $EFF = str_replace(",", ".", $live->EFF);
            $COEF=($EFF/100)*$CORRECTFACTOR;

            if ($COEF>1) {
                $COEF=1;
            }

            $GP2 = round($GP2*$COEF,2);
            if ($GP2 > $currentMPT->GP) {
                // Found a new max power of today
            	$mpt = new MaxPowerToday();
                $mpt->SDTE = $live->SDTE;
                $mpt->GP = $GP2;
                $dataAdapter->writeMaxPowerToday($invtnum, $mpt);
            }

            
            // TODO :: 2 :: Code below could be removed because we now safe every inverter seperated and not as a SUM of all inverters 
            if ($NUMINV>1) { // Max instant power of the day on multi
                $pmaxotd=file((dirname(dirname(__FILE__))."/data/pmaxotd.txt"));
                $array = explode(";",$pmaxotd[0]);
                $GP2m[$invtnum] = str_replace(",", ".", $live->GP);
                $EFF = str_replace(",", ".", $live->EFF);
                $COEF=($EFF/100)*$CORRECTFACTOR;
                if ($COEF>1) {
                    $COEF=1;
                }
                $GP2m[$invtnum] = round($GP2m[$invtnum]*$COEF,2);

                if (array_sum($GP2m)>$array[1]) {
                    $GP2multi=array_sum($GP2m);
                    
                    $mpt = new MaxPowerToday();
                    $mpt->SDTE = $live->SDTE;
                    $mpt->GP = $GP2multi;
                    $dataAdapter->writeMaxPowerToday($invtnum, $mpt);
                    
                    $myFile=(dirname(dirname(__FILE__))."/data/pmaxotd.txt");
                    $fh = fopen($myFile , 'w+') or die("can't open $myFile file");
                    $stringData=$live->SDTE + ";" + $GP2multi;
                    fwrite($fh, $stringData);
                    fclose($fh);
                }
            }
            // TODO :: 2 :: above code could be remove?!

            // Check alarms
            /*
            $alarmmsg = $aurora->getAlarms();
            if ($alarmmsg !== false && trim($alarmmsg) != "") {
                $alarm = new Alarm();
                $alarm->time = date("Y-m-d H:i");
                $alarm->alarm = str_replace("\n","<br/>",$alarmmsg);
                $dataAdapter->addAlarm($invtnum, $alarm);
            }
            */

            $minute= date("i");
            $minlist = array("00","05","10","15","20","25","30","35","40","45","50","55");

            if (in_array($minute, $minlist) && (!file_exists(Util::getDataDir($invtnum).'/5minflag'))) { // 5 min jobs
                $flag5 = touch(Util::getDataDir($invtnum).'/5minflag'); // Do it once every 5 min

                $live->SDTE = date("Ymd-H:i:s"); // PC time
                $dataAdapter->addHistory($invtnum, $live, date("Ymd"));

                // Dawn startup
                $contalines = $dataAdapter->getHistoryCount($invtnum, date("Ymd"));
                echo "$contalines".$contalines;
                if ( $contalines == 1) {
                    $dir = Util::getDataDir($invtnum).'/csv';
                    $output = scandir($dir);
                    $output = array_filter($output, "tricsv");
                    sort($output);
                    $xdays=count($output);

                    if ($xdays>1){
                        $yesterdaylog=$dir."/".$output[$xdays-2]; //yesterday
                        $lines=file($yesterdaylog);
                        $contalines = count($lines);
                        $array = explode(";",$lines[0]);
                        $prodyesterday = str_replace(",", ".", $array[14]);
                        $array = explode(";",$lines[$contalines-1]);
                        $prodtoday = str_replace(",", ".", $array[14]);
                        if ($prodtoday>=$prodyesterday) {
                            $production=round(($prodtoday-$prodyesterday),3);
                        } else { // passed 100.000kWh
                            $production=round((($prodtoday+100000)-$prodyesterday),3);
                        }

                        $date1 = substr($output[$xdays-2], 0, 8);
                        $year = substr($output[$xdays-2], 0, 4); // For new year

                        $energy = new MaxPowerToday();
                        $energy->SDTE = $date1;
                        $energy->GP = $production;
                        echo("DEBUG::Trying to write energy line: " + $dataAdapter->getMaxPowerTodayCsvString($energy));
                        $dataAdapter->addEnergy($invtnum, $energy, $year);
                    }

                    $dataAdapter->dropMaxPowerToday($invtnum);
                    if ($NUMINV>1) {
                        unlink(dirname(dirname(__FILE__))."/data/pmaxotd.txt");
                    }

                    $now = date("Y-m-d H:i");
                    $file = Util::getDataDir($invtnum) . "/infos/events.txt";
                    $new_lignes = "$now\tInverter awake\n\n";
                    $old_lignes = file(Util::getDataDir($invtnum)."/infos/events.txt");
                    array_unshift($old_lignes,$new_lignes);
                    $new_content = implode('',$old_lignes);
                    $fp = fopen($file,'w');
                    $write = fwrite($fp, $new_content);
                    fclose($fp);

                    // Morning cleanup, purge detailled files
                    if ($KEEPDDAYS!=0 ) {
                        $output = scandir($dir);
                        $output = array_filter($output, "tricsv");
                        sort($output);
                        $contalogs = count($output);
                        if ($contalogs>$KEEPDDAYS) {
                            $i=0;
                            while ($i < $contalogs-$KEEPDDAYS) {
                                unlink (Util::getDataDir($invtnum) . "/csv/" . $output[$i]);
                                $i++;
                            }
                        }
                    }
                    // Clean up logs
                    if ($AMOUNTLOG!=0 ) {
                        $myFile = Util::getDataDir($invtnum) . "/infos/events.txt";
                        $file = file($myFile);
                        $contalines = count($file);
                        if ($contalines >= $AMOUNTLOG) {
                            array_splice($file, $AMOUNTLOG);
                        }
                        $file2 = fopen($myFile, 'w');
                        fwrite($file2, implode('', $file));
                        fclose($file2);
                    } // End of morning clean up
                } // End of dawn startup

                // Wait 60 min after startup to make sure the inverter is fully awake
                if ($contalines==12) {
                    sleep (1);
                    $info = $aurora->getInfo();
                    $myFile = Util::getDataDir($invtnum) . "/infos/infos.txt";
                    $fh = fopen($myFile, 'w+') or die("can't open $myFile file");
                    $stringData="$info";
                    fwrite($fh, $stringData);
                    fclose($fh);
                    if ($SYNC==true) {
                        sleep (2);
                        $info = $aurora->syncTime();
                    }
                }

                // Alarms and warnings
                sleep (1); // take a nap
                $alarm = $aurora->getAlarms();
                $now = date("Y-m-d H:i");
                $myFile = Util::getDataDir($invtnum)."/errors/latest_alarms.txt";
                $fh = fopen($myFile, 'w+') or die("can't open $myFile file");
                $stringData="$now $alarm";
                fwrite($fh, $stringData);
                fclose($fh);

                // Test the alarms file
                $myFile= file(Util::getDataDir($invtnum) . '/errors/latest_alarms.txt');
                foreach ($myFile as $item) {
                    $match=$match.$item;
                    $msg=$msg.$item."\n";
                }

                // Log alarms in events
                if (strstr($match, 'E0')|| strstr($match, 'W0')) {
                	$alarm = new Alarm();
                	$alarm->alarm = $alarm;
                	$alarm->datetime = $now;
                	$dataAdapter->addAlarm($invtnum, $alarm);
                	
                    $file = Util::getDataDir($invtnum) . "/infos/events.txt";
                    $new_lignes = "$now $alarm";
                    $old_lignes = file(Util::getDataDir($invtnum) . "/infos/events.txt");
                    array_unshift($old_lignes,$new_lignes);
                    $new_content = implode('',$old_lignes);
                    $fp = fopen($file,'w');
                    $write = fwrite($fp, $new_content);
                    fclose($fp);
                }


                $FILTER = explode(",",$FILTER);

                foreach($FILTER as $word) // Email filter
                    $match= str_replace($word, "", $match);

                if ($SENDALARMS==true && strstr($match, 'E0')) {
                    mail("$EMAIL", "123Aurora: Inverter #".$invtnum." ERROR", $msg);
                }

                if ($SENDMSGS==true && strstr($match, 'W0')) {
                    mail("$EMAIL", "123Aurora: Inverter #".$invtnum." message", $msg);
                }
            } // End of 5 min jobs

            if (!in_array($minute, $minlist) && file_exists(Util::getDataDir($invtnum) . '/5minflag')) {
                if ($PVOUTPUT==true && $invtnum==$NUMINV) {// PVoutput
                    $myFile=(dirname(dirname(__FILE__)) . "/data/pvoutput_return.txt");
                    $fh = fopen($myFile, 'w+') or die("can't open $myFile file");
                    for ($i=1;$i<=$NUMINV;$i++) {
                        $dir = dirname(dirname(__FILE__))."/data/invt$i/csv";
                        $output = scandir($dir);
                        $output = array_filter($output, "tricsv");
                        sort($output);
                        $xdays=count($output);
                        $lines=file($dir."/".$output[$xdays-1]);
                        $array = explode(";",$lines[0]);
                        $contalines = count($lines);
                        if ($contalines==0)  {
                            $array2=$array;
                        } else { $array2 = explode(";",$lines[$contalines-1]);
                        }
                        $SDTE=$array2[0];
                        $year = substr($SDTE, 0, 4);
                        $month = substr($SDTE, 4, 2);
                        $day = substr($SDTE, 6, 2);
                        $hour = substr($SDTE, 9, 2);
                        $minut = substr($SDTE, 12, 2);
                        $seconde = substr($SDTE, 15, 2);
                        $UTCdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minut.":".$seconde);
                        $now = strtotime(date("Ymd H:i"));
                        if ($now-$UTCdate>300){ // too old
                            $KWHD=0;
                            $GP=0;
                        } else {
                            $KWHT_strt=str_replace(",", ".",$array[14]);
                            $KWHT_stop=str_replace(",", ".",$array2[14]);
                            $KWHD=round((($KWHT_stop-$KWHT_strt)*1000*$CORRECTFACTOR),0); //Wh
                            $GP=round(str_replace(",", ".",$array2[9]),0);
                        }
                        $KWHDtot=$KWHDtot+$KWHD;
                        $GPtot=$GPtot+$GP;
                        $stringData="#$i $KWHD Wh $GP W\n";
                        fwrite($fh, $stringData);
                    } // end of multi
                    $now = date("Ymd");
                    $time= date('H:i',mktime(date("H"), date("i")-1, 0, date("m") , date("d") , date("Y")));
                    $INVT = round(str_replace(",", ".",$INVT),1);
                    $GV = round(str_replace(",", ".",$GV),1);

                    $pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');

                    $stringData="\nSend : $now $time - $KWHDtot Wh $GPtot W $INVT C $GV V\nPVoutput returned: $pvoutput";
                    fwrite($fh, $stringData);
                    fclose($fh);
                } // End of once PVoutput feed
                unlink(Util::getDataDir($invtnum).'/5minflag');
            }
        } else { //NOK

            if (file_exists(Util::getDataDir($invtnum).'/infos/live.txt')) {
                unlink(Util::getDataDir($invtnum).'/infos/live.txt');
            }

            if (!empty($datareturn)) {
                if ($LOGCOM==true) {
                    $now = date("Y-m-d H:i:s");
                    Util::getDataDir($invtnum)."/infos/events.txt";
                    $new_lignes = "$now\tCommunication Error\n\n";
                    $old_lignes = file(Util::getDataDir($invtnum)."/infos/events.txt");
                    array_unshift($old_lignes,$new_lignes);
                    $new_content = implode('',$old_lignes);
                    $fp = fopen($file,'w');
                    $write = fwrite($fp, $new_content);
                    fclose($fp);
                }
                if ($DEBUG==true) {
                    // Comm test errors logs
                    $time= date ("Y-m-d-H:i:s");
                    $myFile = Util::getDataDir($invtnum)."/errors/de".$time.".dat";
                    $fh = fopen($myFile, 'a+') or die("can't open $myFile file");
                    fwrite($fh, $datareturn);
                    fclose($fh);
                    $datareturn = shell_exec('cp '.Util::getDataDir($invtnum).'/errors/de.err '.Util::getDataDir($invtnum).'/errors/de'.$time.'.err');
                }
            }  // End of not empty
        }// End of OK not NOK
    }// End of Multi inverters pooling

  Util::removeLockFile();


} catch (Exception $e) {
    echo($e->getFile() . "(" . $e->getLine() . ") " .  + $e->getMessage() + " TRACE: " . $e->getTraceAsString());
    echo("removing lock!");
    Util::removeLockFile();

    // Try to write error to file
    $myFile = Util::getDataDir($invtnum) . "/errors/error.log";
    $fh = fopen($myFile, 'a+') or die("can't open $myFile file");
    fwrite($fh, $e->getFile() . "(" . $e->getLine() . ") " .  + $e->getMessage() + " TRACE: " . $e->getTraceAsString());
    fclose($fh);


}
?>