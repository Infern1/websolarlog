<?php 

Class WeatherOWM implements DeviceApi {
	private $weatherUrl = "http://openweathermap.org/data/2.1/find/city?units=metrics&cnt=1";
	private $lat;
	private $lon;
	
	function __construct($lat, $lon) {
		$this->lat = $lat;
		$this->lon = $lon;
	}
	
	
	public function getState() {
		return 0; // Only offline if no internet connection or site down
	}
	
	public function getAlarms() {
		// not supported
	}
	
	public function getData(Inverter $device) {
		$latlng = "lat=" . $this->lat . "&lon=" . $this->lon;
		$url = $this->weatherUrl . "&" . $latlng;
		$result = json_decode($this->call($url));
		
		$weather = new Weather();
		$weather->deviceId = $device->id;
		$weather->time = time();
		$weather->temp = $result->list[0]->main->temp - 273.15;
		$weather->temp_min = $result->list[0]->main->temp_min - 273.15;
		$weather->temp_max = $result->list[0]->main->temp_max - 273.15;
		$weather->pressure = $result->list[0]->main->pressure;
		$weather->humidity = $result->list[0]->main->humidity;
		return $weather;
	}
	
	public function getInfo() {
		return "www.openweathermap.org";	
	}
	
	public function syncTime() {
		// not supported
	}
	
	public function getHistoryData() {
		// not supported
	}
	
	// Communicate
	private function call($url) {
		try {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($httpCode == "200") {
				return $result;
			}
		} catch (Exception $e) {
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
		return false;
	}
}
?>