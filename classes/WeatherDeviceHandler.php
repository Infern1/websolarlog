<?php
class WeatherDeviceHandler {
	
	function __construct() {
		// Initialize services
	}
	
	function __destruct() {
		// Destroy services
	}
	
	
	
	public static function handleLive(QueueItem $item, Inverter $device) {
		// Not supported
	}

	public static function handleHistory(QueueItem $item, Inverter $device) {
		$weather = $device->getApi(Session::getConfig())->getData();
		$weather->deviceId = $device->id;
		$weather->time = $item->time;

		$weatherService = new WeatherService();
		$weatherService->save($weather);
	}

	public static function handleDeviceHistory(QueueItem $item, Inverter $device) {
		// Not supported
	}
	
	public static function handleEnergy(QueueItem $item, Inverter $device) {
		// Not supported
	}

	public static function handleInfo(QueueItem $item, Inverter $device) {
		$info = trim($device->getApi(Session::getConfig())->getInfo());
		if ($info != "") {
			HookHandler::getInstance()->fire("onInverterInfo", $device, $info);
		}
	}
	
	
	public static function handleAlarm(QueueItem $item, Inverter $device) {
		// Not supported
	}
}
?>