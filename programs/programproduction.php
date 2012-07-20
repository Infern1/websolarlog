<?php 
// Credit Louviaux Jean-Marc 2012
date_default_timezone_set('GMT');
define('checkaccess', TRUE);
include('../config/config_main.php');

$invtnum = $_GET['invtnum'];
if (isset($_COOKIE['user_lang'])){
	$user_lang=$_COOKIE['user_lang'];
} else {
	$user_lang="English";
};
include("../languages/".$user_lang.".php");

if (!empty ($_GET['whichyear'])) {
	$whichyear= $_GET['whichyear'];
} else { $whichyear= date("Y");
}
if (!empty ($_GET['compare'])) {
	$compare = $_GET['compare'];
} else { $compare="";
}

function tricsv($var) {
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

if($invtnum==0) {
	$startinv=1;
	$uptoinv=$NUMINV;
} else {
	$startinv=$invtnum;
	$uptoinv=$invtnum;
}

if ($compare=="expected") {
	$upto=24;
} else {
	$upto=12;
}

for ($invtnum=$startinv;$invtnum<=$uptoinv;$invtnum++) {  // Multi
	$config_invt="../config/config_invt".$invtnum.".php";
	include("$config_invt");
	$dir = '../data/invt'.$invtnum.'/production/';
	$thefile=file($dir."energy".$whichyear.".csv");
	$contalines = count($thefile);
	//$h=0;

	if (file_exists($dir."energy".$whichyear.".csv")) { // First ever startup
		for ($line_num=0;$line_num<$contalines;$line_num++) {
			$array = preg_split("/;/",$thefile[$line_num]);
			$year = substr($array[0], 0, 4);
			$month = substr($array[0], 4, 2);
			$day = substr($array[0], 6, 2);
			$array[1] = str_replace(",", ".", $array[1]);
			$KWHT[$line_num]=$array[1]*$CORRECTFACTOR;
			$date1 = strtotime ($year."-".$month."-".$day); // Convert to epoch date
			if ($compare=="expected") {
				$month = (int)(($month-1)*2);
			} else {
				$month = (int)($month-1);
			}
			$day=(int)($day);
			$subcategories[$month][$day]= date("D j M", $date1);
			$prod_day[$invtnum][$month][$day]= round($KWHT[$line_num],1);
		} // end of looping through the file
	}

	if ($whichyear==date("Y")) { // Add today prod
		$dir = '../data/invt'.$invtnum.'/csv';
		$output2 = scandir($dir);
		$output2 = array_filter($output2, "tricsv");
		sort($output2);
		$cnt=count($output2);
		$lines=file($dir."/".$output2[$cnt-1]);
		$contalines = count($lines);
		$array = preg_split("/;/",$lines[0]);
		$array[14] = str_replace(",", ".", $array[14]);
		$array2 = preg_split("/;/",$lines[$contalines-1]);
		$array2[14] = str_replace(",", ".", $array2[14]);
		$year = substr($array[0], 0, 4);
		$month = substr($array[0], 4, 2);
		$day = substr($array[0], 6, 2);
		$date1 = strtotime ($year."-".$month."-".$day);
		if ($compare=="expected") {
			$month = (int)(($month-1)*2);
		} else {
			$month = (int)($month-1);
		}
		$day=(int)($day);
		$subcategories[$month][$day]= date("D j M", $date1);
		$date2 = strtotime(date("Ymd"));
		if ($date1==$date2) { //add if good day{
			$prod_day[$invtnum][$month][$day]= round((($array2[14]-$array[14])*$CORRECTFACTOR),1);
		} else {
			$prod_day[$invtnum][$month][$day]=0;
		}
	} // end of today prod

	// Fill blanks dates
	$stmonth=1;
	for ($h=0;$h<$upto;$h++) {
		$daythatm = cal_days_in_month(CAL_GREGORIAN, $stmonth, $year);
		$month_len=count($prod_day[$invtnum][$h]);
		if ($month_len < $daythatm ) {
			for ($i=1;$i<=$daythatm;$i++)
				if(!isset($prod_day[$invtnum][$h][$i])) {
				$date1 = strtotime ($year."-".$stmonth."-".$i);
				$subcategories[$h][$i]= date("D j M", $date1);
				$prod_day[$invtnum][$h][$i]=0;
			}
		}
		if ($compare=="expected") {
			$h++;
		}
		$stmonth++;
	}

	if ($compare=="expected") {  // Expected
		$EXPECTEDPROD=$EXPECTEDPROD/100;
		$prod_month[1]=round(($EXPECTJAN*$EXPECTEDPROD),0)+$prod_month[1];
		$prod_month[3]=round(($EXPECTFEB*$EXPECTEDPROD),0)+$prod_month[3];
		$prod_month[5]=round(($EXPECTMAR*$EXPECTEDPROD),0)+$prod_month[5];
		$prod_month[7]=round(($EXPECTAPR*$EXPECTEDPROD),0)+$prod_month[7];
		$prod_month[9]=round(($EXPECTMAY*$EXPECTEDPROD),0)+$prod_month[9];
		$prod_month[11]=round(($EXPECTJUN*$EXPECTEDPROD),0)+$prod_month[11];
		$prod_month[13]=round(($EXPECTJUI*$EXPECTEDPROD),0)+$prod_month[13];
		$prod_month[15]=round(($EXPECTAUG*$EXPECTEDPROD),0)+$prod_month[15];
		$prod_month[17]=round(($EXPECTSEP*$EXPECTEDPROD),0)+$prod_month[17];
		$prod_month[19]=round(($EXPECTOCT*$EXPECTEDPROD),0)+$prod_month[19];
		$prod_month[21]=round(($EXPECTNOV*$EXPECTEDPROD),0)+$prod_month[21];
		$prod_month[23]=round(($EXPECTDEC*$EXPECTEDPROD),0)+$prod_month[23];
	}
} // End of multi

for ($invtnum=$startinv;$invtnum<=$uptoinv;$invtnum++) { // Multi
	for ($h=0;$h<$upto;$h++) {
		$prod_month[$h]=array_sum($prod_day[$invtnum][$h])+$prod_month[$h];
		if ($compare=="expected") {
			$h++;
		}
	}
} // End of multi
for ($h=0;$h<$upto;$h++) {
	$prod_year=$prod_month[$h]+$prod_year;  // Year prod
	if ($compare=="expected") {
		$h++;
	}
}

$i=1;
for ($h=0;$h<$upto;$h++) {  // Rearrange 'em all
	$cnt=count($subcategories[$h]);
	$k=0;
	for($j=1;$j<=$cnt;$j++) {
		$subcategories2[$k]=$subcategories[$h][$j];
		for ($invtnum=$startinv;$invtnum<=$uptoinv;$invtnum++) { // Multi
			$prod_day2[$k]=$prod_day[$invtnum][$h][$j]+$prod_day2[$k];
		} //  End of multi
		$k++;
	}
	$drilldown[$h] = array('name' => "$lgSMONTH[$i] $whichyear ($prod_month[$h]kWh)", 'color' => '#4572A7', 'categories' => $subcategories2, 'data' => $prod_day2);
	$subcategories2 =array(); // emtpy
	$prod_day2=array();
	$categories[$h]="$lgSMONTH[$i] $whichyear";
	$i++;
	if ($compare=="expected") {
		$h++;
	}
}

for ($h=0;$h<$upto;$h++) {
	$jsonreturn2[$h] = array('y' => $prod_month[$h], 'color' => '#4572A7', 'drilldown' => $drilldown[$h]);
	if ($compare=="expected") {
		$h++;
	}
}

if ($compare=="expected") {
	$i=1;
	for ($h=1;$h<24;$h++) {
		$categories[$h]=$lgSMONTH[$i];
		$jsonreturn2[$h] = array('name' => $categories[$i], 'y' => $prod_month[$h], 'color' => '#89A54E');
		$h++;
		$i++;
	}
}
ksort($categories);
ksort($jsonreturn2);

$jsonreturn = array('categories' => $categories, 'name' => 'Months', 'color' => 'colors[0]', 'data' => $jsonreturn2, 'prod_y' => $prod_year);

header("Content-type: text/json");
echo json_encode($jsonreturn);
?>
