<?php
class CoreAddon {
	private $adapter;
	private $config;

	function __construct() {
		$this->adapter = PDODataAdapter::getInstance();
		$this->config = Session::getConfig();
	}

	function __destruct() {
		$this->config = null;
		$this->adapter = null;
	}

	/**
	 * Handle hook onLiveData
	 * @param unknown $args
	 */
	public function onLiveData($args) {
		$inverter = $args[1];
		$live = $args[2];
		
		if ($inverter == null) {
			HookHandler::fire("onError", "CoreAddon::onLiveData() inverter == null");
			return;
		}
		
		// Save the live information
		$this->adapter->writeLiveInfo($inverter->id, $live);
		HookHandler::fire("newLiveData", $inverter, $live);
		
		// Check the Max value
		$this->checkMaxPowerValue($inverter, $live);
	}
	
	/**
	 * Handle hook onHistory
	 * @param unknown $args
	 */
	public function onHistory($args) {
		$inverter = $args[1];
		$live = $args[2];
		$history = $this->adapter->addHistory($inverter->id, $live);
		HookHandler::fire("newHistory", $inverter, $history);
	}
	
	
	public function onEnergy($args) {
		$inverter = $args[1];
		
		$arHistory = $this->adapter->readHistory($inverter->id, null);
		
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
		$energy->time = time();
		$energy->INV = $inverter->id;
		$energy->KWH = $production;
		$energy->KWHT = $productionEnd;
		$energy->co2 = Formulas::CO2kWh($production, $this->config->co2kwh); // Calculate co2
		$this->adapter->addEnergy($inverter->id, $energy);
		
		HookHandler::fire("newEnergy", $inverter, $energy);
	}
	
	/**
	 * Handle hook onInfo
	 * @param unknown $args
	 */
	public function onInfo($args) {
		$inverter = $args[1];
		$info = $args[2];
		
		// Write InverterInfo (firmware,model,etc) to DB
		$event = new Event($inverter->id, time(), 'Info', $info);
		$this->adapter->addEvent($inverter->id, $event);
		HookHandler::fire("newInfo", $inverter, $event);		
	}
	
	/**
	 * Handle hook onAlarm
	 * @param unknown $args
	 */
	public function onAlarm($args) {
		$inverter = $args[1];
		$alarm = $args[2];
		
		try {
			if (strpos($alarm->event, 'Warning') !== false ) {
				HookHandler::getInstance()->fire("onInverterWarning", $inverter, nl2br($alarm->event));
			}
			if (strpos($alarm->event, 'Error') !== false ) {
				HookHandler::getInstance()->fire("onInverterError", $inverter, nl2br($alarm->event));
			}
			$alarm->alarmSend = true;
		} catch (Exception $e) {
			$alarm->alarmSend = false;
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
		$this->adapter->addEvent($inverter->id, $alarm);
		HookHandler::fire("newAlarm", $inverter, $alarm);		
	}
	
	/**
	 * Check if the live value is higher then the saved value
	 * if true, then save it
	 * @param Inverter $inverter
	 * @param Live $live
	 */
	private function checkMaxPowerValue($inverter, $live) {
		// Get the highest value off the day
		$currentMPT = $this->adapter->readMaxPowerToday($inverter->id);
		$COEF=($live->EFF/100)* $inverter->correctionFactor;
		$COEF=($COEF > 1) ? 1 : $COEF;
		$GP2 = round($live->GP * $COEF,2);
		if (!isset($currentMPT) || $GP2 > $currentMPT->GP) {
			// Found a new max power of today
			$Ompt = new MaxPowerToday();
			$Ompt->SDTE = $live->SDTE;
			$Ompt->time = Util::getUTCdate($live->SDTE);
			$Ompt->GP = $GP2;
			$this->adapter->writeMaxPowerToday($inverter->id, $Ompt);
			HookHandler::fire("newMaxPowerToday", $inverter, $Ompt);
		}
	}
	
	
}