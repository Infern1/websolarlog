<?php
class EventService {
	public static $tbl = "event";
	
	/**
	 * Save the object to the database
	 * @param Event $object
	 * @return Event
	 */
	public function save(Event $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}

	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Event
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new Event();
	}
	
	
	private function toBean($object, $bObject) {
		$bObject->INV = $object->INV;
		$bObject->deviceId = $object->deviceId;
		$bObject->SDTE = $object->SDTE;
		$bObject->time = $object->time;
		$bObject->type = $object->type;
		$bObject->event = $object->event;
		$bObject->alarmSend = $object->alarmSend;
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new Event();
		$object->id = $bObject->id;
		$object->INV = $bObject->INV;
		$object->deviceId = $bObject->deviceId;
		$object->SDTE = $bObject->SDTE;
		$object->time = $bObject->time;
		$object->type = $bObject->type;
		$object->event = $bObject->event;
		$object->alarmSend = $bObject->alarmSend;
		return $object;
	}
}
?>