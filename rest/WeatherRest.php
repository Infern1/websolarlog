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
		if (count($options) > 0) {
			$type = (trim($options[0]) != "") ? strtolower($options[0]) : $type;
		}
		
		$result = array();
		$result['type'] = $type;
		$result['data'] = null;
		switch ($type) {
			case "live":
				$weatherAddon = new WeatherAddon();
				$result['data'] = $weatherAddon->live();
				break;
			case "today":
				$weatherAddon = new WeatherAddon();
				$result['data'] = $weatherAddon->readWeatherHistory();
				break;
			default:
				$result['message'] = "not supported";
		}
		
		return $result;
	}	
}
?>