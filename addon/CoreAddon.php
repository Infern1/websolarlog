<?php
class CoreAddon {
	private $adapter;
	private $liveService;
	private $config;

	function __construct() {
		$this->adapter = PDODataAdapter::getInstance();
		$this->config = Session::getConfig();
		$this->liveService = new LiveService();
	}

	function __destruct() {
		$this->liveService = null;
		$this->config = null;
		$this->adapter = null;
	}

	/**
	 * Handle hook onLiveData
	 * @param unknown $args
	 */
	public function onLiveData($args) {
		$device = $args[1];
		$live = $args[2];
	
		if ($device == null) {
			HookHandler::getInstance()->fire("onError", "CoreAddon::onLiveData() $device == null");
			return;
		}
		
		// Get the current live object
		$dbLive = $this->liveService->getLiveByDevice($device);
	
		// Save the live information
		$live->id = $dbLive->id;
		$this->liveService->save($live);
		//$this->adapter->writeLiveInfo($device->id, $live);
		HookHandler::getInstance()->getInstance()->fire("newLiveData", $device, $live);
	
		// Check the Max value
		$this->checkMaxPowerValue($device, $live);
	}
	


	/**
	 * Handle hook onHistory
	 * @param unknown $args
	 */
	public function onHistory($args) {
		$device = $args[1];
		$live = $args[2];
		$timestamp = $args[3];

		// Only add history when the device is live
		if ($device->state == 1) {
			$this->adapter->addHistory($device->id, $live, $timestamp);
			hookHandler::getInstance()->fire("newHistory", $device, $timestamp);
		}
	}
	
	


	public function onEnergy($args) {
		//echo "CoreAddon onEnergy";
		//var_dump($args);
		$device = $args[1];
		$timestamp = $args[2];
	
		$arHistory = $this->adapter->readHistory($device->id, null);
	
		$first = reset($arHistory);
		$last = end($arHistory);
	
		$productionStart = $first['KWHT'];
		$productionEnd = $last['KWHT'];
	
		// Check if we passed 100.000kWh
		if ($productionEnd < $productionStart) {
			$productionEnd += 100000;
		}
		$production = round($productionEnd - $productionStart, 3);
	
		// Set the new values and save it
		$energy = new Energy();
		$energy->SDTE = $first['SDTE'];
		$energy->time = $timestamp;
		$energy->INV = $device->id;
		$energy->KWH = $production;
		$energy->KWHT = $productionEnd;
		$energy->co2 = Formulas::CO2kWh($production, $this->config->co2kwh); // Calculate co2
		$this->adapter->addEnergy($device->id, $energy);
		HookHandler::getInstance()->fire("newEnergy", $device, $energy);
	}
	

	
	/**
	 * Handle hook onInverterInfo
	 * @param unknown $args
	 */
	public function onInverterInfo($args) {
		$device = $args[1];
		$info = $args[2];
		
		// Write InverterInfo (firmware,model,etc) to DB
		$event = new Event($device->id, time(), 'Info', $info);
		$this->adapter->addEvent($device->id, $event);
		HookHandler::getInstance()->fire("newInfo", $device, $event);		
	}
	
	/**
	 * Handle hook onInverterAlarm
	 * @param unknown $args
	 */
	public function onInverterAlarm($args) {
		$device = $args[1];
		$alarm = $args[2];
		
		try {
			if (strpos($alarm->event, 'Warning') !== false ) {
				HookHandler::getInstance()->fire("onInverterWarning", $device, nl2br($alarm->event));
			}
			if (strpos($alarm->event, 'Error') !== false ) {
				HookHandler::getInstance()->fire("onInverterError", $device, nl2br($alarm->event));
			}
			$alarm->alarmSend = true;
		} catch (Exception $e) {
			$alarm->alarmSend = false;
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
		$this->adapter->addEvent($device->id, $alarm);
		HookHandler::getInstance()->fire("newAlarm", $device, $alarm);		
	}
	
	/**
	 * Check if the live value is higher then the saved value
	 * if true, then save it
	 * @param Device $device
	 * @param Live $live
	 */
	private function checkMaxPowerValue($device, $live) {
		// Get the highest value off the day
		$currentMPT = $this->adapter->readMaxPowerToday($device->id);
		$COEF=($live->EFF/100)* 1;
		$COEF=($COEF > 1) ? 1 : $COEF;
		$GP2 = round($live->GP * $COEF,2);
		if (!isset($currentMPT) || $GP2 > $currentMPT->GP) {
			// Found a new max power of today
			$Ompt = new MaxPowerToday();
			$Ompt->SDTE = $live->SDTE;
			$Ompt->time = Util::getUTCdate($live->SDTE);
			$Ompt->GP = $GP2;
			$this->adapter->writeMaxPowerToday($device->id, $Ompt);
			HookHandler::getInstance()->fire("newMaxPowerToday", $device, $Ompt);
		}
	}
	
	public function touchPidFile(){
		$filename = Session::getBasePath().'/scripts/server.php.pid';
		touch($filename);
	}
}