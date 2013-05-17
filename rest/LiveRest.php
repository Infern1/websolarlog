<?php
class LiveRest {
	private $weatherService;
	private $liveService;
	private $liveSmartMeterService;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->weatherService = new WeatherService();
		$this->liveService = new LiveService();
		$this->liveSmartMeterService = new LiveSmartMeterService();
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
		$this->liveSmartMeterService = null;
		$this->liveService = null;
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
					$live = $this->liveService->getLiveByDevice($device);
					break;
				case "metering":
					$live = $this->liveSmartMeterService->getLiveByDevice($device);					
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