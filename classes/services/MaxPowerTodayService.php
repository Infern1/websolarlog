<?php
class MaxPowerTodayService {

    public static $tbl = "pMaxOTD";
    private $config;
	
	function __construct() {
		HookHandler::getInstance()->add("onJanitorDbCheck", "MaxPowerTodayService.janitorDbCheck");
        $this->config = Session::getConfig();
	}
	
	/**
	 * Save the object to the database
	 * @param MaxPowerToday $object
	 * @return MaxPowerToday
	 */
	public function save(MaxPowerToday $object) {
        $bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}

	/**
	 * Load an object from the database
	 * @param int $id
	 * @return MaxPowerToday
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new MaxPowerToday();
    }
	
	/**
	 * Retrieves all lines
	 * @return Array of MaxPowerToday
	 */
	public function getList() {
		$bObjects = R::find( self::$tbl, ' ORDER BY time');
		$objects = array();
		foreach ($bObjects as $bObject) {
			$objects[] = $this->toObject($bObject);
		}
		return $objects;
	}

	/**
	 * Retrieves all energy lines by device
	 * @return Array of MaxPowerToday
	 */
	public function getListByDevice(Device $device) {
		$bObjects = R::find(self::$tbl, ' deviceId = :deviceId ORDER BY time', array("deviceId" => $device->id));
        $objects = array();
		foreach ($bObjects as $bObject) {
			$objects[] = $this->toObject($bObject);
		}
		return $objects;
	}
	
	/**
	 * get the Energy for an device
	 * @param Device $energy
	 * @param $time
	 * @return $energy
	 */
	public function getByDeviceAndTime(Device $device, $time) {
		$beginEndDate = Util::getBeginEndDate('day', 1,$time);

		$bean = R::findOne(self::$tbl, ' deviceId = :deviceId AND time > :beginDate AND time < :endDate LIMIT 1 ', array(':deviceId'=>$device->id, ':beginDate'=>$beginEndDate['beginDate']+60,':endDate'=>$beginEndDate['endDate']-60)	);
	
		if (!$bean){
			return null;
		}
	
		return $this->toObject($bean);
	}
	
	public function janitorDbCheck() {
		HookHandler::getInstance()->fire("onDebug", "MaxPowerTodayService janitor DB Check");

        // Get an object and save it, to make sure al fields are available in the database
        $bObject = R::findOne(self::$tbl, ' 1=1 LIMIT 1');
        if ($bObject) {
            $object = $this->toObject($bObject);
            R::store($this->toBean($object, $bObject));
            HookHandler::getInstance()->fire("onDebug", "Updated MaxPowerToday");
        } else {
            HookHandler::getInstance()->fire("onDebug", "MaxPowerToday object not found");
        }

        // set the device id
        R::exec("UPDATE pMaxOTD SET deviceId = INV WHERE INV is not NULL");
    }
	
	private function toBean($object, $bObject) {
		$bObject->deviceId = $object->deviceId;
		$bObject->time = $object->time;
		$bObject->GP = $object->GP;
        return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new MaxPowerToday();
        if (isset($bObject)) {
			$object->id = $bObject->id;
			$object->deviceId = $bObject->deviceId;
			$object->time = $bObject->time;
			$object->GP = $bObject->GP;
        }
		return $object;
	}
}
?>