<?php
class WeatherRest
{
	/**
	 * Constructor
	 */
	function __construct()
	{
	}
	
	/**
	 * Destructor
	 */
	function __destruct()
	{
	}

	/**
	 * Rest functions 
	 */
	
	public function GET($request, $options) {
		$type = "today"; // Default
		$deviceId = -1; // All
		if (count($options) > 0) {
			$type = (trim($options[0]) != "") ? strtolower($options[0]) : $type;
		}
		if (count($options) > 1) {
			$deviceId = (trim($options[1]) != "") ? strtolower($options[1]) : $type;
		}
		
		
		$result = array();
		$result['type'] = $type;
		$result['data'] = null;
		switch ($type) {
			case "today":
				if ($deviceId > 0) {
					$result['data'] =array(array( "deviceId"=>$deviceId, "data"=>PDODataAdapter::getInstance()->getWeatherForDate($deviceId)));
				} else {
					$weathers = array();
					foreach (Session::getConfig()->devices as $device) {
						if ($device->type == "weather") {
							$weathers[] = array("deviceId"=>$device->id, "data"=>PDODataAdapter::getInstance()->getWeatherForDate($device->id));
						}
					}
					$result['data'] = $weathers;
				}
				break;
			default:
				$result['message'] = "not supported";
		}
		
		return $result;
	}	
}
?>