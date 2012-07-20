<?php 
// Credit Louviaux Jean-Marc 2012
define('checkaccess', TRUE);
include('../config/config_main.php');
date_default_timezone_set('GMT');
if (isset($_COOKIE['user_lang'])){
	$user_lang=$_COOKIE['user_lang'];
} else {
	$user_lang="English";
};
include("../languages/".$user_lang.".php");

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

for ($i=1;$i<=$NUMINV;$i++) {
	include ('../config/config_invt'.$i.'.php');
	$PLANT_POWERtot = $PLANT_POWER+$PLANT_POWERtot;
	if ($PRODXDAYS>$PRODXDAYStot) {
		$PRODXDAYStot= $PRODXDAYS;
	}
}

for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { // Multi
	$config_invt="../config/config_invt".$invtnum.".php";
	include("$config_invt");

	$dir = '../data/invt'.$invtnum.'/production/';
	$output = scandir($dir);
	$output = array_filter($output, "tricsv");
	sort($output);
	$cntcsv=count($output);

	$j=0;
	$h=1;
	$day_num=0;

	while ($day_num<$PRODXDAYStot) {

		$lines=file($dir.$output[$cntcsv-$h]);
		$countalines = count($lines);

		// Digging into the array
		$array = preg_split("/;/",$lines[$countalines-$j-1]);

		$year = substr($array[0], 0, 4);
		$month = substr($array[0], 4, 2);
		$day = substr($array[0], 6, 2);

		$UTCdate = strtotime ($year."-".$month."-".$day);
		$UTCdate = $UTCdate *1000;

		$array[1] = str_replace(",", ".", $array[1]);
		$production=round(($array[1]*$CORRECTFACTOR),1);
		$stack[$invtnum][$day_num] = array ($UTCdate, $production);

		$j++;
		$day_num++;

		if ($countalines==$j) {
			if ($h<$cntcsv) {
				$h++;
				$lines=file($dir.$output[$cntcsv-$h]); //Takes older file
				$countalines = count($lines);
				$j=0;
			} else {
				$PRODXDAYStot=$day_num; //Stop
			}
		}
	}
} //multi

$data[0] = array('name' => "empty",'data' => null, 'animation' => false);

$i=1;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { // Multi
	sort($stack[$invtnum]);
	$data[$i] = array('name' => "$lgINVT$invtnum",'data' => $stack[$invtnum], 'animation' => false);
	$i++;
}// multi

header("Content-type: text/json");
echo json_encode($data);
?>
