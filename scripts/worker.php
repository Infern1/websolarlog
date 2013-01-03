<?php
error_reporting(E_ALL);

define('checkaccess', TRUE);

$docRoot = dirname(dirname(__FILE__));
require_once $docRoot . '/admin/classes/classloader.php';
Session::initialize();

// Check if there is already an worker running
$pid = new Pid('/tmp');
if($pid->isAlreadyRunning) {
	echo "Already running.\n";
	exit;
}

$worker = new Worker();
$worker->start();

//$workHandler = new WorkHandler();
//while (true) {
//	$workHandler->start();
//	sleep(2);
//}
?>