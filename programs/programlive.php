<?php
// Credit Louviaux Jean-Marc 2012
date_default_timezone_set('GMT');
$invtnum = $_GET['invtnum'];

define('checkaccess', TRUE);
$config_invt="../config/config_invt".$invtnum.".php";
include("$config_invt");

$dir = '../data/invt'.$invtnum.'/infos';
$livedash=file($dir."/live.txt");
$array = explode(";",$livedash[0]);

$year = substr($array[0], 0, 4);
$month = substr($array[0], 4, 2);
$day = substr($array[0], 6, 2);
$hour = substr($array[0], 9, 2);
$minute = substr($array[0], 12, 2);
$seconde = substr($array[0], 15, 2);

$UTCdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);

$array[1]= str_replace(",", ".",$array[1]);
$array[2]= str_replace(",", ".",$array[2]);
$array[3]= str_replace(",", ".",$array[3]);
$array[4]= str_replace(",", ".",$array[4]);
$array[5]= str_replace(",", ".",$array[5]);
$array[6]= str_replace(",", ".",$array[6]);
$array[7]= str_replace(",", ".",$array[7]);
$array[8]= str_replace(",", ".",$array[8]);
$array[9]= str_replace(",", ".",$array[9]);
$array[10]= str_replace(",", ".",$array[10]);
$array[11]= str_replace(",", ".",$array[11]); //EFF
$array[12]= str_replace(",", ".",$array[12]);
$array[13]= str_replace(",", ".",$array[13]);

$COEF=($array[11]/100)*$CORRECTFACTOR;
if ($COEF>1) {
	$COEF=1;
}
$array[9]=$array[9]*$COEF;
if ($array[9]>1000) { // Round power > 1000W
	$array[9]= round($array[9],0);
} else {
	$array[9]= round($array[9],2);
}

$pmaxotd=file($dir."/pmaxotd.txt");
$parray = explode(";",$pmaxotd[0]);
$pmax=round($parray[1],0);
$hour = substr($parray[0], 9, 2);
$minute = substr($parray[0], 12, 2);

$arr= array(
		'SDTE' => $UTCdate*1000,
		'I1V' => floatval(round($array[1],2)),
		'I1A' => floatval(round($array[2],2)),
		'I1P' => floatval(round($array[3],2)),
		'I2V' => floatval(round($array[4],2)),
		'I2A' => floatval(round($array[5],2)),
		'I2P' => floatval(round($array[6],2)),
		'GV' => floatval(round($array[7],2)),
		'GA' => floatval(round($array[8],2)),
		'GP' => floatval($array[9]),
		'FRQ' => floatval(round($array[10],2)),
		'EFF' => floatval(round($array[11],2)),
		'INVT' => floatval(round($array[12],1)),
		'BOOT' => floatval(round($array[13],1)),
		'KHWT' => floatval($array[14]),
		'PMAXOTD' => floatval($pmax),
		'PMAXOTDTIME' => ($hour.":".$minute)
);

$ret= array($arr);

header("Content-type: text/json");
echo json_encode($ret);
?>
