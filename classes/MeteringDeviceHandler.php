<?php
class MeteringDeviceHandler {
	
	public static function handleLive(QueueItem $item, Inverter $device) {
		// Get the api we need to use
		$api = $device->getApi(Session::getConfig());
		
		// Retrieve the inverter data
		$live = $api->getLiveData();
		
		// Fire the hook
		if ($live != null) {
			HookHandler::getInstance()->fire("onLiveSmartMeterData", $device, $live);
		} else {
			HookHandler::getInstance()->fire("onNoLiveData", $device);
		}
	}

	public static function handleHistory(QueueItem $item, Inverter $device) {
		$smartMeterAddon = new SmartMeterAddon();
		$live = $smartMeterAddon->readLiveSmartMeterInfo($device->id);
		HookHandler::getInstance()->fire("onSmartMeterHistory", $device, $live, $item->time);
	}

	public static function handleEnergy(QueueItem $item, Inverter $device) {
		HookHandler::getInstance()->fire("onSmartMeterEnergy", $device, $item->time);
	}

	public static function handleInfo(QueueItem $item, Inverter $device) {
		// Not supported
	}
	
	public static function handleAlarm(QueueItem $item, Inverter $device) {
		// Not supported
	}
}
?>