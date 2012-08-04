<?php
define('checkaccess', TRUE);
include("config/config_main.php");

require 'classes/classloader.php';
//include_once("classes/BaseResult.php");
//include_once("classes/LiveDataResult.php");

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
		$data['menu'] = $menu;
		break;
	case 'getEvents':
		$filename="data/invt$invtnum/infos/events.txt";
		$handle = fopen($filename, "r");
		$contents = explode("\n", fread($handle, filesize($filename)));
		fclose($handle);
		$data['events'] = $contents;
		break;
	case 'getLiveData':
	    include_once("config/config_invt".$invtnum.".php");
		$filename="data/invt$invtnum/infos/live.txt";
		$handle = fopen($filename, "r");
		$array = explode(";", fread($handle, filesize($filename)));
		fclose($handle);
		$UTCdate = strtotime(substr($array[0], 0, 4)."-".substr($array[0], 4, 2)."-".substr($array[0], 6, 2)." ".substr($array[0], 9, 2).":".substr($array[0], 12, 2).":".substr($array[0], 15, 2));

		// loop through all livedata values to replace "comma" to "dot"
		foreach ($array as $key => $value){
			$array[$key]  = str_replace(",", ".", $value);
		}

		/*
		 * $array[11] = by inverter reported efficiency;
		* $CORRECTFACTOR = var set in int_config for correcting reported efficiency;
		* $COEF = COrrected EFficiency;
		*/
		$COEF=($array[11]/100)*$CORRECTFACTOR;
		$COEF=($COEF > 1) ? 1 : $COEF;

		$array[9]=$array[9]*$COEF;
		if ($array[9]>1000) { // Round power > 1000W
			$array[9]= round($array[9],0);
		} else {
			$array[9]= round($array[9],9);
		}

		$pMaxOTD=file("data/invt$invtnum/infos/pmaxotd.txt");
		$pMaxArray = explode(";",$pMaxOTD[0]);

		$liveData = new LiveDataResult();
		$liveData->setMppOne(floatval(round($array[1],2)), floatval(round($array[2],2)), floatval(round($array[3],2)));
		$liveData->setMppTwo(floatval(round($array[4],2)), floatval(round($array[5],2)), floatval(round($array[6],2)));
		$liveData->setGrid(floatval(round($array[7],2)), floatval(round($array[8],2)), floatval($array[9]));

		$liveData->valueSDTE = $UTCdate*1000;
		$liveData->valueFRQ = floatval(round($array[10],2));
		$liveData->valueEFF = floatval(round($array[11],2));
		$liveData->valueINVT = floatval(round($array[12],1));
		$liveData->valueBOOT = floatval(round($array[13],1));
		$liveData->valueKHWT = floatval($array[14]);

		$liveData->valuePMAXOTD = floatval(round($pMaxArray[1],0));
		$liveData->valuePMAXOTDTIME = (substr($pMaxArray[0], 9, 2).":".substr($pMaxArray[0], 12, 2));

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
	    $lines=file(FileUtil::getLastChangedFileFromDir("data/invt".$invtnum."/csv"));

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
	        $UTCdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$second);

	        //calculate average Power between 2 pooling, more precise
	        //$diffUTCdate = strtotime ($pastyear."-".$pastmonth."-".$pastday." ".$pasthour.":".$pastminute.":".$pastseconde);
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




	    $data = new GraphResult();
	    $data->label ='Gem. Vermogen (W)';
	    $data->file = FileUtil::getLastChangedFileFromDir("data/invt".$invtnum."/csv");
	    $data->data = $points;



	default:
		break;
}

try {
	echo json_encode($data);
} catch (Exception $e) {
	echo "error: <br/>" . $e->getMessage() ;
}
?>