<?php
define('checkaccess', TRUE);
include("config/config_main.php");

// Retrieve action params
$method = $_GET['method'];

// Detect en load the current user_lang
if (!empty ($_POST['user_lang'])) {
    setcookie('user_lang',$_POST['user_lang'],strtotime('+5 year'));
    $user_lang=$_POST['user_lang'];
} elseif (isset($_COOKIE['user_lang'])){
    $user_lang=$_COOKIE['user_lang'];
} else {
    $user_lang="English";
}
include("languages/".$user_lang.".php");

// Detect the current user style
if (!empty ($_POST['user_style'])) {
    setcookie('user_style',$_POST['user_style'],strtotime('+5 year'));
    $user_style=$_POST['user_style'];
} elseif (isset($_COOKIE['user_style'])){
    $user_style=$_COOKIE['user_style'];
} else {
    $user_style="default";
}

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// Initialize return array
$data = array();

switch ($method) {
    case 'getLanguages':
        $languages = array();
        if ($handle = opendir('languages')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." ) {
                    $languages[] = str_replace(".php", "", $entry);
                }
            }
        }
        $data['languages'] = $languages;
        $data['currentlanguage'] = $user_lang;
        break;
    case 'getMenu':
        // TODO :: Move to json file or something???
        $menu = array();
        $menu[] = array( "url" => "index.php", "title" => $lgMINDEX);
        $menu[] = array( "url" => "indexdetailed.php", "title" => $lgMDETAILED);
        $menu[] = array( "url" => "indexproduction.php", "title" => $lgMPRODUCTION);
        $menu[] = array( "url" => "indexcomparison.php", "title" => $lgMCOMPARISON);
        $menu[] = array( "url" => "indexinfo.php", "title" => $lgMINFO);
        $data['menu'] = $menu;
        break;
    case 'getEvents':
        $invtnum = $_GET['invtnum'];
        $filename="data/invt$invtnum/infos/events.txt";
        $handle = fopen($filename, "r");
        $contents = explode("\n", fread($handle, filesize($filename)));
        fclose($handle);
        $data['events'] = $contents;
        break;
    case 'getLiveData':
    	// TODO :: Clean/shorten code
        $invtnum = $_GET['invtnum'];
        $filename="data/invt$invtnum/infos/live.txt";
        $handle = fopen($filename, "r");
        $contents = explode("\n", fread($handle, filesize($filename)));
        fclose($handle);
        var_dump($contents);
        
        //$config_invt="config/config_invt".$invtnum.".php";
        //include("$config_invt");
        //$dir = '../data/invt'.$invtnum.'/infos';
        //$livedash=file($dir."/live.txt");
        //echo $livedash;
        $array = preg_split("/;/",$livedash[0]);
        
        /*
         * Remove the vars and place the substr() directly in the strtotime() ???
         */
        $year = substr($array[0], 0, 4);
        $month = substr($array[0], 4, 2);
        $day = substr($array[0], 6, 2);
        $hour = substr($array[0], 9, 2);
        $minute = substr($array[0], 12, 2);
        $seconde = substr($array[0], 15, 2);
        
        $UTCdate = strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);
        
        /*
         * create routine for str_replace ???
         */
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
        $parray = preg_split("/;/",$pmaxotd[0]);
        $pmax=round($parray[1],0);
        $hour = substr($parray[0], 9, 2);
        $minute = substr($parray[0], 12, 2);
        
        
        $liveData = array();
        $liveData[] = array( "title" => "SDTE", "value" => UTCdate*1000);
        $liveData[] = array( "title" => "I1V",  "value" => floatval(round($array[1],2)));
        $liveData[] = array( "title" => "I1A",  "value" => floatval(round($array[2],2)));
        $liveData[] = array( "title" => "I1P",  "value" => floatval(round($array[3],2)));
        $liveData[] = array( "title" => "I2V",  "value" => floatval(round($array[4],2)));
        $liveData[] = array( "title" => "I2A",  "value" => floatval(round($array[5],2)));
        $liveData[] = array( "title" => "I2P",  "value" => floatval(round($array[6],2)));
        $liveData[] = array( "title" => "GV",   "value" => floatval(round($array[7],2)));
        $liveData[] = array( "title" => "GA",   "value" => floatval(round($array[8],2)));
        $liveData[] = array( "title" => "GP",   "value" => floatval($array[9]));
        $liveData[] = array( "title" => "FRQ",  "value" => floatval(round($array[10],2)));
        $liveData[] = array( "title" => "EFF",  "value" => floatval(round($array[11],2)));
        $liveData[] = array( "title" => "INVT", "value" => floatval(round($array[12],1)));
        $liveData[] = array( "title" => "BOOT", "value" => floatval(round($array[13],1)));
        $liveData[] = array( "title" => "KHWT", "value" => floatval($array[14]));
        $liveData[] = array( "title" => "PMAXOTD", "value" => floatval($pmax));
        $liveData[] = array( "title" => "PMAXOTDTIME", "value" => ($hour.":".$minute));
        
        $data['liveData'] = $liveData;
        break;
    default:
        break;
}

try {
    echo json_encode($data);
} catch (Exception $e) {
    echo "error: <br/>" . $e->getMessage() ;
}
?>