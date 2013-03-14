<?php
error_reporting(E_ALL);

$docRoot = dirname(dirname(__FILE__));
require_once $docRoot . '/classes/classloader.php';
Session::initialize();

// Check if there is already an worker running
$pid = new Pid(dirname(__FILE__));
if($pid->isAlreadyRunning) {
	echo "Already running.\n";
	exit;
}

$historyStartTime = time() + 1; // Make a round starting like 12:00, 12:10
$historyUpdateRate = 5 * 60; // 5 minute refreshrate

$energyStartTime = time() + 2; // Make a round starting like 12:00, 12:10
$energyUpdateRate = 10 * 60; // 5 minute refreshrate

$infoStartTime = createOnceADayJob("12", "00"); // Only run at 12:00
$infoUpdateRate = 24 * 60 * 60; // 5 minute refreshrate

$alarmStartTime = time() + 3; 
$alarmUpdateRate = 15 * 60; // every quarter

// Create the inverter jobs
foreach (Session::getConfig()->inverters as $device) {
	QueueServer::getInstance()->add(new QueueItem(time(), "DeviceHandler.handleLive", array($device), true, $device->refreshTime));
	QueueServer::getInstance()->add(new QueueItem($historyStartTime, "DeviceHandler.handleHistory", array($device), true, $historyUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($energyStartTime, "DeviceHandler.handleEnergy", array($device), true, $energyUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($infoStartTime, "DeviceHandler.handleInfo", array($device), true, $infoUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($alarmStartTime, "DeviceHandler.handleAlarm", array($device), true, $alarmUpdateRate));
}


$fastJobStartTime = time() + 4;
$fastJobUpdateRate = 60; // Every minute
QueueServer::getInstance()->add(new QueueItem($fastJobStartTime, "HookHandler.fireFromQueue", array("onFastJob"), true, $fastJobUpdateRate));

$regularJobStartTime = time() + 5;
$regularJobUpdateRate = 30 * 60; // Every 30 minutes
QueueServer::getInstance()->add(new QueueItem($regularJobStartTime, "HookHandler.fireFromQueue", array("onRegularJob"), true, $regularJobUpdateRate));

$slowJobStartTime = time() + 6;
$slowJobUpdateRate = 60 * 60; // Every hour
QueueServer::getInstance()->add(new QueueItem($slowJobStartTime, "HookHandler.fireFromQueue", array("onSlowJob"), true, $slowJobUpdateRate));

// Add job to refresh the config object every 2 minutes
QueueServer::getInstance()->add(new QueueItem(time() + 120, "DeviceHandler.refreshConfig", "", true, 120));

// Start the queue server
QueueServer::getInstance()->start();

/**
 * Creates an job on a fix time every day
 * @param $hour
 * @param $minute
 * @return time
 */
function createOnceADayJob($hour, $minute){
	$today_text = date("Y-m-d ", time()) . $hour . ":" . $minute . ":00";
	$today = strtotime($today_text);
	$tomorrow = strtotime($today_text . ' +24 hour');
	if ($today > time()) {
		return $today;
	}
	return $tomorrow;
}
?>