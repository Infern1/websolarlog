<?php
$_SESSION['log'] = array();
$_SESSION['log']['startTime'] = microtime(true);
$_SESSION['log']['api']['start'] = microtime(true);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'classes/classloader.php';
// time it took to load the classloader:
$_SESSION['log']['api']['afterClassLoader'] = (microtime(true) - $_SESSION['log']['startTime']);

Session::initializeLight();
// time it took to load initLightx:
$_SESSION['log']['api']['afterLightInit'] = (microtime(true) - $_SESSION['log']['startTime']);

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json; charset=utf-8');


$result = array();
try {
	$result = ApiController::getInstance()->route();
	
	// time it took for the route routine to run:
	$_SESSION['log']['api']['AfterRoute()'] = (microtime(true) - $_SESSION['log']['startTime']);
} catch (SetupException $e) {
	$result = $e->getJSONMessage();
} catch (Exception $e) {        // Skipped
    $msg = $e->getMessage() . " in file " . $e->getFile() . '[' . $e->getLine() . ']';
    $result = array('result'=>'error', 'success'=>false, 'exception'=>get_class($e), 'message'=>$msg);
}
// total time for the API run:
$_SESSION['log']['api']['apiTime'] = (microtime(true) - $_SESSION['log']['api']['start']);


foreach($_SESSION['log']  as $key=>$value){
	if(is_array($value)){
		foreach($value  as $logKey=>$logValue){
			if(isset($backupValue)){
				$diff = (float)$logValue - (float)$backupValue;
				if($diff <= 0.0001){
					$diffText = 'to small';
				}
				if($diff>= 0.5){
					$diffText ='<<<<<';
				}
			}else{
				$diff = 0;
				$diffText ='equal';
			}
			$log[]= array("key"=>$key."-".$logKey,"value"=>$logValue,"diff"=>$diff,"diffText"=>$diffText);
	
			$backupKey = $logKey;
			$backupValue = $logValue;
		}
	}else{
			if(isset($backupValue)){
				$diff = (float)$value - (float)$backupValue;
				if($diff <= 0.0001){
					$diffText = 'to small';
				}
				if($diff>= 0.5){
					$diffText ='<<<<<';
				}
			}else{
				$diff = 0;
				$diffText ='equal';
			}
			$log[]= array("key"=>$key,"value"=>$value,"diff"=>$diff,"diffText"=>$diffText);
		
			$backupKey = $key;
			$backupValue = $value;
		
	}
}
$util = new Util();
$log = $util->aasort($log,'value');

$result['log'] = $log;

echo json_encode($result);
?>