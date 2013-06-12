<?php
class EnergyService {
	public static $tbl = "energy";
	
	/**
	 * Save the object to the database
	 * @param Energy $object
	 * @return Energy
	 */
	public function save(Energy $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}

	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Energy
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new Energy();
	}
	
	/**
	 * add or update the Energy
	 * @param Energy $energy
	 * @param $time
	 * @return $energy
	 */
	public function addOrUpdateEnergyByDeviceAndTime(Energy $energy, $time) {
		$beginEndDate = Util::getBeginEndDate('day', 1, $time);
		$bean =  R::findOne( self::$tbl, ' INV = :deviceId AND time > :beginDate AND time < :endDate ',
				array(':deviceId'=>$energy->deviceId, ':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
	
		if (!$bean){
			$bean = R::dispense(self::$tbl);
		}
	
		$bean = $this->toBean($energy, $bean);
		
		// Only save record if there is something
		if (!empty($energy->KWH) && !empty($energy->KWHT)) {
			$energy->id = R::store($bean);
		}
		return $energy;
	}
	
	/**
	 * get the Energy for an device
	 * @param Device $energy
	 * @param $time
	 * @return $energy
	 */
	public function getEnergyByDeviceAndTime(Device $device, $time) {
		$beginEndDate = Util::getBeginEndDate('day', 1, date("Y-m-d", $time));
		$bean =  R::findOne( self::$tbl, ' INV = :deviceId AND time > :beginDate AND time < :endDate ',
				array(':deviceId'=>$device->id, ':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
	
		if (!$bean){
			return null;
		}
	
		return $this->toObject($bean);
	}
	
	private function toBean($object, $bObject) {
		$bObject->INV = $object->INV;
		$bObject->deviceId = $object->deviceId;
		$bObject->SDTE = $object->SDTE;
		$bObject->time = $object->time;
		$bObject->KWH = $object->KWH;
		$bObject->KWHT = $object->KWHT;
		$bObject->KWHKWP = $object->KWHKWP; // kWh per kWp
		$bObject->co2 = $object->co2;
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new Energy();
		$object->id = $bObject->id;
		$object->INV = $bObject->INV;
		$object->deviceId = $bObject->deviceId;
		$object->SDTE = $bObject->SDTE;
		$object->time = $bObject->time;
		$object->KWH = $bObject->KWH;
		$object->KWHT = $bObject->KWHT;
		$object->KWHKWP = $bObject->KWHKWP;
		$object->co2 = $bObject->co2;
		return $object;
	}
}
?>