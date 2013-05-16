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
		foreach (Session::getConfig()->devices as $device) {
			$type = $device->type;
			$live = null;
			switch ($type) {
				case "production":
					$live = PDODataAdapter::getInstance()->readLiveInfo($device->id);					
					break;
				case "metering":
					$smartMeterAddon = new SmartMeterAddon();
					$live = $smartMeterAddon->readLiveSmartMeterInfo($device->id);					
					break;
				case "weather":
					$live = $this->weatherService->getLastWeather($device);					
					break;
			}
			$result[] = array("type"=>$type, "id"=>$device->id, "name"=>$device->name, "data"=>$live);
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