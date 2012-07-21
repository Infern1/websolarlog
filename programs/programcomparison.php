<?php
// Credit Louviaux Jean-Marc 2012
date_default_timezone_set('GMT');
define('checkaccess', TRUE);

$invtnum = $_GET['invtnum'];
if (isset($_COOKIE['user_lang'])){
    $user_lang=$_COOKIE['user_lang'];
} else {
    $user_lang="English";
};
include("../languages/".$user_lang.".php");

if (!empty ($_GET['whichmonth'])) {
    $whichmonth= $_GET['whichmonth'];
} else { $whichmonth=date("n");
}
if (!empty ($_GET['whichyear'])) {
    $whichyear= $_GET['whichyear'];
} else { $whichyear= date("Y");
}
if (!empty ($_GET['comparemonth'])) {
    $comparemonth= $_GET['comparemonth'];
} else { $comparemonth=date("n");
}
if (!empty ($_GET['compareyear'])) {
    $compareyear= $_GET['compareyear'];
} else { $compareyear= "expected";
}

function tricsv($var) {
    return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}

function getvalues($selectmonth,$selectyear,$invtnum)
{
    include('../config/config_main.php');
    if($invtnum==0) { //all
        $startinv=1;
        $uptoinv=$NUMINV;
    } else {
        $startinv=$invtnum;
        $uptoinv=$invtnum;
    }

    for ($invt_num=$startinv;$invt_num<=$uptoinv;$invt_num++) {  // Multi
        $config_invt="../config/config_invt".$invt_num.".php";
        include("$config_invt");

        $dir = '../data/invt'.$invt_num.'/production/';
        $thefile=file($dir."energy".$selectyear.".csv");
        $contalines = count($thefile);

        $i=0;
        for ($line_num=0;$line_num<$contalines;$line_num++) {
            $array = explode(";", $thefile[$line_num]);

            $year=$selectyear;
            $month = substr($array[0], 4, 2);
            $day = substr($array[0], 6, 2);

            if ($month==$selectmonth){
                $array[1] = str_replace(",", ".", $array[1]);
                $date1 = strtotime ($year."-".$month."-".$day);
                $date1 = $date1 *1000; // in ms
                $month = (int)($month);
                $day=(int)($day);
                $prod_day[$invt_num][$month][$day]= round(($array[1]*$CORRECTFACTOR),1);
                $stack[$i] = array ($date1, $cumu_prod);
                $i++;
            }
        } // end of looping through the file

        if ($selectyear==date("Y")&& $selectmonth==date("n")) { // Add today prod
            $dir = '../data/invt'.$invt_num.'/csv';
            $output2 = scandir($dir);
            $output2 = array_filter($output2, "tricsv");
            sort($output2);
            $cnt=count($output2);
            $lines=file($dir."/".$output2[$cnt-1]);

            $contalines = count($lines);
            $array = explode(";", $lines[0]);
            $array[14] = str_replace(",", ".", $array[14]);
            $array2 = explode(";", $lines[$contalines-1]);
            $array2[14] = str_replace(",", ".", $array2[14]);
            $year = substr($array[0], 0, 4);
            $month = substr($array[0], 4, 2);
            $day = substr($array[0], 6, 2);
            $date1 = strtotime ($year."-".$month."-".$day);
            $date1 = $date1 *1000; // in ms
            $month=(int)($month);
            $day=(int)($day);
            $prod_day[$invt_num][$month][$day]= round((($array2[14]-$array[14])*$CORRECTFACTOR),1);
            $stack[$i] = array ($date1, $prod_day[$invt_num][$month][$day]);
            $i++;
        } // end of today prod

        // Fill blanks dates
        $daythatm = cal_days_in_month(CAL_GREGORIAN, $selectmonth, $selectyear);
        $month_len=count($prod_day[$selectmonth]);

        if ($month_len < $daythatm ) {
            for ($j=1;$j<=$daythatm;$j++) {
                if(!isset($prod_day[$selectmonth][$j])) {
                    $date1 = strtotime ($selectyear."-".$selectmonth."-".$j);
                    $date1 = $date1 *1000;
                    $prod_day[$invt_num][$whichmonth][$j]=0;
                    $stack[$i] = array ($date1, $prod_day[$selectmonth][$j]);
                    $i++;
                }
            }
        }
    } // end of multi

    $startdate=strtotime ($selectyear."-".$selectmonth."-01");
    $startdate=$startdate*1000;
    for ($j=0;$j<$daythatm;$j++) { // Cumulative
        for ($invt_num=$startinv;$invt_num<=$uptoinv;$invt_num++) { // Multi
            $cumu_prod=$cumu_prod+$prod_day[$invt_num][$selectmonth][$j+1];
        }
        $stack_ret[$j]= array ($startdate,$cumu_prod);
        $startdate=$startdate+86400000;//next day
    }

    return $stack_ret;
} // enf of fnct getvalues

$datareturn= getvalues($whichmonth,$whichyear,$invtnum); // Call fnct

if ($compareyear==$whichyear && $comparemonth==$whichmonth) { //Same req
    $datareturn2=$datareturn;
    $xaxe=0;

}else {

    if ($compareyear!="expected") {  // Compare with
        $datareturn2= getvalues($comparemonth,$compareyear,$invtnum);
        $xaxe=1;
    } else { // Expected

        include('../config/config_main.php');
        if($invtnum==0) {
            $startinv=1;
            $uptoinv=$NUMINV;
        } else {
            $startinv=$invtnum;
            $uptoinv=$invtnum;
        }

        $compareyear=$lgPRODTOOLTIPEXPECTED; //name
        for ($invt_num=$startinv;$invt_num<=$uptoinv;$invt_num++) {  // Multi
            $config_invt="../config/config_invt".$invt_num.".php";
            include("$config_invt");
            $EXPECTEDPROD=$EXPECTEDPROD/100;
            $prod_exp[1]=($EXPECTJAN*$EXPECTEDPROD)+$prod_exp[1];
            $prod_exp[2]=($EXPECTFEB*$EXPECTEDPROD)+$prod_exp[2];
            $prod_exp[3]=($EXPECTMAR*$EXPECTEDPROD)+$prod_exp[3];
            $prod_exp[4]=($EXPECTAPR*$EXPECTEDPROD)+$prod_exp[4];
            $prod_exp[5]=($EXPECTMAY*$EXPECTEDPROD)+$prod_exp[5];
            $prod_exp[6]=($EXPECTJUN*$EXPECTEDPROD)+$prod_exp[6];
            $prod_exp[7]=($EXPECTJUI*$EXPECTEDPROD)+$prod_exp[7];
            $prod_exp[8]=($EXPECTAUG*$EXPECTEDPROD)+$prod_exp[8];
            $prod_exp[9]=($EXPECTSEP*$EXPECTEDPROD)+$prod_exp[9];
            $prod_exp[10]=($EXPECTOCT*$EXPECTEDPROD)+$prod_exp[10];
            $prod_exp[11]=($EXPECTNOV*$EXPECTEDPROD)+$prod_exp[11];
            $prod_exp[12]=($EXPECTDEC*$EXPECTEDPROD)+$prod_exp[12];
        } // end of multi
        $startdate=strtotime ($whichyear."-".$comparemonth."-01");
        $startdate=$startdate*1000;
        $daythatm = cal_days_in_month(CAL_GREGORIAN, $comparemonth, $whichyear);
        $stopdate=strtotime ($whichyear."-".$comparemonth."-".$daythatm);
        $stopdate=$stopdate*1000;
        $prodinexpday=round(($prod_exp[$comparemonth]/$daythatm),1);
        for ($j=0;$j<$daythatm;$j++) {
            $cumu_prod2=$cumu_prod2+$prodinexpday;
            $datareturn2[$j]= array ($startdate,$cumu_prod2);
            $startdate=$startdate+86400000;//next day
        }

        if ($comparemonth==$whichmonth) {
            $xaxe=0;
        } else {
            $xaxe=1;
        }
    }

} // end of same req

$data = array(
        0 => array(
                'name' => "$lgSMONTH[$whichmonth] $whichyear",
                'type'=> 'areaspline',
                'data' => $datareturn,
                'xAxis' => 0
        ),
        1 => array(
                'name' => "$lgSMONTH[$comparemonth] $compareyear",
                'type'=> 'spline',
                'data' => $datareturn2,
                'xAxis' => $xaxe
        )
);

header("Content-type: text/json");
echo json_encode($data);
?>
