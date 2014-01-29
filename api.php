<?php

$_SESSION[$_SESSION['logId']] = null;
$_SESSION[$_SESSION['logId']] = array();

$logId = rand(100, 99999);
$_SESSION['logId'] = $logId;
$_SESSION[$_SESSION['logId']]['startTime'] = microtime(true);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'classes/classloader.php';
// time it took to load the classloader:
$_SESSION[$_SESSION['logId']][]['api.afterClassLoader'] = (microtime(true) - $_SESSION[$_SESSION['logId']]['startTime']);

Session::initializeLight();
// time it took to load initLightx:
$_SESSION[$_SESSION['logId']][]['api.afterLightInit'] = (microtime(true) - $_SESSION[$_SESSION['logId']]['startTime']);

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json; charset=utf-8');


$result = array();
try {
	$result = ApiController::getInstance()->route();
	// time it took for the route routine to run:
	$_SESSION[$_SESSION['logId']][]['api.AfterRoute()'] = (microtime(true) - $_SESSION[$_SESSION['logId']]['startTime']);
} catch (SetupException $e) {
	$result = $e->getJSONMessage();
} catch (Exception $e) {        // Skipped
    $msg = $e->getMessage() . " in file " . $e->getFile() . '[' . $e->getLine() . ']';
    $result = array('result'=>'error', 'success'=>false, 'exception'=>get_class($e), 'message'=>$msg);
}
// total time for the API run:
$_SESSION[$_SESSION['logId']][]['api.apiTime'] = (microtime(true) - $_SESSION[$_SESSION['logId']]['startTime']);

	
foreach ($_SESSION[$_SESSION['logId']] as $value) {
	foreach($value  as $key=>$value){
		//echo $value;
        if(isset($backupValue)){
        	$diff = $value - $backupValue;
            if($diff> 0.5){
            	$logSlow[]= array("logId"=>$_SESSION['logId'],"key"=>$key,"value"=>$value,"diff"=>$diff,"diffText"=>$diffText);
            }
        }else{
                $diff = 0;
        }
  		$log[]= array("logId"=>$_SESSION['logId'],"key"=>$key,"value"=>$value,"diff"=>$diff,"diffText"=>$diffText);

        $backupKey = $key;
        $backupValue = $value;
	}
}



$log[]= array("logId"=>$_SESSION['logId'],"key"=>"server.endTime","value"=>microtime(true),"diff"=>(microtime(true)-$_SESSION[$_SESSION['logId']]['startTime']));

if($_GET['log']==true){
	$result['log'] = $log;
	$result['logSlow'] = $logSlow;
}
unset($_SESSION[$_SESSION['logId']]);
	
echo json_encode($result);
?>