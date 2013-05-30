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
		$object = new Live();
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