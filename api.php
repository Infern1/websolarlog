<?php
session_start();
$_SESSION['timers'] = '';
$_SESSION['timerBegin'] = microtime(true);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'classes/classloader.php';
Session::initializeLight();

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$result = array();
try {
        $_SESSION['timers']['ApiBeforeRoute()'] =(microtime(true) - $_SESSION['timerBegin'] );
        $result = ApiController::getInstance()->route();
        $_SESSION['timers']['ApiAfterRoute()'] =(microtime(true)-$_SESSION['timerBegin']);
} catch (SetupException $e) {
        $result = $e->getJSONMessage();
} catch (Exception $e) {        // Skipped
    $msg = $e->getMessage() . " in file " . $e->getFile() . '[' . $e->getLine() . ']';
    $result = array('result'=>'error', 'success'=>false, 'exception'=>get_class($e), 'message'=>$msg);
}
$_SESSION['timers']['ApiBeforeJSONgeneration']=(microtime(true)-$_SESSION['timerBegin'] );
//echo $timers[count($timers)]['elapsed'];
echo json_encode($result);
$_SESSION['timers']['ApiAfterJSONGeneration()'] =(microtime(true)-$_SESSION['timerBegin'] );

if($_GET['showTimers']){
	echo "\r\n";
	foreach($_SESSION['timers']  as $key=>$value){
	        if(isset($backupValue)){
	                $diff = $value - $backupValue;
	                if($diff < 0.0001){
	                        $diff = 'to small';
	                }
	                if($diff> 0.5){
	                        $diff.='<<<<<';
	                }
	        }else{
	                $diff = 0;
	        }
	        echo "\r\n".$key.": ".$value." (diff:".$diff.")\r\n";
	
	        $backupKey = $key;
	        $backupValue = $value;
	}
}
?>

