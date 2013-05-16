<?php
class MeteringDeviceHandler {
	
	public static function handleLive(QueueItem $item, Device $device) {
		// Get the api we need to use
		$api = $device->getApi(Session::getConfig());
		
		// Retrieve the device data
		$live = $api->getLiveData();
		
		// Fire the hook
		if ($live != null) {
			HookHandler::getInstance()->fire("onLiveSmartMeterData", $device, $live);
		} else {
			HookHandler::getInstance()->fire("onNoLiveData", $device);
		}
	}

	public static function handleHistory(QueueItem $item, Device $device) {
		$smartMeterAddon = new SmartMeterAddon();
		$live = $smartMeterAddon->readLiveSmartMeterInfo($device->id);
		if($live->highReturn > 0 AND $live->highUsage > 0 AND $live->lowReturn > 0 AND $live->lowUsage > 0){
				HookHandler::getInstance()->fire("onSmartMeterHistory", $device, $live, $item->time);
		}
	}

	public static function handleDeviceHistory(QueueItem $item, Device $device) {
		// Not supported
	}
	
	public static function handleEnergy(QueueItem $item, Device $device) {
		HookHandler::getInstance()->fire("onSmartMeterEnergy", $device, $item->time);
	}

	public static function handleInfo(QueueItem $item, Device $device) {
		// Not supported
	}
	
	public static function handleAlarm(QueueItem $item, Device $device) {
		// Not supported
	}
}
?>