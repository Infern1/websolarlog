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
			default:
				echo("DeviceType " . $device->type . " does not support handle history");
				break;
		}			
	}

	public function handleEnergy($args) {
		$item = $args[0];
		$device = $this->getFreshDevice($args[1]);
		
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
		$device = $this->getFreshDevice($args[1]);
		
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
		$device = $this->getFreshDevice($args[1]);
		
		switch ($device->type) {
			case "production":
				ProductionDeviceHandler::handleAlarm($item, $device);
				break;
			case "metering":
				MeteringDeviceHandler::handleAlarm($item, $device);
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
	
	/**
	 * This method will be called from the queueServer to refresh the loaded config
	 * So we can be sure we are talking to latest settings and devices
	 */
	public function refreshConfig() {
		Session::getConfig(true);
	}
	
	/**
	 * Do we need to restart?
	 */
	public function checkRestart() {
		if (Session::getConfig()->restartWorker) {
			$config = Session::getConfig(true); // Retrieve up to date config
			$config->restartWorker = false;
			PDODataAdapter::getInstance()->writeConfig($config);
			sleep (1);
			exit("Restarting worker\n");
		}
	}
	
	/**
	 * Do we need to pause?
	 */
	public function checkPause() {
		if ($config->pauseWorker) {
			while (Session::getConfig(true)->pauseWorker) {
				sleep(5);
				echo("Worker paused\n");
			}
		}
	}
}
?>