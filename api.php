<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'classes/classloader.php';
Session::initialize();

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$result = array();
try {
	$result = ApiController::getInstance()->route();
} catch (SetupException $e) {
	$result = $e->getJSONMessage();
} catch (Exception $e) {        // Skipped
    $msg = $e->getMessage() . " in file " . $e->getFile() . '[' . $e->getLine() . ']';
    $result = array('result'=>'error', 'success'=>false, 'exception'=>get_class($e), 'message'=>$msg);
}
echo json_encode($result);
?>