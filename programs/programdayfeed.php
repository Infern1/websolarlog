<?php
// Credit Louviaux Jean-Marc 2012
date_default_timezone_set('GMT');
$invtnum = $_GET['invtnum'];

define('checkaccess', TRUE);
$config_invt="../config/config_invt".$invtnum.".php";
include($config_invt);
include_once("../classes/Formulas.php");

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

$dir = '../data/invt'.$invtnum.'/csv';
$output = scandir($dir);
$output = array_filter($output, "tricsv");
sort($output);
$cnt=count($output);

$lines=file($dir."/".$output[$cnt-1]);
$contalines = count($lines);

// Loop through the array
$MaxPow = 0;
foreach ($lines as $line_num => $line) {

	// remove all whitespaces
	$array = preg_split("/;/",$line);

	$SDTE[$line_num]=$array[0];
	$KWHT[$line_num]=str_replace(",", ".",$array[14]);

	if ($line_num==0)  {
		$pastline_num=0;
	} else {
		$pastline_num=$line_num-1;
	}

	$year = substr($SDTE[$line_num], 0, 4);
	$month = substr($SDTE[$line_num], 4, 2);
	$day = substr($SDTE[$line_num], 6, 2);
	$hour = substr($SDTE[$line_num], 9, 2);
	$minute = substr($SDTE[$line_num], 12, 2);
	$seconde = substr($SDTE[$line_num], 15, 2);

	$pastyear = substr($SDTE[$pastline_num], 0, 4);
	$pastmonth = substr($SDTE[$pastline_num], 4, 2);
	$pastday = substr($SDTE[$pastline_num], 6, 2);
	$pasthour = substr($SDTE[$pastline_num], 9, 2);
	$pastminute = substr($SDTE[$pastline_num], 12, 2);
	$pastseconde = substr($SDTE[$pastline_num], 15, 2);

	// Convert to epoch date
	$UTCdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);

	//calculate average Power between 2 pooling, more precise
	$diffUTCdate = strtotime ($pastyear."-".$pastmonth."-".$pastday." ".$pasthour.":".$pastminute.":".$pastseconde);
	$diffTime=$UTCdate-$diffUTCdate;

	if ($diffTime!=0) {
		//AveragePOWer = ((KiloWattHourTime[currentline] - KiloWattHourTime[last add line](give timediff in sec) *3600 (to hour) / $difftime) * 1000 (watt??)), round by 1 decimal)
		// ^averagepower over a given time.
		$AvgPOW = Formulas::calcAveragePower($KWHT[$pastline_num], $KWHT[$line_num], $diffTime);
	} else {
		$AvgPOW=0;
	}

	$UTCdate = $UTCdate *1000;

	if ($AvgPOW>=$MaxPow && $line_num<$contalines) { // Past maximum
		$MaxPow=$AvgPOW;
		$MaxTime=$UTCdate;
	}
}

$LatestPow = $AvgPOW;  // Latest value
$LatestTime = $UTCdate;

// Updating title
$array = explode(";",$lines[0]);
$array2 = explode(";",$lines[$contalines-1]);
$array[14]=str_replace(",", ".",$array[14]);
$array2[14]=str_replace(",", ".",$array2[14]);
// corrected KiloWattHourDay
$KWHD = Formulas::calcKiloWattHourDay($array[14], $array2[14], $CORRECTFACTOR, 1);
$PTITLE="($KWHD kWh)";

$data = array(
		0 => array( // Add the last point
				'LastTime' => $UTCdate,
				'LastValue' => $AvgPOW
		),
		1 => array(
				'MaxTime' => $MaxTime,
				'MaxPow' => $MaxPow
		),
		2 => array(
				'PTITLE' => $PTITLE
		)
);

header("Content-type: text/json");
echo json_encode($data);
?>
