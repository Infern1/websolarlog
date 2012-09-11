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
include($basepath . "/classes/objects/Event.php");
include($basepath . "/classes/objects/Live.php");
include($basepath . "/classes/objects/MaxPowerToday.php");
include($basepath . "/classes/objects/InverterInfo.php");
include($basepath . "/classes/objects/Lock.php");


function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

$config = new Config();
try {
	
	// We only call functions from the interface, so we can easily switch this to mysql or any other adapter
	$dataAdapter = new PDODataAdapter();
	
	$OLock = new Lock();
	$OLock->SDTE = date('Ymd-H:m:s');
	$OLock->type = 'LockPort';
	$dataAdapter->writeLock($OLock);
	
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
		$dataAdapter->dropLock();
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

		$Olive = new Live();

		$RET = false;
		if (!empty ($array[22])) {
			$RET = $array[22];
		}

		if ($RET=="OK") {
			if (!empty ($array[0])) {
				$Olive->SDTE = $array[0];
			}
			if (!empty ($array[1])) {
				$Olive->I1V = $array[1];
			}
			if (!empty ($array[2])) {
				$Olive->I1A = $array[2];
			}
			if (!empty ($array[3])) {
				$Olive->I1P = $array[3];
			}
			if (!empty ($array[4])) {
				$Olive->I2V = $array[4];
			}
			if (!empty ($array[5])) {
				$Olive->I2A = $array[5];
			}
			if (!empty ($array[6])) {
				$Olive->I2P = $array[6];
			}
			if (!empty ($array[7])) {
				$Olive->GV = $array[7];
			}
			if (!empty ($array[8])) {
				$Olive->GA = $array[8];
			}
			if (!empty ($array[9])) {
				$Olive->GP = $array[9];
			}
			if (!empty ($array[10])) {
				$Olive->FRQ = $array[10];
			}
			if (!empty ($array[11])) {
				$Olive->EFF = $array[11];
			}
			if (!empty ($array[12])) {
				$Olive->INVT = $array[12];
			}
			if (!empty ($array[13])) {
				$Olive->BOOT = $array[13];
			}
			if (!empty ($array[19])) {
				$Olive->KWHT = $array[19];
			}

			// Initialize variables
			$match="";
			$msg="";
			$KWHDtot=0;
			$GPtot=0;

			$dataAdapter->writeLiveInfo($invtnum, $Olive);

			$currentMPT = $dataAdapter->readMaxPowerToday($invtnum);

			$GP2 = str_replace(",", ".", $Olive->GP);
			$EFF = str_replace(",", ".", $Olive->EFF);
			$COEF=($EFF/100)*$CORRECTFACTOR;

			if ($COEF>1) {
				$COEF=1;
			}

			$GP2 = round($GP2*$COEF,2);
			if ($GP2 > $currentMPT->GP) {
				// Found a new max power of today
				$Ompt = new MaxPowerToday();
				$Ompt->SDTE = $Olive->SDTE;
				$Ompt->GP = $GP2;
				$dataAdapter->writeMaxPowerToday($invtnum, $Ompt);
			}


			// TODO :: 2 :: Code below could be removed because we now save every inverter seperated and not as a SUM of all inverters
			if ($NUMINV>1) { // Max instant power of the day on multi
				$pmaxotd=file((dirname(dirname(__FILE__))."/data/pmaxotd.txt"));
				$array = explode(";",$pmaxotd[0]);
				$GP2m[$invtnum] = str_replace(",", ".", $Olive->GP);
				$EFF = str_replace(",", ".", $Olive->EFF);
				$COEF=($EFF/100)*$CORRECTFACTOR;
				if ($COEF>1) {
					$COEF=1;
				}
				$GP2m[$invtnum] = round($GP2m[$invtnum]*$COEF,2);

				if (array_sum($GP2m)>$array[1]) {
					$GP2multi=array_sum($GP2m);

					$myFile=(dirname(dirname(__FILE__))."/data/pmaxotd.txt");
					$fh = fopen($myFile , 'w+') or die("can't open $myFile file");
					$stringData=$Olive->SDTE + ";" + $GP2multi;
					fwrite($fh, $stringData);
					fclose($fh);
					
					$Ompt = new MaxPowerToday();
					$Ompt->SDTE = $Olive->SDTE;
					$Ompt->GP = $GP2multi;
					$dataAdapter->writeMaxPowerToday($invtnum, $Ompt);
				}
			}
			// TODO :: 2 :: above code could be remove?!

			// Check alarms
			/*
			$alarmmsg = $aurora->getAlarms();
			if ($alarmmsg !== false && trim($alarmmsg) != "") {
			$event = new Event();
			$event->time = date("Y-m-d H:i");
			$event->alarm = str_replace("\n","<br/>",$alarmmsg);
			$dataAdapter->addAlarm($invtnum, $event);
			}
			*/
						
			$minute= date("i");
			$minlist = array("00","05","10","15","20","25","30","35","40","45","50","55");

			if (in_array($minute, $minlist) && (!file_exists(Util::getDataDir($invtnum).'/5minflag'))) { // 5 min jobs
				$flag5 = touch(Util::getDataDir($invtnum).'/5minflag'); // Do it once every 5 min

				$Olive->SDTE = date("Ymd-H:i:s"); // PC time
				$dataAdapter->addHistory($invtnum, $Olive, date("Ymd"));
				
				// Dawn startup
				$contalines = $dataAdapter->getHistoryCount($invtnum, date("Ymd"));
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

						$Oenergy = new MaxPowerToday();
						$Oenergy->SDTE = $date1;
						$Oenergy->GP = $production;
						
						/* 
						 * $dataAdapter->getMaxPowerTodayCsvString($energy))   Doesn't exist... 
						 */
						//echo("DEBUG::Trying to write energy line: " + $dataAdapter->getMaxPowerTodayCsvString($energy));
						$dataAdapter->addEnergy($invtnum, $Oenergy, $year);
					}

					$dataAdapter->dropMaxPowerToday($invtnum);
					if ($NUMINV>1) {
						unlink(dirname(dirname(__FILE__))."/data/pmaxotd.txt");
					}

					/*
					 * remove the code below, see DB version below
					*/
					
					// log 'Interver awake' to .txt file
					$now = date("Y-m-d H:i");
					$file = Util::getDataDir($invtnum) . "/infos/events.txt";
					$new_lignes = "$now\tInverter awake\n\n";
					
					$old_lignes = file(Util::getDataDir($invtnum)."/infos/events.txt");
					array_unshift($old_lignes,$new_lignes);
					$new_content = implode('',$old_lignes);
					$fp = fopen($file,'w');
					$write = fwrite($fp, $new_content);
					fclose($fp);

					// log 'Interver awake' to DB					
					$OEvent = new Event();
					$OEvent->INV = $invtnum;
					$OEvent->event = $new_lignes;
					$OEvent->type = 'Inverter Notice';
					$OEvent->SDTE = date("Y-m-d H:i");
					$dataAdapter->addEvent($invtnum, $OEvent);
					
					
					
					/*
					 * TODO :: Why delete data? cleanup not necessary when WSL is running in PDO "mode"? 
					 */
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
					
					/*
					 * TODO :: Why delete data? cleanup not necessary when WSL is running in PDO "mode"?
					*/
					
					
					
					
					
					
				} // End of dawn startup

				// Wait 60 min after startup to make sure the inverter is fully awake
				if ($contalines==12) {
					sleep (1);
					$info = $aurora->getInfo();

					/*
					 * remove the code below, see DB version below
					*/
					
					// Write InverterInfo (firmware,model,etc) to CSV
					$myFile = Util::getDataDir($invtnum) . "/infos/infos.txt";
					$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
					$stringData="$info";
					fwrite($fh, $stringData);
					fclose($fh);
					
					// Write InverterInfo (firmware,model,etc) to DB
					$Oinfo = new InverterInfo();
					$Oinfo->INV = $invtnum;
					$Oinfo->SDTE = date("Ymd H:i:s");
					$Oinfo->info = $info;
					$dataAdapter->writeInverterInfo($invtnum, $Oinfo);
					
					
					if ($SYNC==true) {
						sleep (2);
						$info = $aurora->syncTime();
					}
				}

				/*
				 * remove the code below, see DB version below
				 */
				// Alarms and warnings
				sleep (1); // take a nap
				$alarm = $aurora->getAlarms();
				$now = date("Y-m-d H:i");
				$myFile = Util::getDataDir($invtnum)."/errors/latest_alarms.txt";
				$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
				$stringData="$now $alarm";
				fwrite($fh, $stringData);
				fclose($fh);				
				
				//Alarms and warnings to DB
				$OEvent = new Event();
				$OEvent->INV = $invtnum;
				$OEvent->event = Util::formatEvent($aurora->getAlarms());
				$OEvent->SDTE = date("Y-m-d H:i");
				$OEvent->type = 'Alarm';
				$dataAdapter->addEvent($invtnum, $OEvent);

				
				/*
				 * TODO :: Why log only E(rror) and W(arning) events, if all alarm are logged to the DB??
				 * 
				 * REMOVE "Log alarms in events" CODE BELOW ????
				 */
				// Test the alarms file
				$myFile= file(Util::getDataDir($invtnum) . '/errors/latest_alarms.txt');
				foreach ($myFile as $item) {
					$match=$match.$item;
					$msg=$msg.$item."\n";
				}
				
				// Log alarms in events
				if (strstr($match, 'E0')|| strstr($match, 'W0')) {
					$file = Util::getDataDir($invtnum) . "/infos/events.txt";
					$new_lignes = "$now $alarm";
					$old_lignes = file(Util::getDataDir($invtnum) . "/infos/events.txt");
					array_unshift($old_lignes,$new_lignes);
					$new_content = implode('',$old_lignes);
					$fp = fopen($file,'w');
					$write = fwrite($fp, $new_content);
					fclose($fp);
				}
				/*
 				 * REMOVE "Log alarms in events" CODE ABOVE ????
 				 */

				/*
				 * TODO ::
				 * Move the following MAIL code to UTIL !?, ADD a boolean/Integer to event table,
				 * Let the function check the state of the boolean 
				 * If the Alarm event was not send, let the function send the event to the config-emailadres
				 * let the UTIL::function switch the boolean on mail send
				 */
				$FILTER = explode(",",$FILTER);

				foreach($FILTER as $word) // Email filter
					$match= str_replace($word, "", $match);

				if ($SENDALARMS==true && strstr($match, 'E0')) {
					mail("$EMAIL", "123Aurora: Inverter #".$invtnum." ERROR", $msg);
				}

				if ($SENDMSGS==true && strstr($match, 'W0')) {
					mail("$EMAIL", "123Aurora: Inverter #".$invtnum." message", $msg);
				}
				
				/*
				 * END TODO ::
				 */
				
				
			} // End of 5 min jobs


			if (!in_array($minute, $minlist) && file_exists(Util::getDataDir($invtnum) . '/5minflag')) {
				if ($PVOUTPUT==true && $invtnum==$NUMINV) {// PVoutput
					//$myFile=(dirname(dirname(__FILE__)) . "/data/pvoutput_return.txt");
					//$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
					
					for ($i=1;$i<=$NUMINV;$i++) {
						$historyDay = $dataAdapter->readHistory(1, date('Ymd'));
						$HDFirstLine = reset($historyDay); // HistoryDayFirstLine
						$HDLastLine = end($historyDay);    // HistoryDayLastLine
							
						// get time of first and last record of date...
						$HDFirstDateTime = UTIL::getUTCdate($HDFirstLine['SDTE']);
						$HDLastDateTime = UTIL::getUTCdate($HDLastLine['SDTE']);

						// get current UTC date/time
						$now = strtotime(date("Ymd H:i"));
						
						// check is last line is not older then 300 sec.
						if ($now-$HDLastDateTime>300){ // too old
							// set to 0
							$KWHD=0;
							$GP=0;
						} else {
							// get KWHT first line
							$KWHT_strt=str_replace(",", ".",$HDFirstLine['KWHT']);
							// get KWHT last line line
							$KWHT_stop=str_replace(",", ".",$HDLastLine['KWHT']);
							// calculate KWHD (D of Daily?!) kwhStart - kwhStop = Wh today multiplied by 1000 to get kWh, correted and round by 0 decimals 
							$KWHD=round((($KWHT_stop-$KWHT_strt)*1000*$config->CORRECTFACTOR ),0); //Wh
							// GridPower = GridPower of last line rounded by 0 decimals
							$GP=round(str_replace(",", ".",$HDLastLine['GP']),0);
						}
						// sum the inverter values
						$KWHDtot=$KWHDtot+$KWHD;
						$GPtot=$GPtot+$GP;
						// get the $stringData
						$stringData="#$i $KWHD Wh $GP W\n";
						fwrite($fh, $stringData);
					} // end of multi
					$now = date("Ymd");
					$time= date('H:i',mktime(date("H"), date("i")-1, 0, date("m") , date("d") , date("Y")));
					$INVT = round(str_replace(",", ".",$INVT),1);
					$GV = round(str_replace(",", ".",$GV),1);

					$pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');

					$stringData="\nSend : $now $time - $KWHDtot Wh $GPtot W $INVT C $GV V\nPVoutput returned: $pvoutput";
					//fwrite($fh, $stringData);
					
					$OEvent->INV = $INVT;
					$OEvent->SDTE = date("Ymd-H:m:s");
					$OEvent->type = 'PVoutput returned';
					// TODO :: not sure if $new_content is the right thing to save in DB;
					$OEvent->event = $stringData;
					$dataAdapter->addEvent($invtnum, $event);
					
					//fclose($fh);
				} // End of once PVoutput feed
				unlink(Util::getDataDir($invtnum).'/5minflag');
			}
		} else { //NOK

			if (file_exists(Util::getDataDir($invtnum).'/infos/live.txt')) {
				unlink(Util::getDataDir($invtnum).'/infos/live.txt');
				$dataAdapter->dropLiveInfo($invtnum);
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
					
					//Comm error logs TO DB
					$OEvent->INV = $invtnum;
					$OEvent->SDTE = date("Ymd-H:m:s");
					$OEvent->type = 'Comm error';
					// TODO :: not sure if $new_content is the right thing to save in DB;
					$OEvent->event = $new_content;
					$dataAdapter->addEvent($invtnum, $event);
					
				}
				if ($DEBUG==true) {
					// Comm test errors logs					
					$time= date ("Y-m-d-H:i:s");
					$myFile = Util::getDataDir($invtnum)."/errors/de".$time.".dat";
					$fh = fopen($myFile, 'a+') or die("can't open $myFile file");
					fwrite($fh, $datareturn);
					fclose($fh);
					
					// Comm TEst error logs TO DB
					$OEvent->INV = $invtnum;
					$OEvent->SDTE = date("Ymd-H:m:s");
					$OEvent->event = $datareturn;
					$OEvent->type = 'Debug Comm error??';
					$dataAdapter->addEvent($invtnum, $event);
					
					$datareturn = shell_exec('cp '.Util::getDataDir($invtnum).'/errors/de.err '.Util::getDataDir($invtnum).'/errors/de'.$time.'.err');
				}
			}  // End of not empty
		}// End of OK not NOK
	}// End of Multi inverters pooling

	$dataAdapter->dropLock();
	Util::removeLockFile();


} catch (Exception $e) {
	var_dump($e);
	$error = $e->getFile() . "(" . $e->getLine() . ") " .  + $e->getMessage() + " TRACE: " . $e->getTraceAsString();
	$OEvent = new Event();
	$OEvent->INV = '';
	$OEvent->event = $error;
	$OEvent->SDTE = date('Ymd-H:m:s');
	$OEvent->type = 'Script error';
	$dataAdapter->addEvent('', $OEvent);
	
	$dataAdapter->dropLock();
	$error = "removing lock!";
	$OEvent->event = $error;
	$OEvent->SDTE = date('Ymd-H:m:s');
	$OEvent->type = 'Lock dropped';
	$dataAdapter->addEvent('', $OEvent);
	
	Util::removeLockFile();
	
	
	/*
	 * TODO :: ^^^^^^^^^^Skip the following code, because its already saved in de DB ^^^^^^^^^
	 */
	// Try to write error to file
	$myFile = Util::getDataDir($invtnum) . "/errors/error.log";
	$fh = fopen($myFile, 'a+') or die("can't open $myFile file");
	fwrite($fh, $e->getFile() . "(" . $e->getLine() . ") " .  + $e->getMessage() + " TRACE: " . $e->getTraceAsString());
	fclose($fh);

}
?>