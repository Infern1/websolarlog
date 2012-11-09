<?php
// For debugging only
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('checkaccess', TRUE);
include("config/config_main.php");

require 'classes/classloader.php';

Session::setLanguage("nl_NL");

try {
// InActiveCheck
$inactiveCheck = new InactiveCheck();
$inactiveCheck->check();

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
$dataAdapter = new PDODataAdapter();
$config = $dataAdapter->readConfig();

$data = array();
$invtnum = Common::getValue('invtnum', 0);
$page = Common::getValue('page', 0);
$count = Common::getValue('count', 0);
$type = Common::getValue('type', 0);
$date = Common::getValue('date', 0);
$year = Common::getValue('year', 0);



switch ($method) {
	case 'getTabs':
		// TODO :: Move to json file or something???
		$tabs = array();
		$tabs[] = array( "graphName" => "Today","translation" => _("Today"),"position" => "0","active" => ($page == "index") ? 'true' : 'false');
		$tabs[] = array( "graphName" => "Yesterday","translation" => _("Yesterday"),"position" => "1","active" => ($page == "yesterday") ? 'true' : 'false');
		$tabs[] = array( "graphName" => "Month","translation" => _("Month"),"position" => "2","active" => ($page == "month") ? 'true' : 'false');
		$tabs[] = array( "graphName" => "Year","translation" => _("Year"),"position" => "3","active" => ($page == "year") ? 'true' : 'false');
		
		$options = array();
		$options[] =array( "value" => "Today","name"=> _("Day"));
		$options[] =array( "value" => "Week","name"=> _("Week"));
		$options[] =array( "value" => "Month","name"=> _("Month"));
		$options[] =array( "value" => "Year","name"=> _("Year"));
		
		//$tabs[] = array( "graphName" => "Expected","position" => "4","active" => ($page == "expected") ? 'true' : 'false');
		$data['tabsPosition'] =  Common::searchMultiArray($tabs, 'active', 'true');
		$lang = array();
		$lang['date'] = _('date');
		$lang['periode'] = _('periode');
		$data['lang'] = $lang;
		$data['tabs'] = $tabs;
		$data['options'] = $options;

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
// 	case 'getMenu':
// 		// TODO :: Move to json file or something???
// 		$menu = array();
// 		$menu[] = array( "url" => "index.php", "title" => $lgMINDEX);
// 		$menu[] = array( "url" => "indexdetailed.php", "title" => $lgMDETAILED);
// 		$menu[] = array( "url" => "indexproduction.php", "title" => $lgMPRODUCTION);
// 		$menu[] = array( "url" => "indexcomparison.php", "title" => $lgMCOMPARISON);
// 		$menu[] = array( "url" => "plantinfo.php", "title" => $lgMINFO);
// 		$menu[] = array( "url" => "index_chartstest.php", "title" => "Test page");
// 		$data['menu'] = $menu;
// 		break;
	case 'getEvents':
		$events = $dataAdapter->readEvent($invtnum);
		$resultEvents=array();
		foreach ($events as $event){
			
			$order   = array("\r\n", "\n", "\r");
			$replace = ' ';
			// Processes \r\n's first so they aren't converted twice.
			$eventText= str_replace($order, $replace, $event['Event']);
			
			$eventText = preg_split('/(Alarm)/', $eventText, -1, PREG_SPLIT_DELIM_CAPTURE);
			
			if(count($eventText)>1){
				$eventText = $eventText[1].' '.substr($eventText[2],4,strlen($eventText[2]));
			}else{
				$eventText = trim($eventText[0]);
			}
			$event['time'] = date('d-m-Y H:i:s',$event['time']);
			$event['Event'] = $eventText;
			$newEvents[] = $event;
		}
		/*
		Alarm 1: Grid Fail Warning 003 
		Alarm\r\n2: Grid Fail Warning 003 
		Alarm 3: Grid Fail Warning 003 
		Alarm 4: Grid Fail\r\nWarning 003
		*/
		//var_dump($resultEvents);
		$lang = array();
		$lang['Time'] = _('Time');
		$lang['Inverter'] = _('Inverter');
		$lang['Events'] = _('Events');
		$data['lang'] = $lang;
		$data['events'] = $newEvents;
		
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
		
		$lines = $dataAdapter->getGraphPoint(1, $type, $date);
		$dayData = new DayDataResult();
		$dayData->data = $lines->points;
		$dayData->valueKWHT = $lines->KWHT;
		$dayData->KWHTUnit = $lines->KWHTUnit;
		$dayData->KWHKWP = $lines->KWHKWP;
		$dayData->success = true;
		$lang = array();
		$lang['cumPowerW'] = _('cum. Power (W)');
		$lang['avgPowerW'] = _('avg. Power (W)');
		$lang['harvested'] = _('harvested (W)');
		$lang['cumulative'] = _('cumulative (W)');
		$lang['totalEnergy'] = _('total Energy');
		$data['lang'] = $lang;
		$data['dayData'] = $dayData;
		break;
	case 'getDetailsGraph':
		$config_invt="config/config_invt".$invtnum.".php";
		include("$config_invt");

		$lines = $dataAdapter->getDetailsHistory($invtnum,$date);

		//var_dump($details);

		$dayData = new DayDataResult();
		$dayData->data = $lines['details'];
		$dayData->labels = $lines['labels'];
		$dayData->switches = $lines['switches'];
		$dayData->max = $lines['max'];
		
		$lang = array();
		$lang['showHideGroups'] = _('Show or hide graph groups:');
		$lang['P'] = _('Power');
		$lang['V'] = _('Voltage');
		$lang['A'] = _('Amps');
		$lang['F'] = _('Frequency');
		$lang['R'] = _('Ratio');
		$lang['T'] = _('Temperature');
		$lang['E'] = _('Efficiency');
		$data['lang'] = $lang;
		
		$dayData->success = true;
		$data['dayData'] = $dayData;
		break;
	case 'getProductionGraph':
		//$config_invt="config/config_invt".$invtnum.".php";
		//include("$config_invt");
		(!$year)?$year=date("Y"):$year=$year;
		(!$invtnum)?$invtnum=1:$invtnum=$invtnum;

		$lines = $dataAdapter->getYearSumPowerPerMonth($invtnum, $date);
		//var_dump($lines['energy']->points);


		$dayData = new DayDataResult();
		$dayData->data = $lines['energy']->points;
		$dayData->success = true;
		$data['dayData'] = $dayData;
		break;
	case 'getPageYearValues':
		$beans = R::findAndExport('Inverter');
		$maxEnergy=array();
		$energy=array();
		$maxPower=array();
		$minMaxEnergyYear=array();

		foreach ($beans as $inverter){
			$maxEnergy[] = $dataAdapter->getYearMaxEnergyPerMonth($inverter['id']);
			$energy[] = $dataAdapter->getYearEnergyPerMonth($inverter['id']);
			$maxPower[] = $dataAdapter->getYearMaxPowerPerMonth($inverter['id']);
			$minMaxEnergyYear[] = $dataAdapter->getMaxMinEnergyYear($inverter['id']);
		}
		
		
		
		$dayData = new DayDataResult();
		$dayData->data = array(
				"maxPower"=>$maxPower,
				"energy"=>$energy,
				"minMaxEnergy"=>$minMaxEnergyYear,
				"maxEnergy"=>$maxEnergy,
		);
		$dayData->success = true;

		$data['yearData'] = $dayData;
		break;
	case 'getPageMonthValues':
		$beans = R::findAndExport('Inverter');
		
		foreach ($beans as $inverter){
			$maxPower[] = $dataAdapter->getMonthMaxPowerPerDay($inverter['id']);
		}

		$dayData = new DayDataResult();
		$dayData->data = array(
				"maxPower"=>$maxPower,
				"maxEnergy"=>$maxEnergy,
		);
		$dayData->success = true;
		$data['monthData'] = $dayData;
		break;
	case 'getPageTodayValues':
		$beans = R::findAndExport('Inverter');
		foreach ($beans as $inverter){
			$maxEnergy[] = $dataAdapter->getDayEnergyPerDay($inverter['id']);
			$maxPower[] = $dataAdapter->getDayMaxPowerPerDay($inverter['id']);
		}
		$dayData = new DayDataResult();
		$dayData->data = array(
				"maxPower"=>$maxPower,
				"maxEnergy"=>$maxEnergy,
		);
		$dayData->success = true;
		$data['dayData'] = $dayData;
		
		$lang = array();
		$lang['today'] 			= _("Today");
		$lang['maxGridPower'] 	= _("Max Grid Power");
		$lang['date'] 			= _("date");
		$lang['totalKWh'] 		= _("TotalKWh");
		$lang['inv'] 			= _("Inv");
		$lang['kwh'] 			= _("kwh");
		$lang['historyValues'] 	= _("History values");
		$lang['loading'] 		= _("loading")."...";
		$lang['watt'] 			= _("watt");
		$data['lang'] = $lang;
		break;
	case 'getCompareGraph':
		//$config_invt="config/config_invt".$invtnum.".php";
		//include("$config_invt");
		$whichMonth = Common::getValue('whichMonth', 0);
		$whichYear = Common::getValue('whichYear', 0);
		$compareMonth = Common::getValue('compareMonth', 0);
		$compareYear = Common::getValue('compareYear', 0);
		$invtnum = Common::getValue('invnum', 0);
		
		$monthYear = $dataAdapter->getYearsMonthCompareFilters();
		$inverters = R::findAndExport('Inverter');
		foreach ($inverters as $inv){
			$inverter[] = array("name"=>$inv['name'],"id"=>$inv['id']);
		}
		$lines = $dataAdapter->getCompareGraph($invtnum, $config,$whichMonth,$whichYear,$compareMonth,$compareYear);
		//var_dump($inverters);

		
		$lang = array();
		$lang['today'] 			= _("Today");
		$lang['maxGridPower'] 	= _("Max Grid Power");
		$lang['date'] 			= _("date");
		$lang['totalKWh'] 		= _("TotalKWh");
		$lang['inv'] 			= _("Inv");
		$lang['kwh'] 			= _("kwh");
		$lang['historyValues'] 	= _("History values");
		$lang['loading'] 		= _("loading")."...";
		$lang['watt'] 			= _("watt");
		$data['lang'] = $lang;
		
		$dayData = new DayDataResult();
		$dayData->month = $monthYear['month'];
		$dayData->year = $monthYear['year'];
		$dayData->inverters = $inverter;
		$dayData->data = array(
				"which"=>$lines['whichBeans']->points,
				"compare"=>$lines['compareBeans']->points,
				
				);
		$dayData->type = $lines['type'];
		$dayData->success = true;
		$data['dayData'] = $dayData;
		break;
	case 'getHistoryValues':
		$history = $dataAdapter->getDayHistoryPerRecord();
		for ($i = 0; $i < count($history); $i++) {
			$history[$i]['GP'] = number_format($history[$i]['GP'],2,',','');
		}
		$dayData = new DayDataResult();
		$dayData->data = array("history"=>$history);
		$dayData->success = true;
		$data['dayData'] = $dayData;
		break;
	case 'getPageIndexLiveValues':
		$indexValues = $dataAdapter->readPageIndexLiveValues();
		$lang['DCPower'] 		= _("DC Power");
		$lang['ACPower'] 		= _("AC Power");
		$lang['Efficiency'] 	= _("Efficiency");
		
		$data['lang'] = $lang;
		$data['IndexValues'] = $indexValues;
		break;
	case 'getPageIndexValues':
		$indexValues = $dataAdapter->readPageIndexData();
		$lang = array();
		$lang['someFigures'] 	= _("Some Figures");
		$lang['by']			 	= _("By");
		$lang['total'] 			= _("Total");
		$lang['averages']	 	= _("Averages");
		$lang['today'] 			= _("Today");
		$lang['week'] 			= _("Week");
		$lang['month'] 			= _("Month");
		$lang['year'] 			= _("Year");
		$lang['overall'] 		= _("overall");
		$lang['inv'] 			= _("Inv.");
		$lang['gridShort'] 		= _("gridShort");
		$lang['time'] 			= _("Time");
		$lang['inverter'] 		= _("Inverter");
		$lang['power'] 			= _("Power");
		$lang['ratio'] 			= _("ratio");
		$lang['allFiguresAreInKWH']= _("* All figures are in kWh");
		
		$data['lang'] = $lang;
		$data['IndexValues'] = $indexValues;
		break;
	default:
		break;
}


echo json_encode($data);
} catch (Exception $e) {
	$data = array();
	$data["success"] = false;
	$data["message"] = $e->getMessage();
	echo json_encode($data);
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