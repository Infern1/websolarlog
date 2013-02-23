<?php
error_reporting(E_ALL);

$startTime = time(); // start time in seconds

$docRoot = dirname(dirname(__FILE__));
require_once $docRoot . '/admin/classes/classloader.php';
Session::initialize();

$workHandler = new WorkHandler();
while (time() - $startTime < 55) {
	try {
		$workHandler->start();
	} catch (Exception $e) {
		echo ($e->getMessage());
	}
	
	/*
	 *  As we are running as cron we probably are on an server with shared hosting
	 *  We don't want to get the cpu to 100% so sit back for 5 seconds
	 */
	sleep(5);
}
?>