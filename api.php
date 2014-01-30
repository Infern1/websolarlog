<?php

$_SESSION['logId'.$_SESSION['logId']] = null;
$_SESSION['logId'.$_SESSION['logId']] = array();

$logId = rand(100, 99999);
$_SESSION['logId'] = $logId;
$_SESSION['logId'.$_SESSION['logId']]['startTime'] = microtime(true);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'classes/classloader.php';
// time it took to load the classloader:
$_SESSION['logId'.$_SESSION['logId']][]['api.afterClassLoader'] = (microtime(true) - $_SESSION['logId'.$_SESSION['logId']]['startTime']);

Session::initializeLight();
// time it took to load initLightx:
$_SESSION['logId'.$_SESSION['logId']][]['api.afterLightInit'] = (microtime(true) - $_SESSION['logId'.$_SESSION['logId']]['startTime']);

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json; charset=utf-8');


$result = array();
try {
	$result = ApiController::getInstance()->route();
	// time it took for the route routine to run:
	$_SESSION['logId'.$_SESSION['logId']][]['api.AfterRoute()'] = (microtime(true) - $_SESSION['logId'.$_SESSION['logId']]['startTime']);
} catch (SetupException $e) {
	$result = $e->getJSONMessage();
} catch (Exception $e) {        // Skipped
    $msg = $e->getMessage() . " in file " . $e->getFile() . '[' . $e->getLine() . ']';
    $result = array('result'=>'error', 'success'=>false, 'exception'=>get_class($e), 'message'=>$msg);
}
// total time for the API run:
$_SESSION['logId'.$_SESSION['logId']][]['api.apiTime'] = (microtime(true) - $_SESSION['logId'.$_SESSION['logId']]['startTime']);

	
	
	// create the Log and SlowLog array
	if(is_array($_SESSION['logId'.$_SESSION['logId']])){
		foreach ($_SESSION['logId'.$_SESSION['logId']] as $values) {
			if(is_array($values)){
				foreach($values  as $key=>$value){
					//echo $value;
			        if(isset($backupValue)){
			        	$diff = $value - $backupValue;
			            if($diff> 0.5){
			            	$logSlow[]= array("logId"=>$_SESSION['logId'],"key"=>$key,"value"=>$value,"diff"=>$diff);
			            }
			        }else{
			                $diff = 0;
			        }
			        
			        $backupKey = $key;
			        $backupValue = $value;
			        
			        if(($value - $backupValue) < 0.0001){
			        	$diff = 0.0001;
			        }


			         
			        if(($value - $backupValue) < 0.0001){
			        	$diff = 0.0001;
			        }
			  		$log[]= array("logId"=>$_SESSION['logId'],"key"=>$key,"value"=>$value,"diff"=>$diff);
			        
				}
			}
		}
	}



$log[]= array("logId"=>$_SESSION['logId'],"key"=>"server.endTime","value"=>microtime(true),"diff"=>(microtime(true)-$_SESSION['logId'.$_SESSION['logId']]['startTime']));

if($_GET['log']==true){
	$result['log'] = $log;
	$result['logSlow'] = $logSlow;
}
unset($_SESSION['logId'.$_SESSION['logId']]);
	
echo json_encode($result);
?>