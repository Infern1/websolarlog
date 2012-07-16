<?php 
// Credit Louviaux Jean-Marc 2012
date_default_timezone_set('GMT');
  if (isset($_COOKIE['user_lang'])){
  $user_lang=$_COOKIE['user_lang'];
  } else {
  $user_lang="English";  
  };
include("../languages/".$user_lang.".php");
$rowstoreturn=0;
$nbryaxis=0;

if (!empty ($_GET['date1'])) { $date1 = $_GET['date1']; } else { $date1=FALSE; }
if (!empty ($_GET['checkpower'])) { $checkpower = $_GET['checkpower']; $rowstoreturn++;} else { $checkpower=FALSE; }
if (!empty ($_GET['checkavgpower'])) { $checkavgpower = $_GET['checkavgpower']; $rowstoreturn++;} else { $checkavgpower=FALSE; }
if (!empty ($_GET['checkI1V'])) { $checkI1V = $_GET['checkI1V']; $rowstoreturn++;} else { $checkI1V=FALSE; }
if (!empty ($_GET['checkI1A'])) { $checkI1A = $_GET['checkI1A']; $rowstoreturn++;} else { $checkI1A=FALSE; }
if (!empty ($_GET['checkI1P'])) { $checkI1P = $_GET['checkI1P']; $rowstoreturn++;} else { $checkI1P=FALSE; }
if (!empty ($_GET['checkI2V'])) { $checkI2V = $_GET['checkI2V']; $rowstoreturn++;} else { $checkI2V=FALSE; }
if (!empty ($_GET['checkI2A'])) { $checkI2A = $_GET['checkI2A']; $rowstoreturn++;} else { $checkI2A=FALSE; }
if (!empty ($_GET['checkI2P'])) { $checkI2P = $_GET['checkI2P']; $rowstoreturn++;} else { $checkI2P=FALSE; }
if (!empty ($_GET['checkGV'])) { $checkGV = $_GET['checkGV']; $rowstoreturn++;} else { $checkGV=FALSE; }
if (!empty ($_GET['checkGA'])) { $checkGA = $_GET['checkGA']; $rowstoreturn++;} else { $checkGA=FALSE; }
if (!empty ($_GET['checkGP'])) { $checkGP = $_GET['checkGP']; $rowstoreturn++;} else { $checkGP=FALSE; }
if (!empty ($_GET['checkFRQ'])) { $checkFRQ = $_GET['checkFRQ']; $rowstoreturn++;} else { $checkFRQ=FALSE; }
if (!empty ($_GET['checkEFF'])) { $checkEFF = $_GET['checkEFF']; $rowstoreturn++;} else { $checkEFF=FALSE; }
if (!empty ($_GET['checkINVT'])) { $checkINVT = $_GET['checkINVT']; $rowstoreturn++;} else { $checkINVT=FALSE; }
if (!empty ($_GET['checkBOOT'])) { $checkBOOT = $_GET['checkBOOT']; $rowstoreturn++;} else { $checkBOOT=FALSE; }


$invtnum = $_GET['invtnum'];

$dir = '../data/invt'.$invtnum.'/csv/';
$log=$dir.$date1;
$lines = file($log); 
$contalines = count($lines);

foreach ($lines as $line_num => $line) {
$array = preg_split("/;/",$line);

$SDTE[$line_num]=$array[0];
$I1V=str_replace(",", ".",$array[1]);
$I1A=str_replace(",", ".",$array[2]);
$I1P=str_replace(",", ".",$array[3]);
$I2V=str_replace(",", ".",$array[4]);
$I2A=str_replace(",", ".",$array[5]);
$I2P=str_replace(",", ".",$array[6]);
$GV=str_replace(",", ".",$array[7]);
$GA=str_replace(",", ".",$array[8]);
$GP=str_replace(",", ".",$array[9]);
$FRQ=str_replace(",", ".",$array[10]);
$EFF=str_replace(",", ".",$array[11]);
$INVT=str_replace(",", ".",$array[12]);
$BOOT=str_replace(",", ".",$array[13]);
$KWHT[$line_num]=str_replace(",", ".",$array[14]);

$year = substr($SDTE[$line_num], 0, 4);
$month = substr($SDTE[$line_num], 4, 2);
$day = substr($SDTE[$line_num], 6, 2);
$hour = substr($SDTE[$line_num], 9, 2);
$minute = substr($SDTE[$line_num], 12, 2);
$seconde = substr($SDTE[$line_num], 15, 2);

$epochdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);

$UTCdate = $epochdate *1000; // in ms

$countstack=0;
 
// Count the numbers of Yaxis
if ($checkpower==TRUE) {
$POW=round(((($I1P+$I2P)*$EFF)/100),1);
$stackname[$countstack]=$lgDPOWERINSTANT;
$stack[$countstack][$line_num] = array ($UTCdate, $POW);
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkavgpower==TRUE) {
  if ($line_num>0)  {
  $pastline_num=$line_num-1;
  $pastyear = substr($SDTE[$pastline_num], 0, 4);
  $pastmonth = substr($SDTE[$pastline_num], 4, 2);
  $pastday = substr($SDTE[$pastline_num], 6, 2);
  $pasthour = substr($SDTE[$pastline_num], 9, 2);
  $pastminute = substr($SDTE[$pastline_num], 12, 2);
  $pastseconde = substr($SDTE[$pastline_num], 15, 2);
  //calculate average Power between 2 pooling
  $diffUTCdate = strtotime ($pastyear."-".$pastmonth."-".$pastday." ".$pasthour.":".$pastminute.":".$pastseconde);
  $diffTime=$epochdate-$diffUTCdate;
  $AvgPOW=round((((($KWHT[$line_num]-$KWHT[$pastline_num])*3600)/$diffTime)*1000),1);
  } else {
  $AvgPOW=0;
  }
$stackname[$countstack]=$lgDPOWERAVG;
$stack[$countstack][$line_num] = array ($UTCdate, $AvgPOW);
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkI1P==TRUE) {
$stackname[$countstack]=$lgDPOWER1;
$stack[$countstack][$line_num] = array ($UTCdate, round($I1P,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkI2P==TRUE) {
$stackname[$countstack]=$lgDPOWER2;
$stack[$countstack][$line_num] = array ($UTCdate, round($I2P,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkGP==TRUE) {
$stackname[$countstack]="Grid Power";
$stack[$countstack][$line_num] = array ($UTCdate, round($GP,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}

if ($checkpower==TRUE || $checkavgpower==TRUE || $checkI1P==TRUE || $checkI2P==TRUE || $checkGP==TRUE) {$nbryaxis++;}

if ($checkI1V==TRUE) {
$stackname[$countstack]=$lgDVOLTAGE1;
$stack[$countstack][$line_num] = array ($UTCdate, round($I1V,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkI2V==TRUE) {
$stackname[$countstack]=$lgDVOLTAGE2;
$stack[$countstack][$line_num] = array ($UTCdate, round($I2V,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkGV==TRUE) {
$stackname[$countstack]=$lgDGRIDVOLTAGE;
$stack[$countstack][$line_num] = array ($UTCdate, round($GV,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}

if ($checkI1V==TRUE || $checkI2V==TRUE || $checkGV==TRUE) {$nbryaxis++;}

if ($checkI1A==TRUE) {
$stackname[$countstack]=$lgDCURRENT1;
$stack[$countstack][$line_num] = array ($UTCdate, round($I1A,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkI2A==TRUE) {
$stackname[$countstack]=$lgDCURRENT2;
$stack[$countstack][$line_num] = array ($UTCdate, round($I2A,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkGA==TRUE) {
$stackname[$countstack]=$lgDGRIDCURRENT;
$stack[$countstack][$line_num] = array ($UTCdate, round($GA,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}

if ($checkI1A==TRUE || $checkI2A==TRUE || $checkGA==TRUE) {$nbryaxis++;} 

if ($checkFRQ==TRUE) {
$stackname[$countstack]=$lgDFREQ;
$stack[$countstack][$line_num] = array ($UTCdate, round($FRQ,2));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$nbryaxis++;
$countstack++;
}
if ($checkEFF==TRUE) {
$stackname[$countstack]=$lgDEFFICIENCY;
$stack[$countstack][$line_num] = array ($UTCdate, round($EFF,1));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$nbryaxis++;
$countstack++;
}

if ($checkINVT==TRUE) {
$stackname[$countstack]=$lgDINVERTERTEMP;
$stack[$countstack][$line_num] = array ($UTCdate, round($INVT,1));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}
if ($checkBOOT==TRUE) {
$stackname[$countstack]=$lgDBOOSTERTEMP;
$stack[$countstack][$line_num] = array ($UTCdate, round($BOOT,1));
$yaxis[$countstack]=$nbryaxis;
$dashStyle[$countstack]="Solid";
$countstack++;
}

$nbryaxis=0;
} // End of foreach

// Return datas via json
for ($i=0;$i<$rowstoreturn;$i++) {
$data[$i] = array('name' => $stackname[$i],'data' => $stack[$i], 'yAxis' => $yaxis[$i], 'dashStyle' => $dashStyle[$i]);
}

header("Content-type: text/json");
echo json_encode($data);
?>
