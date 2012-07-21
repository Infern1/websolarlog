<?php
// Credit Louviaux Jean-Marc 2012
date_default_timezone_set('GMT');
$invtnum = $_GET['invtnum'];

define('checkaccess', TRUE);
$config_invt="../config/config_invt".$invtnum.".php";
include("$config_invt");

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

$dir = '../data/invt'.$invtnum.'/production/';
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

	$UTCdate = strtotime ($year."-".$month."-".$day);
	$UTCdate = $UTCdate *1000;

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

sort($stack);
$data = array(
		0 => array(
				'name' => 'kWh',
				'animation' => false,
				'data' => $stack
		)
);

header("Content-type: text/json");
echo json_encode($data);
?>
