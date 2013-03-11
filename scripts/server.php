<?php
error_reporting(E_ALL);

$docRoot = dirname(dirname(__FILE__));
require_once $docRoot . '/admin/classes/classloader.php';
Session::initialize();

// Check if there is already an worker running
$pid = new Pid(dirname(__FILE__));
if($pid->isAlreadyRunning) {
	echo "Already running.\n";
	exit;
}

$historyStartTime = time(); // Make a round starting like 12:00, 12:10
$historyUpdateRate = 5 * 60; // 5 minute refreshrate

$energyStartTime = time(); // Make a round starting like 12:00, 12:10
$energyUpdateRate = 10 * 60; // 5 minute refreshrate

$infoStartTime = createOnceADayJob("12", "00"); // Only run at 12:00
$infoUpdateRate = 24 * 60 * 60; // 5 minute refreshrate

$alarmStartTime = time(); 
$alarmUpdateRate = 15 * 60; // every quarter

// Create the inverter jobs
foreach (Session::getConfig()->inverters as $device) {
	QueueServer::getInstance()->add(new QueueItem(time(), "deviceHandler.handleLive", array($device), true, $device->refreshTime));
	QueueServer::getInstance()->add(new QueueItem($historyStartTime, "deviceHandler.handleHistory", array($device), true, $historyUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($energyStartTime, "deviceHandler.handleEnergy", array($device), true, $energyUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($infoStartTime, "deviceHandler.handleInfo", array($device), true, $infoUpdateRate));
	QueueServer::getInstance()->add(new QueueItem($alarmStartTime, "deviceHandler.handleAlarm", array($device), true, $alarmUpdateRate));
}

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