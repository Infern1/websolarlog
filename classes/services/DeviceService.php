<?php
class DeviceService {
	public static $tbl = "inverter";
	
	/**
	 * Save the object to the database
	 * @param Device $object
	 * @return Device
	 */
	public function save(Device $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}
	
	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Device
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new Device();
	}
	
	private function toBean($object, $bObject) {
		$bObject->id = $object->id;
		$bObject->deviceApi = $object->deviceApi;
		$bObject->type = $object->type;
		$bObject->name = $object->name;
		$bObject->description = $object->description;
		$bObject->liveOnFrontend = $object->liveOnFrontend;
		$bObject->graphOnFrontend = $object->graphOnFrontend;
		$bObject->initialkwh = $object->initialkwh;
		$bObject->producesSince = $object->producesSince;
		$bObject->expectedkwh = $object->expectedkwh;
		$bObject->heading = $object->heading;
		$bObject->correctionFactor = $object->correctionFactor;
		$bObject->comAddress = $object->comAddress;
		$bObject->comLog = $object->comLog;
		$bObject->syncTime = $object->syncTime;
		$bObject->pvoutputEnabled = ($object->pvoutputEnabled != "") ? $object->pvoutputEnabled : $bObject->pvoutputEnabled;
		$bObject->pvoutputApikey = $object->pvoutputApikey;
		$bObject->pvoutputSystemId = $object->pvoutputSystemId;
		$bObject->pvoutputWSLTeamMember = $object->pvoutputWSLTeamMember;
		$bObject->state = $object->state;
		$bObject->refreshTime = (isset($object->refreshTime) && $object->refreshTime != "") ? $object->refreshTime : $bObject->refreshTime;
		
		$bObject->expectedJAN = $object->expectedJAN;
		$bObject->expectedFEB = $object->expectedFEB;
		$bObject->expectedMAR = $object->expectedMAR;
		$bObject->expectedAPR = $object->expectedAPR;
		$bObject->expectedMAY = $object->expectedMAY;
		$bObject->expectedJUN = $object->expectedJUN;
		$bObject->expectedJUL = $object->expectedJUL;
		$bObject->expectedAUG = $object->expectedAUG;
		$bObject->expectedSEP = $object->expectedSEP;
		$bObject->expectedOCT = $object->expectedOCT;
		$bObject->expectedNOV = $object->expectedNOV;
		$bObject->expectedDEC = $object->expectedDEC;
		
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new Device();
		$object->id = $bObject->id;
		$object->deviceApi = $bObject->deviceApi;
		$object->type = $bObject->type;
		$object->name = $bObject->name;
		$object->description = $bObject->description;
		$object->liveOnFrontend = $bObject->liveOnFrontend;
		$object->graphOnFrontend = $bObject->graphOnFrontend;
		$object->initialkwh = $bObject->initialkwh;
		$object->producesSince = $bObject->producesSince;
		$object->expectedkwh = $bObject->expectedkwh;
		$object->heading = $bObject->heading;
		$object->correctionFactor = $bObject->correctionFactor;
		$object->comAddress = $bObject->comAddress;
		$object->comLog = $bObject->comLog;
		$object->syncTime = $bObject->syncTime;
		$object->pvoutputEnabled = ($bObject->pvoutputEnabled != "") ? $bObject->pvoutputEnabled : $object->pvoutputEnabled;
		$object->pvoutputApikey = $bObject->pvoutputApikey;
		$object->pvoutputSystemId = $bObject->pvoutputSystemId;
		$object->pvoutputWSLTeamMember = $bObject->pvoutputWSLTeamMember;
		$object->state = $bObject->state;
		$object->refreshTime = (isset($bObject->refreshTime) && $bObject->refreshTime != "") ? $bObject->refreshTime : $object->refreshTime;
		
		// TODO :: retrieve by panelService
		//$object->panels = $this->readPanelsByInverter($object->id);
		
		$object->expectedJAN = ($bObject->expectedJAN!='NaN') ? $bObject->expectedJAN : 0;
		$object->expectedFEB = ($bObject->expectedFEB!='NaN') ? $bObject->expectedFEB : 0;
		$object->expectedMAR = ($bObject->expectedMAR!='NaN') ? $bObject->expectedMAR : 0;
		$object->expectedAPR = ($bObject->expectedAPR!='NaN') ? $bObject->expectedAPR : 0;
		$object->expectedMAY = ($bObject->expectedMAY!='NaN') ? $bObject->expectedMAY : 0;
		$object->expectedJUN = ($bObject->expectedJUN!='NaN') ? $bObject->expectedJUN : 0;
		$object->expectedJUL = ($bObject->expectedJUL!='NaN') ? $bObject->expectedJUL : 0;
		$object->expectedAUG = ($bObject->expectedAUG!='NaN') ? $bObject->expectedAUG : 0;
		$object->expectedSEP = ($bObject->expectedSEP!='NaN') ? $bObject->expectedSEP : 0;
		$object->expectedOCT = ($bObject->expectedOCT!='NaN') ? $bObject->expectedOCT : 0;
		$object->expectedNOV = ($bObject->expectedNOV!='NaN') ? $bObject->expectedNOV : 0;
		$object->expectedDEC = ($bObject->expectedDEC!='NaN') ? $bObject->expectedDEC : 0;
		
		return $object;
	}
}
?>