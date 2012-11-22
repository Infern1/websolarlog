<?php
// For debugging only
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('checkaccess', TRUE);
//include("config/config_main.php");

require 'classes/classloader.php';
Session::initialize();

try {
// InActiveCheck
$inactiveCheck = new InactiveCheck();
$inactiveCheck->check();

// Retrieve action params
$method = $_GET['method'];


// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// Initialize return array
$dataAdapter = PDODataAdapter::getInstance();
$config = Session::getConfig();

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
		
		$data['tabsPosition'] =  Common::searchMultiArray($tabs, 'active', 'true');
		$lang = array();
		$lang['date'] = _('date');
		$lang['periode'] = _('periode');
		$lang['previous'] = _('previous');
		$lang['next'] = _('next');
		
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
	case 'getEvents':
		$noticeEvents = Util::makeEventsReadable($dataAdapter->readTypeEvents($invtnum,'Notice'));
		$alarmEvents = Util::makeEventsReadable($dataAdapter->readTypeEvents($invtnum,'Alarm'));
		$infoEvents = Util::makeEventsReadable($dataAdapter->readTypeEvents($invtnum,'Info'));

		$lang = array();
		$lang['Time'] = _('Time');
		$lang['Inv'] = _('Inv');
		$lang['Inverter'] = _('Inverter');
		$lang['Event'] = _('Event');
		$lang['Miscellaneous'] = _('Miscellaneous');
		$lang['Notice'] = _('Notice');
		$lang['Alarm'] = _('Alarm');
		$lang['Info'] = _('Info');
		$lang['Information'] = _('Information');
		$data['lang'] = $lang;
		$data['noticeEvents'] = $noticeEvents;
		$data['alarmEvents'] = $alarmEvents;
		$data['infoEvents'] = $infoEvents;
		
		break;
	case 'getGraphPoints':
		
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
		$lines = $dataAdapter->getDetailsHistory($invtnum,$date);

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
		$data['dayData'] = $dayData;
		break;
	case 'getPeriodFilter':
		$options = array();
		if($type=="all" ){
			$options[] =array( "value" => "Today","name"=> _("Day"));
			$options[] =array( "value" => "Week","name"=> _("Week"));
			$options[] =array( "value" => "Month","name"=> _("Month"));
			$options[] =array( "value" => "Year","name"=> _("Year"));
		}else{
			$options[] =array( "value" => "Today","name"=> _("Day"));
		}

		foreach ($config->inverters as $inverter){
			 $data['inverters'][] = array('id'=>$inverter->id,'name'=>$inverter->name);
		}

		$lang = array();
		$lang['date'] = _('date');
		$lang['inv'] = _('inv');
		$lang['periode'] = _('periode');
		$lang['previous'] = _('previous');
		$lang['next'] = _('next');

		$dayData->success = true;
		$data['lang'] = $lang;
		$data['options'] = $options;
		$data['dayData'] = $dayData;
		
		break;
	case 'getDetailsSwitches':

			$lang = array();
		$lang['showHideGroups'] = _('Show or hide graph groups:');
		$lang['P'] = _('Power');
		$lang['V'] = _('Voltage');
		$lang['A'] = _('Amps');
		$lang['F'] = _('Frequency');
		$lang['R'] = _('Ratio');
		$lang['T'] = _('Temperature');
		$lang['E'] = _('Efficiency');
		$dayData->success = true;
		$data['lang'] = $lang;
		$data['dayData'] = $dayData;
		
		break;
	case 'getProductionGraph':
		(!$year)?$year=date("Y"):$year=$year;
		(!$invtnum)?$invtnum=1:$invtnum=$invtnum;

		$lines = $dataAdapter->getYearSumPowerPerMonth($invtnum, $date);

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
			$maxEnergy[] = $dataAdapter->getYearMaxEnergyPerMonth($inverter['id'],$date);
			$energy[] = $dataAdapter->getYearEnergyPerMonth($inverter['id'],$date);
			$maxPower[] = $dataAdapter->getYearMaxPowerPerMonth($inverter['id'],$date);
			$maxMinEnergyYear[] = $dataAdapter->getMaxMinEnergyYear($inverter['id'],$date);
		}

		$lang = array();
		$lang['today'] 			= _("Today");
		$lang['maxGridPower'] 	= _("Max Grid Power");
		$lang['date'] 			= _("date");
		$lang['selectPeriod']	= _("Select period");
		$lang['totalKWh'] 		= _("TotalKWh");
		$lang['inv'] 			= _("Inv");
		$lang['kwh'] 			= _("kwh");
		$lang['historyValues'] 	= _("History values");
		$lang['loading'] 		= _("loading")."...";
		$lang['watt'] 			= _("watt");
		$lang['Year']			= _("Year");
		$lang['valuesGroupedByMonthYearText'] = _("The values below are grouped by Month of the selected Year");

		$data['lang'] = $lang;
		
		$dayData = new DayDataResult();
		$dayData->data = array(
				"maxPower"=>$maxPower,
				"energy"=>$energy,
				"minMaxEnergy"=>$maxMinEnergyYear,
				"maxEnergy"=>$maxEnergy,
		);
		$dayData->success = true;

		$data['yearData'] = $dayData;
		break;
	case 'getPageMonthValues':
		$beans = R::findAndExport('Inverter');
		
		foreach ($beans as $inverter){
			$maxPower[] = $dataAdapter->getMonthMaxPowerPerDay($inverter['id'], $date);
			$maxEnergy[] = $dataAdapter->getMonthEnergyPerDay($inverter['id'], $date);
			$minMaxEnergyMonth[] = $dataAdapter->getMaxMinEnergyMonth($inverter['id'],$date);
		}

		$dayData = new DayDataResult();
		$dayData->data = array(
				"maxPower"=>$maxPower,
				"maxEnergy"=>$maxEnergy,
				"minMaxEnergy"=>$minMaxEnergyMonth
		);
		$lang = array();
		$lang['today'] 			= _("Today");
		$lang['maxGridPower'] 	= _("Max Grid Power");
		$lang['date'] 			= _("date");
		$lang['selectPeriod']	= _("Select period");
		$lang['totalKWh'] 		= _("TotalKWh");
		$lang['inv'] 			= _("Inv");
		$lang['kwh'] 			= _("kwh");
		$lang['historyValues'] 	= _("History values");
		$lang['loading'] 		= _("loading")."...";
		$lang['watt'] 			= _("watt");
		$lang['Month']			= _("Month");
		$lang['month']			= strtolower(_("Month"));
		$lang['valuesGroupedByDayMonthText'] = _("The values below are grouped by Day of the selected Month");
		
		$dayData->success = true;
		$data['lang'] = $lang;
		$data['monthData'] = $dayData;
		break;
	case 'getPageTodayValues':
		$beans = R::findAndExport('Inverter');
		
		$maxEnergy = array();
		$maxPower = array();
		
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
		$whichMonth = Common::getValue('whichMonth', 0);
		$whichYear = Common::getValue('whichYear', 0);
		$compareMonth = Common::getValue('compareMonth', 0);
		$compareYear = Common::getValue('compareYear', 0);
		$invtnum = Common::getValue('invtnum', 0);

		$monthYear = $dataAdapter->getYearsMonthCompareFilters();
		$inverters = R::findAndExport('Inverter');
		foreach ($inverters as $inv){
			$inverter[] = array("name"=>$inv['name'],"id"=>$inv['id']);
		}

		$lines = $dataAdapter->getCompareGraph($invtnum, $whichMonth,$whichYear,$compareMonth,$compareYear);
		
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
				"compare"=>$lines['compareBeans']->points
				);
		$dayData->type = $lines['type'];
		$dayData->config = $lines['config'];
		$dayData->expectedMonthString = $lines['expectedMonthString'];
		$dayData->expectedPerc = $lines['expectedPerc'];
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
		$indexValues = $dataAdapter->readPageIndexLiveValues($config);
		$lang['DCPower'] 		= _("DC Power");
		$lang['ACPower'] 		= _("AC Power");
		$lang['Efficiency'] 	= _("Efficiency");
		
		$data['lang'] = $lang;
		$data['IndexValues'] = $indexValues;
		break;
	case 'getPageIndexValues':
		$indexValues = $dataAdapter->readPageIndexData($config);
		$lang = array();
		$lang['someFigures'] 	= _("Some Figures*");
		$lang['by']			 	= _("By");
		$lang['status']			= _("status");
		$lang['IT'] 			= _("IT");
		$lang['total'] 			= _("Total");
		
		$lang['AvgDay']	 	= _("Avg. Day");
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
		$lang['overallTotal']	= _("overall**");
		$lang['KWHKWP']			= _("kWh/kWp");
		
		$lang['allFiguresAreInKWH']= _("* All figures are in kWh");
		$lang['overallTotalText']= _("** Incl. initial kWh");
		
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