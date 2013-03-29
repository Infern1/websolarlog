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

$historyUpdateRate = 5 * 60; // 5 minute refreshrate
$historyStartTime = createTimeForWholeInterval($historyUpdateRate);

$energyUpdateRate = 10 * 60; // 10 minute refreshrate
$energyStartTime = createTimeForWholeInterval($energyUpdateRate);

$infoUpdateRate = 24 * 60 * 60; // add 24 hours
$infoStartTime = createOnceADayJob("12", "00"); // Only run at 12:00

$alarmUpdateRate = 15 * 60; // every quarter
$alarmStartTime = createTimeForWholeInterval($alarmUpdateRate);

// Create the inverter jobs
foreach (Session::getConfig()->inverters as $device) {
	QueueServer::getInstance()->add(new QueueItem(time(), "DeviceHandler.handleLive", array($device), true, $device->refreshTime));
	QueueServer::getInstance()->add(new QueueItem($historyStartTime, "DeviceHandler.handleHistory", array($device), true, $historyUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($energyStartTime, "DeviceHandler.handleEnergy", array($device), true, $energyUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($infoStartTime, "DeviceHandler.handleInfo", array($device), true, $infoUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($alarmStartTime, "DeviceHandler.handleAlarm", array($device), true, $alarmUpdateRate));
}


$fastJobUpdateRate = 60; // Every minute
$fastJobStartTime = createTimeForWholeInterval($fastJobUpdateRate);
QueueServer::getInstance()->add(new QueueItem($fastJobStartTime, "HookHandler.fireFromQueue", array("onFastJob"), true, $fastJobUpdateRate));

$regularJobUpdateRate = 30 * 60; // Every 30 minutes
$regularJobStartTime = createTimeForWholeInterval($regularJobUpdateRate);
QueueServer::getInstance()->add(new QueueItem($regularJobStartTime, "HookHandler.fireFromQueue", array("onRegularJob"), true, $regularJobUpdateRate));

$slowJobUpdateRate = 60 * 60; // Every hour
$slowJobStartTime = createTimeForWholeInterval($slowJobUpdateRate);
QueueServer::getInstance()->add(new QueueItem($slowJobStartTime, "HookHandler.fireFromQueue", array("onSlowJob"), true, $slowJobUpdateRate));

// Add job to refresh the config object every 2 minutes
QueueServer::getInstance()->add(new QueueItem(createTimeForWholeInterval(120), "Common.refreshConfig", "", true, 120));

// Add job to check for pause every 2 minutes
// TODO :: When we support db queue then we should not check for it
QueueServer::getInstance()->add(new QueueItem(createTimeForWholeInterval(120), "Common.checkPause", "", true, 120));

// Add job to check for restart every 5 minutes
// TODO :: When we support db queue then we should not check for it
QueueServer::getInstance()->add(new QueueItem(createTimeForWholeInterval(300), "Common.checkRestart", "", true, 300));

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

/**
 * Creates an time based on the first coming interval
 * @param unknown $interval
 * @return number
 */
function createTimeForWholeInterval($interval) {
	return (time() + $interval) - (time() % $interval);
}
?>