<?php
// Credit Louviaux Jean-Marc 2012
$lock = touch(dirname(dirname(__FILE__)) . "/data/lock"); // Lock port

define('checkaccess', TRUE);
include_once dirname(dirname(__FILE__)) . "/config/pdo_database.class.php"; // optional
require_once dirname(dirname(__FILE__)) . "/config/config_main.php";
require_once dirname(dirname(__FILE__)) . "classes/Formulas.php";


$db = new wArLeY_DBMS("mysql", "127.0.0.1", "solarweblog","user","password","");
/*
 * There is no DB
 *
 * if($db->Cnxn() == false)
 * 		die("Error: Cant connect to database.");
 */

function tricsv($var){
	return !is_dir($var) && preg_match('/.*\.csv/', $var);
}

if ($AUTOMODE == true) {
	$now	  = strtotime(date("Ymd H:i"));
	// get sun information based on LAT/LON
	$sun_info = date_sun_info((strtotime(date("Ymd"))), $LATITUDE, $LONGITUDE);
	if ($now < ($sun_info['sunrise'] - 300) || $now > ($sun_info['sunset'] + 300)) {
		for ($invtnum = 1; $invtnum <= $NUMINV; $invtnum++) {
			$DATADIR = dirname(dirname(__FILE__)) . "/data/invt$invtnum";
			if (file_exists($DATADIR . '/infos/live.txt')) {
				unlink($DATADIR . '/infos/live.txt');
			}
		}
		sleep(60); // he's alive ?
		unlink(dirname(dirname(__FILE__)) . "/data/lock");
		die;
	}
}

for ($invtnum = 1; $invtnum <= $NUMINV; $invtnum++) { //Multi inverters pooling
	include((dirname(dirname(__FILE__)) . "/config/config_invt$invtnum.php"));
	$DATADIR = dirname(dirname(__FILE__)) . "/data/invt$invtnum";

	if ($DEBUG != true) {
		$datareturn = shell_exec('aurora -a' . $ADR . ' -c -T ' . $COMOPTION . ' -d0 -e ' . $PORT);
	} else {
		$datareturn = shell_exec('aurora -b -a' . $ADR . ' -c -T ' . $COMOPTION . ' -d0 -e ' . $PORT . ' 2>' . $DATADIR . '/errors/de.err');
	}

	$array = preg_split("/[[:space:]]+/", $datareturn);
	if (!empty($array[21])) {
		$RET = $array[21];
	} else {
		$RET = false;
	}
	if ($RET == "OK") { //OK (OK, we got a valid response from the inverter)
		if (!empty($array[0])) {$SDTE = $array[0];} else {$SDTE = false;}
		if (!empty($array[1])) {$I1V = str_replace(".", ",", $array[1]);} else {$I1V = false;}
		if (!empty($array[2])) {$I1A = str_replace(".", ",", $array[2]);} else {$I1A = false;}
		if (!empty($array[3])) {$I1P = str_replace(".", ",", $array[3]);} else {$I1P = false;}
		if (!empty($array[4])) {$I2V = str_replace(".", ",", $array[4]);} else {$I2V = false;}
		if (!empty($array[5])) {$I2A = str_replace(".", ",", $array[5]);} else {$I2A = false;}
		if (!empty($array[6])) {$I2P = str_replace(".", ",", $array[6]);} else {$I2P = false;}
		if (!empty($array[7])) {$GV = str_replace(".", ",", $array[7]);} else {$GV = false;}
		if (!empty($array[8])) {$GA = str_replace(".", ",", $array[8]);} else {$GA = false;}
		if (!empty($array[9])) {$GP = str_replace(".", ",", $array[9]);} else {$GP = false;}
		if (!empty($array[10])) {$FRQ = str_replace(".", ",", $array[10]);} else {$FRQ = false;}
		if (!empty($array[11])) {$EFF = str_replace(".", ",", $array[11]);} else {$EFF = false;}
		if (!empty($array[12])) {$INVT = str_replace(".", ",", $array[12]);} else {$INVT = false;}
		if (!empty($array[13])) {$BOOT = str_replace(".", ",", $array[13]);} else {$BOOT = false;}
		if (!empty($array[19])) {$KWHT = str_replace(".", ",", $array[19]);} else {$KWHT = false;}
		if ($CALCGRIDP == true) {$array[9] = $array[7] * $array[8];}
		// Initialize variables
		$match   = "";
		$msg	 = "";
		$KWHDtot = 0;
		$GPtot   = 0;

		$stringData = "$SDTE;$I1V;$I1A;$I1P;$I2V;$I2A;$I2P;$GV;$GA;$GP;$FRQ;$EFF;$INVT;$BOOT;$KWHT";
		$myFile	 = $DATADIR . "/infos/live.txt"; // Live
		$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
		fwrite($fh, $stringData);
		fclose($fh);

		$pmaxotd = file($DATADIR . "/infos/pmaxotd.txt"); // Max instant power of the day
		$array   = preg_split("/;/", $pmaxotd[0]);
		$GP2	 = str_replace(",", ".", $GP);
		$EFF	 = str_replace(",", ".", $EFF);
		$COEF	= ($EFF / 100) * $CORRECTFACTOR;

		// GridPower2 = GridPower2 * COrrectedEFeciency, round by 2 decimals
		// ^ GridPower in Watt
		$GP2 = Formulas::calcPowerEfficency($GP2, $COEF);

		if ($GP2 > $array[1]) {
			$myFile = $DATADIR . "/infos/pmaxotd.txt";
			$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
			$stringData = "$SDTE;$GP2";
			fwrite($fh, $stringData);
			fclose($fh);
		}

		if ($NUMINV > 1) { // Max instant power of the day on multi
			$pmaxotd		= file((dirname(dirname(__FILE__)) . "/data/pmaxotd.txt"));
			$array		  = preg_split("/;/", $pmaxotd[0]);
			$GP2m[$invtnum] = str_replace(",", ".", $GP);
			$EFF			= str_replace(",", ".", $EFF);
			$COEF		   = ($EFF / 100) * $CORRECTFACTOR;
			// GridPower2max[inverternumber] = $GridPower2max * COrrectedEFeciency, round by 2 decimals
			// ^ Max. grid power in Watt
			$GP2m[$invtnum] = Formulas::calcPowerEfficency($GP2m[$invtnum], $COEF);

			if (array_sum($GP2m) > $array[1]) {
				$GP2multi = array_sum($GP2m);
				$myFile   = (dirname(dirname(__FILE__)) . "/data/pmaxotd.txt");
				$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
				$stringData = "$SDTE;$GP2multi;";
				fwrite($fh, $stringData);
				fclose($fh);
			}
		}

		$minute  = date("i");

		/*
		 * Create a 5 min. interval array for PVoutput
		 */
		$count = 00;
		$interval = 5; // <<< bring this value to admin
		while($count<=59){
			if ($count < 10){$countstring = sprintf("%02d",$count);}else{$countstring = (string)$count;}
			$minpvoutputlist[$count]=$countstring;
			$count = $count + $interval;
		}


		/*
		 * Create a X min. interval array for log frequency.
		 */
		$count = 00;
		$interval = 2; // <<< bring this value to admin, INT not lower than 1 because it will loop.
		while($count<=59){
			if ($count < 10){$countstring = sprintf("%02d",$count);}else{$countstring = (string)$count;}
			$mindatalist[$count]=$countstring;
			$count = $count + $interval;
		}

		if (in_array($minute, $mindatalist) && (!file_exists($DATADIR . '/dataflag'))) { // X min jobs
			$flagX = touch($DATADIR . '/dataflag'); // Do it once every X min
			$today = date("Ymd");
			$now   = date("Ymd-H:i:s"); // Server time

			$stringData = "$now;$I1V;$I1A;$I1P;$I2V;$I2A;$I2P;$GV;$GA;$GP;$FRQ;$EFF;$INVT;$BOOT;$KWHT\n";
			$myFile	 = $DATADIR . "/csv/" . $today . ".csv";
			$fh = fopen($myFile, 'a+') or die("can't open $myFile file");
			fwrite($fh, $stringData);
			fclose($fh);

			/*
			 * db add by Freemann
			 */
			$params = array(
				":mpp1Voltage|" . str_replace(",", ".", $I1V) . "|STR",
				":mpp1Current|" . str_replace(",", ".", $I1A) . "|STR",
				":mpp1Power|" . str_replace(",", ".", $I1P) . "|STR",
				":mpp2Voltage|" . str_replace(",", ".", $I2V) . "|STR",
				":mpp2Current|" . str_replace(",", ".", $I2A) . "|STR",
				":mpp2Power|" . str_replace(",", ".", $I2P) . "|STR",
				":gridVoltage|" . str_replace(",", ".", $GV) . "|STR",
				":gridCurrent|" . str_replace(",", ".", $GA) . "|STR",
				":gridPower|" . str_replace(",", ".", $GP) . "|INT",
				":gridFrequency|" . str_replace(",", ".", $FRQ) . "|STR",
				":invEfficiency|" . str_replace(",", ".", $EFF) . "|STR",
				":invTemp|" . str_replace(",", ".", $INVT) . "|STR",
				":boosTemp|" . str_replace(",", ".", $BOOT) . "|STR",
				":kwht|" . str_replace(",", ".", $KWHT) . "|STR",
				":azimuth|" . round($azimuth, 2) . "|STR",
				":altitude|" . round($altitude, 2) . "|STR"
			);

			$db->query_secure("INSERT INTO `pvlog`.`live` (`datetime`,`mpp1Voltage`, `mpp1Current`, `mpp1Power`, `mpp2Voltage`, `mpp2Current`, `mpp2Power`, `gridVoltage`, `gridCurrent`, `gridPower`, `GridFrequency`, `invEfficiency`, `invTemp`, `BoosTemp`, `kwht`,`azimuth`,`altitude`)  VALUES  (now(),:mpp1Voltage,:mpp1Current,:mpp1Power,:mpp2Voltage,:mpp2Current,:mpp2Power,:gridVoltage,:gridCurrent,:gridPower,:GridFrequency,:invEfficiency,:invTemp,:BoosTemp,:kwht,:azimuth,:altitude);", $params, false, false, "|");
			/*
			 *  /db add by Freemann
			 */

			$lines	  = file($myFile);
			$contalines = count($lines);

			// Dawn startup
			if ($contalines == 1) {
				$dir	= $DATADIR . '/csv';
				$output = scandir($dir);
				$output = array_filter($output, "tricsv");
				sort($output);
				$xdays = count($output);

				if ($xdays > 1) {
					$yesterdaylog  = $dir . "/" . $output[$xdays - 2]; //yesterday
					$lines		 = file($yesterdaylog);
					$contalines	= count($lines);
					$array		 = preg_split("/;/", $lines[0]);
					$prodyesterday = str_replace(",", ".", $array[14]);
					$array		 = preg_split("/;/", $lines[$contalines - 1]);
					$prodtoday	 = str_replace(",", ".", $array[14]);
					if ($prodtoday >= $prodyesterday) {
						$production = round(($prodtoday - $prodyesterday), 3);
					} else { // passed 100.000kWh
						$production = round((($prodtoday + 100000) - $prodyesterday), 3);
					}
					$production = str_replace(".", ",", $production);
					$date1	  = substr($output[$xdays - 2], 0, 8);
					$year	   = substr($output[$xdays - 2], 0, 4); // For new year
					$stringData = "$date1;$production\n";
					$myFile	 = $DATADIR . "/production/energy" . $year . ".csv";
					$fh = fopen($myFile, 'a+') or die("can't open $myFile file");
					fwrite($fh, $stringData);
					fclose($fh);

					/*
			 		* db add by Freemann
				 	*/
					$params = array(
						":kwh|" . str_replace(",", ".", $production) . "|STR",
						":yesterday|" . $date1 . "|STR",
					);
					$db->query_secure("INSERT INTO `pvlog`.`production` (`datetime`,`kwh`) VALUES (:yesterday,:kwh);", $params, false, false, "|");
					/*
			 		*   /db add by Freemann
			 		*/
				}

				unlink($DATADIR . "/infos/pmaxotd.txt"); // Remove past pmotd
				if ($NUMINV > 1) {
					unlink(dirname(dirname(__FILE__)) . "/data/pmaxotd.txt");
				}

				$now		= date("Y-m-d H:i");
				$file	   = $DATADIR . "/infos/events.txt";
				$new_lignes = "$now\tInverter awake\n\n";
				$old_lignes = file($DATADIR . "/infos/events.txt");
				array_unshift($old_lignes, $new_lignes);
				$new_content = implode('', $old_lignes);
				$fp		  = fopen($file, 'w');
				$write	   = fwrite($fp, $new_content);
				fclose($fp);

				// Morning cleanup, purge detailled files
				if ($KEEPDDAYS != 0) {
					$output = scandir($dir);
					$output = array_filter($output, "tricsv");
					sort($output);
					$contalogs = count($output);
					if ($contalogs > $KEEPDDAYS) {
						$i = 0;
						while ($i < $contalogs - $KEEPDDAYS) {
							unlink($DATADIR . "/csv/" . $output[$i]);
							$i++;
						}
					}
				}
				// Clean up logs
				if ($AMOUNTLOG != 0) {
					$myFile	 = $DATADIR . "/infos/events.txt";
					$file	   = file($myFile);
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
			if ($contalines == 12) {
				sleep(1);
				$info   = shell_exec('aurora -a' . $ADR . ' -p -n -f -g -m -v ' . $PORT); // Generate the info file
				$myFile = $DATADIR . "/infos/infos.txt";
				$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
				$stringData = "$info";
				fwrite($fh, $stringData);
				fclose($fh);
				if ($SYNC == true) {
					sleep(2);
					$info = shell_exec('aurora -a' . $ADR . ' -L ' . $PORT); //Sync time
				}
			}

			// Alarms and warnings
			sleep(1); // take a nap
			$alarm  = shell_exec('aurora -a ' . $ADR . ' -A ' . $PORT);
			$now	= date("Y-m-d H:i");
			$myFile = $DATADIR . "/errors/latest_alarms.txt";
			$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
			$stringData = "$now $alarm";
			fwrite($fh, $stringData);
			fclose($fh);

			// Test the alarms file
			$myFile = file($DATADIR . '/errors/latest_alarms.txt');
			foreach ($myFile as $item) {
				$match = $match . $item;
				$msg   = $msg . $item . "\n";
			}

			// Log alarms in events
			if (strstr($match, 'E0') || strstr($match, 'W0')) {
				$file	   = $DATADIR . "/infos/events.txt";
				$new_lignes = "$now $alarm";
				$old_lignes = file($DATADIR . "/infos/events.txt");
				array_unshift($old_lignes, $new_lignes);
				$new_content = implode('', $old_lignes);
				$fp		  = fopen($file, 'w');
				$write	   = fwrite($fp, $new_content);
				fclose($fp);
			}
			$FILTER = preg_split("/,/", $FILTER);

			foreach ($FILTER as $word) // Email filter
				$match = str_replace($word, "", $match);

			if ($SENDALARMS == true && strstr($match, 'E0')) {
				mail("$EMAIL", "123Aurora: Inverter #" . $invtnum . " ERROR", $msg);
			}

			if ($SENDMSGS == true && strstr($match, 'W0')) {
				mail("$EMAIL", "123Aurora: Inverter #" . $invtnum . " message", $msg);
			}

		} // End of X min jobs

		// als
		if (!in_array($minute, $mindatalist) && file_exists($DATADIR . '/dataflag')) {
			unlink($DATADIR . '/dataflag');
		}

		// Set 5 min PVoutput flag
		if (in_array($minute, $minpvoutputlist) && (!file_exists($DATADIR . '/pvoutputflag'))) { // 5 min jobs
			$flagX = touch($DATADIR . '/pvoutputflag'); // Do it once every 5 min
		}

		// start 5 min PVoutput flag
		if (!in_array($minute, $minpvoutputlist) && file_exists($DATADIR . '/pvoutputflag')) {
			if ($PVOUTPUT == true && $invtnum == $NUMINV) { // PVoutput
				$myFile = (dirname(dirname(__FILE__)) . "/data/pvoutput_return.txt");
				$fh = fopen($myFile, 'w+') or die("can't open $myFile file");
				for ($i = 1; $i <= $NUMINV; $i++) {
					$dir	= dirname(dirname(__FILE__)) . "/data/invt$i/csv";
					$output = scandir($dir);
					$output = array_filter($output, "tricsv");
					sort($output);
					$xdays	  = count($output);
					$lines	  = file($dir . "/" . $output[$xdays - 1]);
					$array	  = preg_split("/;/", $lines[0]);
					$contalines = count($lines);
					if ($contalines == 0) {
						$array2 = $array;
					} else {
						$array2 = preg_split("/;/", $lines[$contalines - 1]);
					}
					$SDTE	= $array2[0];
					$year	= substr($SDTE, 0, 4);
					$month   = substr($SDTE, 4, 2);
					$day	 = substr($SDTE, 6, 2);
					$hour	= substr($SDTE, 9, 2);
					$minut   = substr($SDTE, 12, 2);
					$seconde = substr($SDTE, 15, 2);
					$UTCdate = strtotime($year . "-" . $month . "-" . $day . " " . $hour . ":" . $minut . ":" . $seconde);
					$now	 = strtotime(date("Ymd H:i"));
					if ($now - $UTCdate > 300) { // too old
						$KWHD = 0;
						$GP   = 0;
					} else {
						$KWHT_strt = str_replace(",", ".", $array[14]);
						$KWHT_stop = str_replace(",", ".", $array2[14]);
						$KWHD = Formulas::calcKiloWattHourDay($KWHT_strt, $KWHT_stop, $CORRECTFACTOR);
						$GP		= round(str_replace(",", ".", $array2[9]), 0);
					}
					$KWHDtot	= $KWHDtot + $KWHD;
					$GPtot	  = $GPtot + $GP;
					$stringData = "#$i $KWHD Wh $GP W\n";
					fwrite($fh, $stringData);
				} // end of multi
				$now  = date("Ymd");
				$time = date('H:i', mktime(date("H"), date("i") - 1, 0, date("m"), date("d"), date("Y")));
				$INVT = round(str_replace(",", ".", $INVT), 1);
				$GV   = round(str_replace(",", ".", $GV), 1);

				$pvoutput = shell_exec('curl -d "d=' . $now . '" -d "t=' . $time . '" -d "c1=0" -d "v1=' . $KWHDtot . '" -d "v2=' . $GPtot . '" -d "v5=' . $INVT . '" -d "v6=' . $GV . '" -H "X-Pvoutput-Apikey: ' . $APIKEY . '" -H "X-Pvoutput-SystemId: ' . $SYSID . '" http://pvoutput.org/service/r2/addstatus.jsp &');

				$stringData = "\nSend : $now $time - $KWHDtot Wh $GPtot W $INVT C $GV V\nPVoutput returned: $pvoutput";
				fwrite($fh, $stringData);
				fclose($fh);
			} // End of once PVoutput feed
			unlink($DATADIR . '/pvoutputflag');
		}
	} else { //NOK (Not OK, we didn't get a valid response from the inverter)
		if (file_exists($DATADIR . '/infos/live.txt')) {
			unlink($DATADIR . '/infos/live.txt');
		}
		if (!empty($datareturn)) {
			if ($LOGCOM == true) {
				$now = date("Y-m-d H:i:s");
				$DATADIR . "/infos/events.txt";
				$new_lignes = "$now\tCommunication Error\n\n";
				$old_lignes = file($DATADIR . "/infos/events.txt");
				array_unshift($old_lignes, $new_lignes);
				$new_content = implode('', $old_lignes);
				$fp		  = fopen($file, 'w');
				$write	   = fwrite($fp, $new_content);
				fclose($fp);
			}
			if ($DEBUG == true) {
				// Comm test errors logs
				$time   = date("Y-m-d-H:i:s");
				$myFile = $DATADIR . "/errors/de" . $time . ".dat";
				$fh = fopen($myFile, 'a+') or die("can't open $myFile file");
				fwrite($fh, $datareturn);
				fclose($fh);
				$datareturn = shell_exec('cp ' . $DATADIR . '/errors/de.err ' . $DATADIR . '/errors/de' . $time . '.err');
			}
		} // End of not empty
	} // End of OK not NOK
} // End of Multi inverters pooling
unlink(dirname(dirname(__FILE__)) . "/data/lock"); // Remove Lock
?>