<?php
// Credit Louviaux Jean-Marc 2012
define('checkaccess', TRUE);
include('../config/config_main.php');
include('../classes/Formulas.php');
date_default_timezone_set('GMT');

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { // Multi
	include('../config/config_invt'.$invtnum.'.php');

	$dir = '../data/invt'.$invtnum.'/csv';
	$output = scandir($dir);
	$output = array_filter($output, "tricsv");
	sort($output);
	$cnt=count($output)-1;

	$lines=file($dir."/".$output[$cnt]);
	$contalines = count($lines);
	$filedate[$invtnum] = substr($output[$cnt], 0, 8);
	rsort($filedate);
	// Loop through the array
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

		// Convert to epoch date
		$UTC = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);

		//calculate average Power between 2 pooling, more precise
		$diffUTCdate = strtotime ($pastyear."-".$pastmonth."-".$pastday." ".$pasthour.":".$pastminute.":".$pastseconde);
		$diffTime=$UTC-$diffUTCdate;
		$UTCdate[$invtnum] = $UTC *1000;

		if ($diffTime!=0) {
			//AveragePOWer = ((KiloWattHourTime[currentline] - KiloWattHourTime[last add line](give timediff in sec) *3600 (to hour) / $difftime) * 1000 (watt??)), round by 1 decimal)
			// ^averagepower over a given time.
		    $AvgPOW = Formulas::calcAveragePower($KWHT[$pastline_num], $KWHT[$line_num], $diffTime);
			if($filedate[$invtnum]>$filedate[0]) { // newer
				$MaxPow=0;
				$MaxTime=0;
			}
			$CumPOW[$hour.$minute]=$AvgPOW[$invtnum]+$CumPOW[$hour.$minute];
			if ($CumPOW[$hour.$minute]>=$MaxPow && $line_num<$contalines) { // Past maximum
				$MaxPow=$CumPOW[$hour.$minute];
				$MaxTime=$UTCdate[$invtnum];
			}
		} else {
			$AvgPOW[$invtnum]=0;
		}
	} // end of looping

	// Updating title
	$arrayT = explode(";",$lines[0]);
	$arrayT[14] = str_replace(',', '.', $arrayT[14]);
	$arrayT2 = explode(";",$lines[$contalines-1]);
	$arrayT2[14] = str_replace(',', '.', $arrayT2[14]);
	$KWHD[$invtnum] = Formulas::calcKiloWattHourDay($arrayT[14], $arrayT2[14], $CORRECTFACTOR);
} // end of multi

$j=0;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { // Multi
	if($filedate[$j]==$filedate[0]) { // skip older files
		$totAvgPOW=$totAvgPOW+$AvgPOW[$invtnum];
		$KWHDTOT=$KWHD[$invtnum]+$KWHDTOT;
		$totTime= (strtotime ($year."-".$month."-".$day." ".$hour.":".$minute)*1000);
	}
	$j++;
} // multi
$LastValue=$totAvgPOW;
$LastTime=$totTime;

$KWHDTOT= round($KWHDTOT,1);
$PTITLE="($KWHDTOT kWh)";

$data[0]=array('totTime'=> $totTime, 'totValue'=>$totAvgPOW);

$j=0;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { // Multi
	if($filedate[$j]==$filedate[0]) {
		$data[$invtnum] = array('x' => $UTCdate[$invtnum] ,'y' => $AvgPOW[$invtnum]);
	}
	$j++;
} // multi
$j++;
$data[$j] = array('MaxTime' => $MaxTime, 'MaxPow' => $MaxPow);
$j++;
$data[$j] = array('LastTime' => $LastTime, 'LastValue' => $LastValue);
$j++;
$data[$j] = array('PTITLE' => $PTITLE);

header("Content-type: text/json");
echo json_encode($data);
?>
