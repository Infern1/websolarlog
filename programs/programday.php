<?php
// Credit Louviaux Jean-Marc 2012
include_once("../classes/Formulas.php");

date_default_timezone_set('GMT');
$invtnum = $_GET['invtnum'];

$dir = '../data/invt'.$invtnum.'/csv';
$output = scandir($dir);
$output = array_filter($output, "tricsv");
sort($output);
$cnt=count($output);

function tricsv($var){
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

$lines=file($dir."/".$output[$cnt-1]);
$contalines = count($lines);

// Loop through the array
$MaxPow = 0;
foreach ($lines as $line_num => $line) {

	// remove all whitespaces
	$array = explode(";",$line);

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
		$AvgPOW = Formulas::calcAveragePower($KWHT[$pastline_num], $KWHT[$line_num], $diffTime);
	} else {
		$AvgPOW=0;
	}

	$UTCdate = $UTCdate *1000;
	$stack[$line_num] = array ($UTCdate, $AvgPOW);

	if ($AvgPOW>=$MaxPow && $line_num<$contalines) { // Maximum
		$MaxPow=$AvgPOW;
		$stack2[0] = array ($UTCdate, $MaxPow);
	}
}

$stack2[1] = array ($UTCdate, $AvgPOW);  // Latest value

$data = array(
		0 => array(
				'name' => 'Avg. Power',
				'type'=> 'areaspline',
				'data' => $stack
		),
		1 => array(
				'name' => 'Max',
				'type'=> 'scatter',
				'data' => $stack2
		),
);

header("Content-type: text/json");
echo json_encode($data);
?>

