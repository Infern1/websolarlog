<?php
class WeatherDeviceHandler {
	
	public static function handleLive(QueueItem $item, Inverter $device) {
		// Not supported
	}

	public static function handleHistory(QueueItem $item, Inverter $device) {
		$api = $device->getApi(Session::getConfig());
		$weather = $api->getData();
		$weather->deviceId = $device->id;
		$weather->time = $item->time;
		
		PDODataAdapter::getInstance()->saveWeather($weather);
	}

	public static function handleDeviceHistory(QueueItem $item, Inverter $device) {
		// Not supported
	}
	
	public static function handleEnergy(QueueItem $item, Inverter $device) {
		// Not supported
	}

	public static function handleInfo(QueueItem $item, Inverter $device) {
		// Not supported
	}
	
	public static function handleAlarm(QueueItem $item, Inverter $device) {
		// Not supported
	}
}
?>