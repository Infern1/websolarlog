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

$useNewWorker = true;

if ($useNewWorker) {
	$workHandler = new WorkHandler();
	$count = 0;
	while ($count < 60) {
		if ($count > 30) {
			checkPauseAndRestart();
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

function checkPauseAndRestart() {
	$config = Session::getConfig(true);
	if ($config->pauseWorker) {
		while ($config->pauseWorker) {
			sleep(10);
			$config = Session::getConfig(true);
		}
	}
	if ($config->restartWorker) {
		$config->restartWorker = false;
		PDODataAdapter::getInstance()->writeConfig($config);
		sleep (2);
		exit("Restarting worker");
	}
} 
?>