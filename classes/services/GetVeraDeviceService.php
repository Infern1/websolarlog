<?php

class GetVeraDeviceService {
	public static $tbl = "GetVeraDevice";
	private $config;
	
	function __construct() {
		$this->config = Session::getConfig();
	}
	
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

        public function onJob() {
            $devicesId = explode(",",$this->config->veraDevices);
            foreach ($devicesId as $deviceId){
                $kwh = (float)$this->call("http://".$this->config->veraIP.":3480/data_request?id=variableget&DeviceNum=".$deviceId."&serviceId=urn:micasaverde-com:serviceId:EnergyMetering1&Variable=KWH");    
                $name = $this->call("http://".$this->config->veraIP.":3480/data_request?id=variableget&DeviceNum=".$deviceId."&Variable=name");
                
                if(is_int($kwh) || is_float($kwh)){
                    $device = new veraDevice();
                    $device->deviceId = $deviceId;
                    $device->KWHT = $kwh;
                    $device->name = $name;
                    $this->addOrUpdateEnergyByDeviceAndTime($device);
                }
            }    
        }

        /**
	 * add or update the Energy
	 * @param Energy $energy
	 * @param $time
	 * @return $energy
	 */
	public function addOrUpdateEnergyByDeviceAndTime(veraDevice $veraDevice) {
                
                $veraDevice->time = time();
		$beginEndYesterday = Util::getBeginEndDate('yesterday', 1);
		$beanYesterday = R::findOne(self::$tbl, ' deviceId = :deviceId AND time > :beginDate AND time < :endDate ', array(':deviceId'=>$veraDevice->deviceId, ':beginDate'=>$beginEndYesterday['beginDate'],':endDate'=>$beginEndYesterday['endDate']));
                
                // is there an Yesterday records else find the most recent old record.
                if(!$beanYesterday){
                    $beanYesterday = R::findOne(self::$tbl, ' deviceId = :deviceId and time < :endDate order by time DESC limit 1 ', array(':deviceId'=>$veraDevice->deviceId,':endDate'=>$beginEndYesterday['endDate']));
                }
                
                // if there no old record, then this is probably the first run and we need to make and "reference" point 
                if(!$beanYesterday){
                    // dispence an bean
                    $bean = R::dispense(self::$tbl);
                    // convert VerDevice object to bean
                    $bean = $this->toBean($veraDevice, $bean);
                    // set bean team on yesterday
                    $bean->KWH = 0;
                    $bean->time = $beginEndYesterday['beginDate'] + (60*60);
                    // save the yesterday bean :)
                    R::store($bean);
                }
                
                $beginEndDate = Util::getBeginEndDate('day', 1);
		$bean = R::findOne(self::$tbl, ' deviceId = :deviceId AND time > :beginDate AND time < :endDate ', array(':deviceId'=>$veraDevice->deviceId, ':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
	
		if (!$bean){
			$bean = R::dispense(self::$tbl);
		}
                $veraDevice->KWH = sprintf("%0.3f",0);
                
                if($beanYesterday['KWHT']!='' and $bean['KWHT']!=''){
                    if((float)$bean['KWHT'] < (float)$beanYesterday['KWHT']){
                        $veraDevice->KWH = sprintf("%0.3f",$bean['KWHT']);
                    }else{
                        $veraDevice->KWH = sprintf("%0.3f",$bean['KWHT'] - $beanYesterday['KWHT']);
                    }
                }

		$bean = $this->toBean($veraDevice, $bean);
                
		// Only save record if there is something
		if (!empty($veraDevice->KWHT)) {
			$veraDevice->id = R::store($bean);
		}
		return $veraDevice;
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
		return isset($object) ? $object : new veraDevice();
	}

	/**
	 * Retrieves all energy lines
	 * @return Array of Energy
	 */
	public function getEnergyList() {
		$bObjects = R::find( self::$tbl, ' ORDER BY time');
		$objects = array();
		foreach ($bObjects as $bObject) {
			$objects[] = $this->toObject($bObject);
		}
		return $objects;
	}

	/**
	 * Retrieves all energy lines by device
	 * @return Array of Energy
	 */
	public function getEnergyListByDevice(Device $device) {
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
	public function getEnergyByDeviceAndTime(veraDevice $device, $time) {
		$beginEndDate = Util::getBeginEndDate('day', 1,$time);

		$bean = R::findOne(self::$tbl, ' deviceId = :deviceId AND time > :beginDate AND time < :endDate LIMIT 1 ', array(':deviceId'=>$device->id, ':beginDate'=>$beginEndDate['beginDate']+60,':endDate'=>$beginEndDate['endDate']-60)	);
	
		if (!$bean){
			return null;
		}
	
		return $this->toObject($bean);
	}
	
	public function janitorDbCheck() {
            HookHandler::getInstance()->fire("onDebug", "getVeraDeviceService janitor DB Check");

            // Get an HistorySmartMeter and save it, to make sure al fields are available in the database
            $bObject = R::findOne(self::$tbl, ' 1=1 LIMIT 1');
            if ($bObject) {
                $object = $this->toObject($bObject);
                R::store($this->toBean($object, $bObject));
                HookHandler::getInstance()->fire("onDebug", "Updated Energy");
            } else {
                HookHandler::getInstance()->fire("onDebug", "Energy object not found");
            }

            R::exec("UPDATE ".self::$tbl." SET deviceId = INV WHERE INV is not NULL");

             // Delete empty rows
            R::exec("DELETE FROM ".self::$tbl." WHERE kwh=0 AND kwht is null;");
	}
	
        
        public function call($url) {
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
	
        
	private function toBean($object, $bObject) {
		$bObject->deviceId = $object->deviceId;
		$bObject->time = $object->time;
		$bObject->KWH = $object->KWH;
                $bObject->name = $object->name;
		$bObject->KWHT = $object->KWHT;
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new veraDevice();
		if (isset($bObject)) {
			$object->id = $bObject->id;
			$object->deviceId = $bObject->deviceId;
			$object->time = $bObject->time;
			$object->KWH = $bObject->KWH;
                        $object->name = $bObject->name;
			$object->KWHT = $bObject->KWHT;
		}
		return $object;
	}
}
?>