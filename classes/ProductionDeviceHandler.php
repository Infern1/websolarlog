<?php
class ProductionDeviceHandler {
	
	public static function handleLive(QueueItem $item, Inverter $device) {
		// Get the api we need to use
		$api = $device->getApi(Session::getConfig());
		
		// Retrieve the inverter data
		$live = $api->getLiveData();
		
		// Fire the hook
		if ($live != null) {
			HookHandler::getInstance()->fire("onLiveData", $device, $live);
		} else {
			HookHandler::getInstance()->fire("onNoLiveData", $device);
		}
	}
	
	public static function handleHistory(QueueItem $item, Inverter $device) {
		// Only create history when the device is online
		if ($device->state == 1) {
			$live = PDODataAdapter::getInstance()->readLiveInfo($device->id);
			if (time() - $live->time > 30 * 60 ) {
				// We don't want to create an history item for an live record older then 30 minutes
				HookHandler::getInstance()->fire("onDebug", "History blocked: live record to old! live->time = " . date("Y/m/d H:i:s", $live->time ));
			} else {
				// Set the time to this job time
				$live->time = $item->time;
				$live->SDTE = date("Ymd-H:i:s", $item->time);
				HookHandler::getInstance()->fire("onHistory", $device, $live, $item->time);
			}
		}
	}
	
	public static function handleDeviceHistory(QueueItem $item, Inverter $device) {
		// Get the api we need to use
		$api = $device->getApi(Session::getConfig());
	
		// Retrieve the inverter data
		$deviceHistoryList = $api->getHistoryData();
		
		// Did we get an result?
		if ($deviceHistoryList == null) {
			return; // Nothing to do
		}
		
		// Save all objects if they do not exist
		$notProcessed = 0;
		foreach ($deviceHistoryList as $deviceHistory) {
			$deviceHistory->deviceId = $device->id;
			$newDeviceHistory = PDODataAdapter::getInstance()->addOrUpdateDeviceHistoryByDeviceAndTime($deviceHistory);
			
			if (!$newDeviceHistory->processed) {
				$notProcessed++;
			}
		}
		HookHandler::getInstance()->fire("onInfo", "Retrieved " . count($deviceHistoryList) . 
			" records from history off device " . $device->name . ". There are " . $notProcessed .  " unprocessed lines.\n");
	}

	public static function handleEnergy(QueueItem $item, Inverter $device) {
		HookHandler::getInstance()->fire("onEnergy", $device, $item->time);
	}

	public static function handleInfo(QueueItem $item, Inverter $device) {
		$info = trim($device->getApi(Session::getConfig())->getInfo());
		if ($info != "") {
			HookHandler::getInstance()->fire("onInverterInfo", $device, $info);
		}
	}
	
	public static function handleAlarm(QueueItem $item, Inverter $device) {
		$alarm = trim($device->getApi(Session::getConfig())->getAlarms());
		if ($alarm != "") {
			$event = new Event($device->id, time(), 'Alarm', Util::formatEvent($alarm));
			if (self::isAlarmDetected($event)) {
				HookHandler::getInstance()->fire("onInverterAlarm", $device, $event);
			}
		}
	}
	
	/**
	 * Check if the line is filled with an real alarm
	 * TODO :: This should move to the device api section
	 * @param Event $event
	 * @return boolean
	 */
	private static function isAlarmDetected($event) {
		$event_text = trim($event->event);
		$event_lines = explode("\n", $event_text);
	
		$alarmFound = false;
		foreach ($event_lines as $line) {
			// Aurora error
			$parts = explode(":", $line);
			if (count($parts) > 1 && trim($parts[1]) != "No Alarm") {
				$alarmFound = true;
				break;
			}
		}
	
		// SMA
		if (trim($event->event) == "Fehler -------") {
			$alarmFound = false;
		}
	
		return $alarmFound;
	}
}
?>