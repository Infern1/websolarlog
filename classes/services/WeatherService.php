<?php
class GeoCacheService {
	public static $tbl = "weather";
	
	public function save(Weather $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}
	
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new Weather();
	}
	
	public function getListByDevice(Inverter $device) {
		$bObjects = R::find( self::$tbl, ' deviceId = :deviceId ORDER BY time ', array("deviceId"=>$device->id));
		$objects = array();
		foreach ($bObjects as $bObject) {
			$objects[] = $this->toObject($bObject);
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
		return $this;
	}
}
?>