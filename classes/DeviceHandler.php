<?php
class DeviceHandler {
	
	public function handleLive($args) {
		$item = $args[0];
		$device = $args[1];

		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleLive($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleLive($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle live");
				break;
		}
	}
	
	public function handleHistory($args) {
		$item = $args[0];
		$device = $args[1];
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleHistory($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleHistory($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle history");
				break;
		}			
	}

	public function handleEnergy($args) {
		$item = $args[0];
		$device = $args[1];
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleAlarm($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleAlarm($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle energy");
				break;
		}			
	}

	public function handleInfo($args) {
		$item = $args[0];
		$device = $args[1];
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleInfo($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleInfo($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle info");
				break;
		}			
	}

	public function handleAlarm($args) {
		$item = $args[0];
		$device = $args[1];
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleInfo($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleInfo($item, $device);
				break;
			default:
				echo("DeviceType " . $device->type . " does not support handle alarm");
				break;
		}			
	}
}
?>