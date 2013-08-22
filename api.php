<?php
session_start();
$_SESSION['timers'] = '';
$_SESSION['timerBegin'] = microtime(true);
$_SESSION['timers']['StartTimers'] =$_SESSION['timerBegin'];
error_reporting(E_ALL);
ini_set('display_errors', 'On');
$_SESSION['timers']['After_ErrorInits'] =(microtime(true) - $_SESSION['timerBegin'] );

require 'classes/classloader.php';
$_SESSION['timers']['After_classloader'] =(microtime(true) - $_SESSION['timerBegin'] );
Session::initializeLight();
$_SESSION['timers']['After_initializeLight'] =(microtime(true) - $_SESSION['timerBegin'] );

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$_SESSION['timers']['After_HeaderInits'] =(microtime(true) - $_SESSION['timerBegin'] );


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

if(isset($_GET['showTimers'])){
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

