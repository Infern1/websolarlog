<?php
// Credit Louviaux Jean-Marc 2012
define('checkaccess', TRUE);
include('../config/config_main.php');

function tricsv($var){
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
	include('../config/config_invt'.$invtnum.'.php');
	$dir = '../data/invt'.$invtnum.'/infos';
	$livedash=file($dir."/live.txt");
	$array = explode(";",$livedash[0]);
	$array[9]= str_replace(",", ".",$array[9]);

	$COEF=($array[11]/100)*$CORRECTFACTOR;
	if ($COEF>1) {
		$COEF=1;
	}
	$array[9]=$array[9]*$COEF;
	$GPTOT=$array[9]+$GPTOT;
}

if ($GPTOT>1000) {
	$GPTOT= round($GPTOT,0);
} else {
	$GPTOT= round($GPTOT,2);
}

$pmaxotd=file("../data/pmaxotd.txt");
$parray = explode(";",$pmaxotd[0]);
$pmax=round($parray[1],0);
$hour = substr($parray[0], 9, 2);
$minute = substr($parray[0], 12, 2);

$arr= array(
		'GPTOT' => floatval($GPTOT),
		'PMAXOTD' => floatval($pmax),
		'PMAXOTDTIME' => ($hour.":".$minute)
);

$ret= array($arr);
header("Content-type: text/json");
echo json_encode($ret);
?>
