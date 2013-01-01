<?php
class WorkHandler {
	private $config;
	
	
	function __construct() {
		// Initialize objects
		$this->config = Session::getConfig();
	}
	
	function __destruct() {
		// Release objects
		$this->config = null;
	}
	
	public function start() {
		$dataAdapter = PDODataAdapter::getInstance(); // Only need the initialisation for the transaction support
		
		// Handle every inverter in its own transaction
		R::begin(); // Start a transaction to speed things up
		foreach ($this->config->inverters as $inverter) {
			$this->handleInverter($inverter);
		}
		R::commit(); // Commit the transaction
		
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
	
	private function handleInverter(Inverter $inverter) {
		// Get the api we need to use
		$api = $this->getInverterApi($inverter);
		
		// Retrieve the inverter data
		$live = $api->getLiveData();
		
		// Fire the hook that will handle the live data
		HookHandler::getInstance()->fire("onLiveData", $inverter, $live);
		
		// Fire the hook that will handle the history data
		if ($live != null && PeriodHelper::isPeriodJob("HistoryJob", 5)) {
			HookHandler::fire("onHistory", $inverter, $live);
		}
		
		// Fire the hook that will handle the energy data
		// if we are live we will fire every 30 minutes
		if ($live != null && PeriodHelper::isPeriodJob("EnergyJob", 30)) {
			HookHandler::fire("onEnergy", $inverter);
		}
		// if we are not live we will fire every 120 minutes
		if ($live == null && PeriodHelper::isPeriodJob("EnergyJob", 120)) {
			HookHandler::fire("onEnergy", $inverter);
		}
		
		// Fire the hook that will handle the information requests
		if ($live != null && PeriodHelper::isPeriodJob("InfoJob", 6 * 60)) {
			sleep(2); // Don't spam the inverter with requests
			$info = $api->getInfo();
			if (trim($info) != "") {
				HookHandler::fire("onInfo", $inverter, $info);
			}
		}
	}
	
	/**
	 * Retrieve the api interface we need to use
	 * @param Inverter $inverter
	 * @return Ambigous <Aurora, Sma>
	 */
	private function getInverterApi(Inverter $inverter) {
		// Get the api we need to use
		$api = $inverter->getApi($this->config);
		if ($api == null) {
			// If nothing is received, we will use the aurora class (Compatibility mode)
			$api = new Aurora($this->config->aurorapath, $inverter->comAddress, $this->config->comPort, $this->config->comOptions, $this->config->comDebug);
		}
		return $api;
	}
}