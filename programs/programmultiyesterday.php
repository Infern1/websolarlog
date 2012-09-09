<?php
// Credit Louviaux Jean-Marc 2012
define('checkaccess', TRUE);
include('../config/config_main.php');
include_once("../classes/Formulas.php");
date_default_timezone_set('GMT');

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

$i=0;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
	$dir = '../data/invt'.$invtnum.'/csv';
	$output = scandir($dir);
	$output = array_filter($output, "tricsv");
	sort($output);
	$cnt=count($output);

	$lines=file($dir."/".$output[$cnt-2]);

	$year = substr($output[$cnt-1], 0, 4);
	$month = substr($output[$cnt-1], 4, 2);
	$day = substr($output[$cnt-1], 6, 2);
	$fileUTCdate[$invtnum] = strtotime ($year."-".$month."-".$day);

	$contalines = count($lines);

	foreach ($lines as $line_num => $line) {
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

		$UTCdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);

		$diffUTCdate = strtotime ($pastyear."-".$pastmonth."-".$pastday." ".$pasthour.":".$pastminute.":".$pastseconde);
		$diffTime=$UTCdate-$diffUTCdate;

		if ($diffTime!=0) {
			$AvgPOW = Formulas::calcAveragePower($KWHT[$pastline_num], $KWHT[$line_num], $diffTime);
		} else {
			$AvgPOW=0;
		}

		$UTCdate = $UTCdate *1000;

		$totAvgPOW = $AvgPOW + $totstack[$hour.$minute][1];
		$totstack[$hour.$minute] = array ($UTCdate, $totAvgPOW); // minute precision
		if ($totAvgPOW >=$MaxPow && $line_num<$contalines) { // Scatter max
			$MaxPow=$totAvgPOW;
			$stack2[0] = array ($UTCdate, $MaxPow);
		}
	}
} // multi

sort($totstack);
$data[0] = array('name' => Avgtot, 'data' => $totstack, 'type'=> 'areaspline');
$data[1] = array('name' => "Max", 'data' => $stack2, 'type'=> 'scatter');

header("Content-type: text/json");
echo json_encode($data);
?>
