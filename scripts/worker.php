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

$useNewWorker = false;

if ($useNewWorker) {
	$workHandler = new WorkHandler();
	$count = 0;
	while (true) {
		if ($count > 60) {
			$workHandler = null;
			$workHandler = new WorkHandler();
			$count = 0;
		}
		try {
			$workHandler->start();
		} catch (Exception $e) {
			echo ($e->getMessage());
		}
		$count++;
		sleep(2);
		
	}
} else {
	$worker = new Worker();
	$worker->start();
	
}
?>