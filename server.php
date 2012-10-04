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
$config = new Config();

$data = array();
$invtnum = Common::getValue('invtnum', 0);
$page = Common::getValue('page', 0);
$count = Common::getValue('count', 0);
$type = Common::getValue('type', 0);
$date = Common::getValue('date', 0);

$dataAdapter = new PDODataAdapter();


switch ($method) {
	case 'getTabs':
		// TODO :: Move to json file or something???
		$tabs = array();
		$tabs[] = array( "graphName" => "Today","position" => "0","active" => ($page == "index") ? 'true' : 'false');
		$tabs[] = array( "graphName" => "Yesterday","position" => "1","active" => ($page == "yesterday") ? 'true' : 'false');
		$tabs[] = array( "graphName" => "Month","position" => "2","active" => ($page == "month") ? 'true' : 'false');
		$tabs[] = array( "graphName" => "Year","position" => "3","active" => ($page == "year") ? 'true' : 'false');
		$data['tabsPosition'] =  Common::searchMultiArray($tabs, 'active', 'true');
		$data['tabs'] = $tabs;

		break;
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
		$liveData->valueMPSDTE = $mpt->SDTE; // TODO :: ^ above code is wrong if i check the containing data

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
	case 'getGraphPoints':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");
		
		$lines = $dataAdapter->getGraphPoint(1, $type, "");
		$dayData = new DayDataResult();
		$dayData->data = $lines->points;
		$dayData->valueKWHT = $lines->KWHT;
		$dayData->success = true;
		$data['dayData'] = $dayData;
		break;
	case 'getPageMonthValues':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");

		// get the date of today.
		//$date = date("Ymd",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		
		/*
		- kWh van vandaag icm CO2 vandaag 
		 */
		$energyToday = $dataAdapter->readEnergyValues($invtnum, "month", 1, 0);
		
		/*
		 * - Max Watt van de dag 
		 */
		
		$MaxPowerOfToday = $dataAdapter->readMaxPowerValues($invtnum, "month", 1, 0);
		/*
		 * - De gauges tonen w/v/a
		 */
		/// ??????????????????????????????
		
		/*
		 * - Grafiek van vandaag (Watt) 
		 */
		///
		
		/*
		 * - Grafiek met w/v/a van vandaag
		 */
		
		/// ????????????????????????????????????

		$dayData = new DayDataResult();
		$dayData->data = array(
				"Energy"=>$energyToday,
				"MaxPower"=>$MaxPowerOfToday,
				"CO2Today"=>Formulas::CO2kWh($energyToday->KWH,$config->co2kwh),
				"CO2Overall"=>Formulas::CO2kWh($energyToday->KWHT, $config->co2kwh)		
				);
		$dayData->success = true;
	
		$data['dayData'] = $dayData;
		break;
	case 'getPageYearValues':
			$config_invt="config/config_invt".$invtnum.".php";
			include("$config_invt");
		
			// get the date of today.
			//$date = date("Ymd",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		
			/*
				- kWh van vandaag icm CO2 vandaag
			*/
			$energyToday = $dataAdapter->readEnergyValues($invtnum, "year", 1, 0);
		
			/*
			 * - Max Watt van de dag
			*/
		
			$MaxPowerOfToday = $dataAdapter->readMaxPowerValues($invtnum, "year", 1, 0);
			/*
			 * - De gauges tonen w/v/a
			*/
			/// ??????????????????????????????
		
			/*
			 * - Grafiek van vandaag (Watt)
			*/
			///
		
			/*
			 * - Grafiek met w/v/a van vandaag
			*/
		
			/// ????????????????????????????????????
		
			$dayData = new DayDataResult();
			$dayData->data = array(
					"Energy"=>$energyToday,
					"MaxPower"=>$MaxPowerOfToday,
					"CO2Today"=>Formulas::CO2kWh($energyToday->KWH,$config->co2kwh),
					"CO2Overall"=>Formulas::CO2kWh($energyToday->KWHT, $config->co2kwh)
			);
			$dayData->success = true;
		
			$data['dayData'] = $dayData;
		break;
	case 'getPageTodayValues':
			//$config_invt="config/config_invt".$invtnum.".php";
			//include("$config_invt");
		
			// get the date of today.
			//$date = date("Ymd",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		
			/*
				- kWh van vandaag icm CO2 vandaag
			*/
			$energyToday = $dataAdapter->readEnergyValues(1, "today", 1, 0);
			
			/*
			 * - Max Watt van de dag
			*/
		
			$MaxPowerOfToday = $dataAdapter->readMaxPowerValues(1, "today", 1, 0);
			/*
			 * - De gauges tonen w/v/a
			*/
			/// ??????????????????????????????
		
			/*
			 * - Grafiek van vandaag (Watt)
			*/
			///
		
			/*
			 * - Grafiek met w/v/a van vandaag
			*/
		
			/// ????????????????????????????????????
		
			$dayData = new DayDataResult();
			$dayData->data = array(
					"Energy"=>$energyToday,
					"MaxPower"=>$MaxPowerOfToday,
					"CO2Today"=>Formulas::CO2kWh($energyToday->KWH,$config->co2kwh),
					"CO2Overall"=>Formulas::CO2kWh($energyToday->KWHT, $config->co2kwh)
			);
			$dayData->success = true;
		
			$data['dayData'] = $dayData;
			break;
	/*case 'getYesterdayValues':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");
		// get the date of today.
		$date = date("Ymd",mktime(0, 0, 0, date("m")  , date("d")-1 , date("Y")));

		$lines = $dataAdapter->readDailyData($date,$invtnum);
		$dayData = new DayDataResult();
		$dayData->data = $lines->points;
		$dayData->valueKWHT = $lines->KWHT;
		$dayData->success = true;

		$data['dayData'] = $dayData;
		break;*/
	case 'getPageIndexValues':
		$indexValues = $dataAdapter->readPageIndexData();
		$data['IndexValues'] = $indexValues;
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