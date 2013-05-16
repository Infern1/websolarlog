<?php
class WeatherService {
	public static $tbl = "weather";
	
	/**
	 * Save the object to the database
	 * @param Weather $object
	 * @return Weather
	 */
	public function save(Weather $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}
	
	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Weather
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new Weather();
	}
	
	/**
	 * Retrieve all values for an device
	 * @param Device $device
	 * @return array of Weather
	 */
	public function getArrayByDevice(Device $device) {
		$bObjects = R::find( self::$tbl, ' deviceId = :deviceId ORDER BY time ', array("deviceId"=>$device->id));
		$objects = array();
		foreach ($bObjects as $bObject) {
			$objects[] = $this->toObject($bObject);
		}
		return $objects;
	}
	
	/**
	 * Retrieve the last object
	 * @param Device $device
	 * @return Weather
	 */
	public function getLastWeather(Device $device) {
		$bean = R::findOne('weather',' deviceId = :deviceId ORDER BY time DESC LIMIT 1',array(':deviceId'=>$device->id));
		return $this->toObject($bean);
	}
	
	/**
	 * Retrieve all objects for the given device and for the given date (default today)
	 * @param Device $device
	 * @param string $date
	 * @return array of Weather
	 */
	public function getWeatherForDate(Device $device, $date=null) {
		(!$date)? $date = date('d-m-Y') : $date = $date;
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);
	
		$beans =  R::findAll( 'weather', ' where deviceId = :deviceId AND time > :beginDate AND time < :endDate ORDER BY time',
				array(':deviceId'=>$device->id,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
	
		$objects = array();
		foreach ($beans as $bean) {
			$objects[] = $this->toObject($bean);
		}
	
		return $objects;
	}
	
	private function toBean($object, $bObject) {
		$bObject->deviceId = $object->deviceId;
		$bObject->time = $object->time;
		$bObject->temp = $object->temp;
		$bObject->temp_min = $object->temp_min;
		$bObject->temp_max = $object->temp_max;
		$bObject->pressure = $object->pressure;
		$bObject->humidity = $object->humidity;
		$bObject->conditionId = $object->conditionId;
		$bObject->wind_speed = $object->wind_speed;
		$bObject->wind_direction = $object->wind_direction;
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new Weather();
		$object->id = $bObject->id;
		$object->deviceId = $bObject->deviceId;
		$object->time = $bObject->time;
		$object->temp = $bObject->temp;
		$object->temp_min = $bObject->temp_min;
		$object->temp_max = $bObject->temp_max;
		$object->pressure = $bObject->pressure;
		$object->humidity = $bObject->humidity;
		$object->conditionId = $bObject->conditionId;
		$object->wind_speed = $bObject->wind_speed;
		$object->wind_direction = $bObject->wind_direction;
		return $object;
	}
}
?>