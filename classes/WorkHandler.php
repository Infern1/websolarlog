<?php
class WorkHandler {
	private $config;
	private $dataAdapter;

	function __construct() {
		// Initialize objects
		$this->config = Session::getConfig(true);
		$this->dataAdapter = PDODataAdapter::getInstance(); // Only need the initialisation for the transaction support

	}

	function __destruct() {
		// Release objects
		$this->config = null;
	}

	public function start() {
		$timestamp = time();
		// Handle every inverter in its own transaction
		foreach ($this->config->inverters as $inverter) {
			try {
				R::begin(); // Start a transaction to speed things up
				$this->handleInverter($inverter,$timestamp);				
				R::commit(); // Commit the transaction
			} catch (Exception $e) {
				R::rollback();
				HookHandler::getInstance()->fire("onInverterError", $inverter, $e->getMessage());
			}
		}

		// These hooks will also run if the inverter is down
		if (PeriodHelper::isPeriodJob("fastJob", 1)) {
			HookHandler::getInstance()->fire("onFastJob");
		}
		if (PeriodHelper::isPeriodJob("regularJob", 30)) {
			HookHandler::getInstance()->fire("onRegularJob");
		}
		if (PeriodHelper::isPeriodJob("slowJob", 60)) {
			HookHandler::getInstance()->fire("onSlowJob");
		}
	}

	private function handleInverter(Inverter $inverter, $timestamp) {
		if(!$inverter->type){
			$this->dataAdapter->setDeviceType($inverter);
		}
		
		// Get the api we need to use
		$api = $this->getInverterApi($inverter);

		// Retrieve the inverter data
		$live = $api->getLiveData();
				
		// Fire the hook that will handle the live data
		switch ($inverter->type) {
			case "production":
				$this->handleProductionDevice($inverter, $api, $live, $timestamp);
				break;
			case "metering":
				$this->handleMeteringDevice($inverter, $api, $live, $timestamp);
				break;
			default:
				echo("ProductType " . $inverter->type . " is not supported by the worker");
				break;
		}
	}
	
	private function handleProductionDevice($inverter, $api, $live, $timestamp) {
		if ($live != null) {
			HookHandler::getInstance()->fire("onLiveData", $inverter, $live);
		} else {
			HookHandler::getInstance()->fire("onNoLiveData", $inverter);
		}

		// Fire the hook that will handle the history data
		if ($live != null && PeriodHelper::isPeriodJob("HistoryJob", 5)) {
			HookHandler::getInstance()->fire("onHistory", $inverter, $live, $timestamp);
		}

		// Fire the hook that will handle the energy data
		// if we are live we will fire every 30 minutes
		if ($live != null && PeriodHelper::isPeriodJob("EnergyJob", 30)) {
			HookHandler::getInstance()->fire("onEnergy", $inverter,$timestamp);
		}
		// if we are not live we will fire every 120 minutes
		if ($live == null && PeriodHelper::isPeriodJob("EnergyJob", 120)) {
			HookHandler::getInstance()->fire("onEnergy", $inverter,$timestamp);
		}

		// Fire the hook that will handle the information requests
		if ($live != null && PeriodHelper::isPeriodJob("InfoJob", 6 * 60)) {
			sleep(2); // Don't spam the inverter with requests
			$info = $api->getInfo();
			if (trim($info) != "") {
				HookHandler::getInstance()->fire("onInverterInfo", $inverter, $info);
			}
		}

		// Check if there are alarms
		if ($live != null && PeriodHelper::isPeriodJob("EventJob", 2)) {
			$alarm = $api->getAlarms();
			if (trim($alarm) != "") {
				$event = new Event($inverter->id, time(), 'Alarm', Util::formatEvent($alarm));
				if ($this->isAlarmDetected($event)) {
					HookHandler::getInstance()->fire("onInverterAlarm", $inverter, $event);
				}
			}
		}
	}
	
	private function handleMeteringDevice($inverter, $api, $live, $timestamp) {
		if ($live != null) {
			HookHandler::getInstance()->fire("onLiveSmartMeterData", $inverter, $live);
		} else {
			HookHandler::getInstance()->fire("onNoLiveData", $inverter);
		}

		// Fire the hook that will handle the history data
		if ($live != null && PeriodHelper::isPeriodJob("HistorySmartMeterJob", 5)) {
			HookHandler::getInstance()->fire("onSmartMeterHistory", $inverter, $live, $timestamp);
		}

		// Fire the hook that will handle the energy data
		// if we are live we will fire every 30 minutes
		if ($live != null && PeriodHelper::isPeriodJob("EnergySmartMeterJob", 10)) {
			HookHandler::getInstance()->fire("onSmartMeterEnergy", $inverter,$timestamp);
		}
	}

	/**
	 * Retrieve the api interface we need to use
	 * @param Inverter $inverter
	 * @return Ambigous <Aurora, Sma>
	 */
	private function getInverterApi(Inverter $inverter) {
		// Get the api we need to use
		//echo "getInverterApi\r\n";
		$api = $inverter->getApi($this->config);
		//var_dump($api);

		//var_dump($inverter);
		//echo $this->config->aurorapath;
		if ($api == null) {
			//echo "geen Api gevonden\r\n";
			// If nothing is received, we will use the aurora class (Compatibility mode)
			$api = new Aurora($this->config->aurorapath, $inverter->comAddress, $this->config->comPort, $this->config->comOptions, $this->config->comDebug);
		}
		//echo "\r\nReturn getInverterApi\r\n";
		return $api;
	}

	/**
	 * Check if the line is filled with an real alarm
	 * @param Event $event
	 * @return boolean
	 */
	private function isAlarmDetected($event) {
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