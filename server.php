<?php
// For debugging only
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('checkaccess', TRUE);
include("config/config_main.php");

require 'classes/classloader.php';

// Retrieve action params
$method = $_GET['method'];

// Detect en load the current user_lang
if (!empty ($_POST['user_lang'])) {
	setcookie('user_lang',$_POST['user_lang'],strtotime('+5 year'));
	$user_lang=$_POST['user_lang'];
} elseif (isset($_COOKIE['user_lang'])){
	$user_lang=$_COOKIE['user_lang'];
} else {
	$user_lang="English";
}
include("languages/".$user_lang.".php");

// Detect the current user style
if (!empty ($_POST['user_style'])) {
	setcookie('user_style',$_POST['user_style'],strtotime('+5 year'));
	$user_style=$_POST['user_style'];
} elseif (isset($_COOKIE['user_style'])){
	$user_style=$_COOKIE['user_style'];
} else {
	$user_style="default";
}

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// Initialize return array
$data = array();
$invtnum = Common::getValue('invtnum', 0);

$dataAdapter = new CsvDataAdapter();

switch ($method) {
	case 'getLanguages':
		$languages = array();
		if ($handle = opendir('languages')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." ) {
					$languages[] = str_replace(".php", "", $entry);
				}
			}
		}
		$data['languages'] = $languages;
		$data['currentlanguage'] = $user_lang;
		break;
	case 'getMenu':
		// TODO :: Move to json file or something???
		$menu = array();
		$menu[] = array( "url" => "index.php", "title" => $lgMINDEX);
		$menu[] = array( "url" => "indexdetailed.php", "title" => $lgMDETAILED);
		$menu[] = array( "url" => "indexproduction.php", "title" => $lgMPRODUCTION);
		$menu[] = array( "url" => "indexcomparison.php", "title" => $lgMCOMPARISON);
		$menu[] = array( "url" => "plantinfo.php", "title" => $lgMINFO);
		$menu[] = array( "url" => "index_chartstest.php", "title" => "Test page");
		$data['menu'] = $menu;
		break;
	case 'getEvents':
		$data['events'] = $dataAdapter->readAlarm($invtnum);
		break;
	case 'getLiveData':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");
		$live = $dataAdapter->readLiveInfo($invtnum);
		$UTCdate = strtotime(substr($live->SDTE, 0, 4)."-".substr($live->SDTE, 4, 2)."-".substr($live->SDTE, 6, 2)." ".substr($live->SDTE, 9, 2).":".substr($live->SDTE, 12, 2).":".substr($live->SDTE, 15, 2));

		/*
		 * $array[11] = by inverter reported efficiency;
		* $CORRECTFACTOR = var set in int_config for correcting reported efficiency;
		* $COEF = COrrected EFficiency;
		*/
		$COEF=($live->EFF/100)*$CORRECTFACTOR;
		$COEF=($COEF > 1) ? 1 : $COEF;
		$gp = $live->GP * $COEF;
		$gp = ($gp > 1000) ? round($gp,0) : $gp= round($gp,9) ;

		// Fill the live data
		$liveData = new LiveDataResult();
		$liveData->setMppOne(floatval(round($live->I1V,2)), floatval(round($live->I1A,2)), floatval(round($live->I1P,2)));
		$liveData->setMppTwo(floatval(round($live->I2V,2)), floatval(round($live->I2A,2)), floatval(round($live->I2P,2)));
		$liveData->setGrid(floatval(round($live->GV,2)), floatval(round($live->GA,2)), floatval($gp));
		$liveData->valueSDTE = $UTCdate*1000;
		$liveData->valueFRQ = floatval(round($live->FRQ,2));
		$liveData->valueEFF = floatval(round($live->EFF,2));
		$liveData->valueINVT = floatval(round($live->INVT,1));
		$liveData->valueBOOT = floatval(round($live->BOOT,1));
		$liveData->valueKWHT = floatval($live->KWHT);

		// Set the Power Max Today
		$mpt = $dataAdapter->readMaxPowerToday($invtnum);
		$liveData->valuePMAXOTD = floatval(round($mpt->GP,0));
		$liveData->valuePMAXOTDTIME = (substr($mpt->SDTE, 9, 2).":".substr($mpt->SDTE, 12, 2));

		// Set some translations
		$liveData->lgDASHBOARD = $lgDASHBOARD;
		$liveData->lgPMAX = $lgPMAX;


		$liveData->success = true;

		$data['liveData'] = $liveData;
		break;
	case 'getPlantInfo':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");
		$dir = "data/invt$invtnum/csv";
		$di = new DirectoryIterator($dir);
		foreach ($di as $fileinfo) {
			if (!$fileinfo->isDot()) {
				if ($fileinfo->getMTime() > $timestamp) {
					// current file has been modified more recently
					// than any other file we've checked until now
					$path = $dir."/" . $fileinfo->getFilename();
				}
			}
		}

		$lines=file($path);
		$contalines = count($lines);
		$array = explode(";",$lines[$contalines-1]);
		/*
		 * $KWHP = corrected total kWh Production
		* $array[14] = by inverter reported total production
		* $INITIALCOUNT = is > 0 in config_invt1.php if
		* $CORRECTFACTOR = the factor to correct with
		*/
		$KWHP=round(($array[14]+$INITIALCOUNT)*$CORRECTFACTOR,1);
		/*
		 * calculate avoided CO2 in grams
		* 456 = grams CO2 per kWh >> move to a var. in admin???
		* CO2 = ((total produced kWh / 1000) * "energy mix" grams CO2 per produced kWh)   in "CountryXXX" (NL = 630 grams per produced kWh)
		*/
		$CO2=(($KWHP/1000)*456);
		if ($CO2>1000) {
			$CO2v="Tonnes";
			$CO2=round(($CO2/1000),3);
		}else {
			$CO2v="Kg";
			$CO2=round(($CO2),1);
		}
		$info="data/invt$invtnum/infos/infos.txt";
		$lines=file($info);
		$updtd=date ("d M H:i.", filemtime($info));
		$reKey = 0;
		foreach ($lines as $key => $value){
			if($value != "\n") {
				$inverter[$reKey]  = $value;
				$reKey++;
			}
		}
		$filename="data/invt$invtnum/infos/events.txt";
		$handle = fopen($filename, "r");
		$contents = explode("\n", fread($handle, filesize($filename)));
		fclose($handle);

		$plantInfo = new PlantInfoResult();
		$plantInfo->langEVENTS = $lgEVENTS;
		$plantInfo->langINVERTERINFO = $lgINVERTERINFO;
		$plantInfo->langTOTALPROD = $lgTOTALPROD;
		$plantInfo->langECOLOGICALINFOB = $lgECOLOGICALINFOB;
		$plantInfo->langPLANTINFO = $lgPLANTINFO;
		$plantInfo->langLOCATION = $lgLOCATION;
		$plantInfo->langCOUNTER = $lgCOUNTER;
		$plantInfo->langPLANTPOWER = $lgPLANTPOWER;

		$plantInfo->valueSYSID = $SYSID;
		$plantInfo->valuePLANT_POWER = $PLANT_POWER;
		$plantInfo->valueLOCATION = $LOCATION;
		$plantInfo->valueCO2 = $CO2;
		$plantInfo->valueCO2v =$CO2v;
		$plantInfo->valueKWHP = $KWHP;
		$plantInfo->valueUpdtd = $updtd;
		$plantInfo->valueEvents = $contents;
		$plantInfo->valueInverter = $inverter;

		$plantInfo->success = true;

		$data['plantInfo'] = $plantInfo;
		break;

	case 'getTodayValues':
		// get the date of today.
		$CSVdate = date("Ymd",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		// get the CSV of today
		$lines=file("data/invt".$invtnum."/csv/".$CSVdate.".csv");

		$points = array();

		$oldTime = 0;
		$oldKWHT = 0;

		foreach ($lines as $line) {
			$line = str_replace("\n", "", $line); // remove line endings
			$line = str_replace(",", ".", $line); // Convert comma to dot
			$fields = explode(";", $line); // Create an array of fields

			// 20120623-05:16:00
			$rawdatetime = explode('-', $fields[0]);
			$year = substr($rawdatetime[0], 0, 4);
			$month = substr($rawdatetime[0], 4, 2);
			$day = substr($rawdatetime[0], 6, 2);
			$hour = substr($rawdatetime[1], 0, 2);
			$minute = substr($rawdatetime[1], 3, 2);
			$second = substr($rawdatetime[1], 6, 2);
			$kwht = $fields[14];

			// Convert to epoch date
			$UTCdate =  strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$second);

			// Check time difference
			$diffTime= $UTCdate - $oldTime;
			if ($diffTime!=0) {
				$AvgPOW = Formulas::calcAveragePower($oldKWHT, $kwht, $diffTime);
			} else {
				$AvgPOW=0;
			}

			// Only add Points if the value changed
			if ($kwht - $oldKWHT != 0) {
				$points[] = array($year."-".$month."-".$day." ".$hour.":".$minute , $AvgPOW);
			}

			$oldTime = $UTCdate;
			$oldKWHT = $kwht;

		}

		$points[] = array(0,count($lines));

		// Calculate total kwht
		$firstFields = explode(';', str_replace(",", ".", $lines[0]));
		$lastFields = explode(';', str_replace(",", ".", $lines[count($lines) - 1]));

		$data = new TodayValuesResult();
		$data->label ='Gem. Vermogen (W)';
		$data->kwht = round($lastFields[14] - $firstFields[14], 2);
		$data->file = "data/invt".$invtnum."/csv".$CSVdate.".csv";
		$data->data = $points;
		break;

	case 'getYesterdayValues':
		// get the date of yesterday.
		$CSVdate = date("Ymd",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));
		// get the CSV of yesterday
		$lines=file("data/invt".$invtnum."/csv/".$CSVdate.".csv");
		
		$points = array();

		$oldTime = 0;
		$oldKWHT = 0;

		foreach ($lines as $line) {
			$line = str_replace("\n", "", $line); // remove line endings
			$line = str_replace(",", ".", $line); // Convert comma to dot
			$fields = explode(";", $line); // Create an array of fields

			// 20120623-05:16:00
			$rawdatetime = explode('-', $fields[0]);
			$year = substr($rawdatetime[0], 0, 4);
			$month = substr($rawdatetime[0], 4, 2);
			$day = substr($rawdatetime[0], 6, 2);
			$hour = substr($rawdatetime[1], 0, 2);
			$minute = substr($rawdatetime[1], 3, 2);
			$second = substr($rawdatetime[1], 6, 2);
			$kwht = $fields[14];

			// Convert to epoch date
			$UTCdate =  strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$second);

			// Check time difference
			$diffTime= $UTCdate - $oldTime;
			if ($diffTime!=0) {
				$AvgPOW = Formulas::calcAveragePower($oldKWHT, $kwht, $diffTime);
			} else {
				$AvgPOW=0;
			}

			// Only add Points if the value changed
			if ($kwht - $oldKWHT != 0) {
				$points[] = array($year."-".$month."-".$day." ".$hour.":".$minute , $AvgPOW);
			}

			$oldTime = $UTCdate;
			$oldKWHT = $kwht;

		}

		$points[] = array(0,count($lines));

		// Calculate total kwht
		$firstFields = explode(';', str_replace(",", ".", $lines[0]));
		$lastFields = explode(';', str_replace(",", ".", $lines[count($lines) - 1]));

		$data = new YesterdayValuesResult();
		$data->label ='Gem. Vermogen (W)';
		$data->kwht = round($lastFields[14] - $firstFields[14], 2);
		$data->file = "data/invt".$invtnum."/csv".$CSVdate.".csv";
		$data->data = $points;
		break;
	case 'getLastDaysValues':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");
		$dir = 'data/invt'.$invtnum.'/production/';
		$output = scandir($dir);
		$output = array_filter($output, "tricsv");
		sort($output);
		$cntcsv=count($output);
		
		$j=0;
		$h=1;
		$day_num=0;
		
		while ($day_num<$PRODXDAYS) {
		
			$lines=file($dir.$output[$cntcsv-$h]);
			$countalines = count($lines);
		
			// Digging into the array
			$array = explode(";",$lines[$countalines-$j-1]);
		
			$year = substr($array[0], 0, 4);
			$month = substr($array[0], 4, 2);
			$day = substr($array[0], 6, 2);
		
			//$UTCdate = strtotime ($year."-".$month."-".$day);
			//$UTCdate = $UTCdate *1000;
			$UTCdate = $year."-".$month."-".$day;
			$array[1] = str_replace(",", ".", $array[1]);
			$production=round(($array[1]*$CORRECTFACTOR),1);
		
			$stack[$day_num] = array ($UTCdate, $production);
		
			$j++;
			$day_num++;
		
			if ($countalines==$j) {
				if ($h<$cntcsv) {
					$h++;
					$lines=file($dir.$output[$cntcsv-$h]); //Takes older file
					$countalines = count($lines);
					$j=0;
				} else {
					$PRODXDAYS=$day_num; //Stop
				}
			}
		}
		
		$data = new YesterdayValuesResult();
		$data->label ='Last Days';
		$data->kwht = $output;
		//$data->file = 'data/invt'.$invtnum.'/production/';
		$data->data = $stack;
		break;

	default:
		break;
}

try {
	echo json_encode($data);
} catch (Exception $e) {
	echo "error: <br/>" . $e->getMessage() ;
}

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}


function getTimeStamp($text) {
	// 20120623-05:16:00
	$rawdatetime = explode('-', $text);
	$year = substr($rawdatetime[0], 0, 4);
	$month = substr($rawdatetime[0], 4, 2);
	$day = substr($rawdatetime[0], 6, 2);
	$hour = substr($rawdatetime[1], 0, 2);
	$minute = substr($rawdatetime[1], 3, 2);
	$second = substr($rawdatetime[1], 6, 2);

	// Convert to epoch date
	return strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$second);
}
?>