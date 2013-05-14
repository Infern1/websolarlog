<?php
class DeviceHandler {
	public function handleLive($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);

		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleLive($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleLive($item, $device);
				break;
			case "weather":
				WeatherDeviceHandler::handleLive($item, $device);
			default:
				echo("DeviceType " . $device->type . " does not support handle live");
				break;
		}
	}
	
	public function handleHistory($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleHistory($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleHistory($item, $device);
				break;
			case "weather":
				WeatherDeviceHandler::handleHistory($item, $device);
			default:
				echo("DeviceType " . $device->type . " does not support handle history");
				break;
		}			
	}
	
	public function handleDeviceHistory($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleDeviceHistory($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleDeviceHistory($item, $device);
				break;
			case "weather":
				WeatherDeviceHandler::handleDeviceHistory($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle device history");
				break;
		}
	}

	public function handleEnergy($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleEnergy($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleEnergy($item, $device);
				break;
			case "weather":
				WeatherDeviceHandler::handleEnergy($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle energy");
				break;
		}			
	}

	public function handleInfo($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleInfo($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleInfo($item, $device);
				break;
			case "weather":
				WeatherDeviceHandler::handleInfo($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle info");
				break;
		}			
	}

	public function handleAlarm($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleAlarm($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleAlarm($item, $device);
				break;
			case "weather":
				WeatherDeviceHandler::handleAlarm($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle alarm");
				break;
		}			
	}

	/**
	 * We need to get the device from the config else we keep talking to
	 * the device object set during the start off the queueServer
	 * @param Inverter $inverter
	 * @return Inverter
	 */
	public function getFreshDevice(Inverter $inverter) {
		return Session::getConfig()->getInverterConfig($inverter->id);
	}
}
?>