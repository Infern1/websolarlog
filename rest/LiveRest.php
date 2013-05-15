<?php
class LiveRest {
	private $weatherService;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->weatherService = new WeatherService();
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
		$this->weatherService = null;
	}

	/**
	 * Rest functions 
	 */
	
	public function GET($request, $options) {
		$result = array();
		foreach (Session::getConfig()->inverters as $inverter) {
			$type = $inverter->type;
			$live = null;
			switch ($type) {
				case "production":
					$live = PDODataAdapter::getInstance()->readLiveInfo($inverter->id);					
					break;
				case "metering":
					$smartMeterAddon = new SmartMeterAddon();
					$live = $smartMeterAddon->readLiveSmartMeterInfo($inverter->id);					
					break;
				case "weather":
					$live = $this->weatherService->getLastWeather($inverter);					
					break;
			}
			$result[] = array("type"=>$type, "id"=>$inverter->id, "name"=>$inverter->name, "data"=>$live);
		}
		return $result;
	}
	
	public function getCategory() {
		return new CategoryRest();
	}

	/**
	 * Non rest functions
	 */
	public function loadProduct($id) {
		return $this->productService->get($id);
	}
	
	
}
?>