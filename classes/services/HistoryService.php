<?php
class HistoryService {
	public static $tbl = "history";
	
	/**
	 * Save the object to the database
	 * @param History $object
	 * @return History
	 */
	public function save(History $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}

	/**
	 * Load an object from the database
	 * @param int $id
	 * @return History
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new History();
	}
	
	/**
	 * Read the history file
	 * @param Device $device
	 * @param string $date
	 * @return array of History
	 */
	public function getArrayByDeviceAndTime(Device $device, $date) {
		(!$date)? $date = date('d-m-Y') : $date = $date;
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);
	
		$bObjects =  R::find( self::$tbl,
				' INV = :deviceId AND time > :beginDate AND  time < :endDate ORDER BY time',
				array(':deviceId'=>$device->id,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
		
		$objects = array();
		foreach ($bObjects as $bObject) {
			$objects[] = $this->toObject($bObject);
		}
		return $objects;
	}
	
	private function toBean($object, $bObject) {
		$bObject->INV = $object->INV;
		$bObject->deviceId = $object->deviceId;
		$bObject->SDTE = $object->SDTE;
		$bObject->time = $object->time;
		$bObject->dayNum = $object->dayNum;
		$bObject->I1V = round($object->I1V,3);
		$bObject->I1A = round($object->I1A,3);
		$bObject->I1P = round($object->I1P,3);
		$bObject->I1Ratio = round($object->I1Ratio,3);
		$bObject->I2V = round($object->I2V,3);
		$bObject->I2A = round($object->I2A,3);
		$bObject->I2P = round($object->I2P,3);
		$bObject->I2Ratio = round($object->I2Ratio,3);
		$bObject->GV = round($object->GV,3);
		$bObject->GA = round($object->GA,3);
		$bObject->GP = round($object->GP,3);
		$bObject->FRQ = round($object->FRQ,3);
		$bObject->EFF = round($object->EFF,3);
		$bObject->INVT = round($object->INVT,3);
		$bObject->BOOT = round($object->BOOT,3);
		$bObject->KWHT = round($object->KWHT,3);
		$bObject->pvoutput = $object->pvoutput;
		$bObject->pvoutputErrorMessage = $object->pvoutputErrorMessage;
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new History();
		$object->id = $bObject->id;
		$object->INV = $bObject->INV;
		$object->deviceId = $bObject->deviceId;
		$object->SDTE = $bObject->SDTE;
		$object->time = $bObject->time;
		$object->dayNum = $bObject->dayNum;
		$object->I1V = $bObject->I1V;
		$object->I1A = $bObject->I1A;
		$object->I1P = $bObject->I1P;
		$object->I1Ratio = $bObject->I1Ratio;
		$object->I2V = $bObject->I2V;
		$object->I2A = $bObject->I2A;
		$object->I2P = $bObject->I2P;
		$object->I2Ratio = $bObject->I2Ratio;
		$object->GV = $bObject->GV;
		$object->GA = $bObject->GA;
		$object->GP = $bObject->GP;
		$object->FRQ = $bObject->FRQ;
		$object->EFF = $bObject->EFF;
		$object->INVT = $bObject->INVT;
		$object->BOOT = $bObject->BOOT;
		$object->KWHT = $bObject->KWHT;
		$object->pvoutput = $bObject->pvoutput;
		$object->pvoutputErrorMessage = $bObject->pvoutputErrorMessage;
		return $object;
	}
}
?>