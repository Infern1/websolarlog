<?php
class WeatherAddon {
	private $whereApiUrl = "http://where.yahooapis.com/geocode?q=";
	private $wheatherApiUrl = "http://weather.yahooapis.com/forecastrss?w=";
	private $appId = "WebSolarLog";
	
	public function getWeather($woeid) {
		$url = "http://weather.yahooapis.com/forecastrss?u=c&w=".$woeid;
		
		$weatherXmlString = $this->call($url);
		$weatherXmlObject = new SimpleXMLElement($weatherXmlString);
		$currentCondition = $weatherXmlObject->xpath("//yweather:condition");
		$currentTemperature = $currentCondition[0]["temp"];
		$currentDescription = $currentCondition[0]["text"];
		
		return $currentTemperature;
	}
	
	
	public function getCityCode($lat, $long) {
		$url = $this->whereApiUrl . $lat.",+".$long."&gflags=R&appid=" . $this->appId;
		return $this->get_match('/<woeid>(.*)</isU', $this->call($url));
	}

	private function get_match($regex,$content){
		preg_match($regex,$content,$matches);
		return $matches[1];
	}
	
	private function call($url) {
		try {
			$ch = curl_init($url);
			//curl_setopt($ch, CURLOPT_POST, 1);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array( $hAPI, $hSYSTEM));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			//HookHandler::getInstance()->fire("onDebug", "send to yahoo: " . print_r($vars, true) . " result: " .  $result);
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