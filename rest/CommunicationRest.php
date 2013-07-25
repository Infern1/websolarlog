<?php
class CommunicationRest {
	private $communicationService;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->communicationService = new CommunicationService();
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
		$this->communicationService = null;
	}

	/**
	 * Rest functions 
	 */
	public function GET($request, $options) {
		$id = -1;
		if (count($options) > 0) {
			$id = (trim($options[0]) != "") ? strtolower($options[0]) : $type;
		}
		if ($id > 0) {
			return $this->communicationService->load($id);
		}
		return $this->communicationService->getList();
	}
	
	public function POST($request, $options) {
		if (!Session::isLogin()) {
			throw new AuthenticationException("Not enough rights");
		}
		
		// Try to fill the object based on its public properties
		$postCommunication = new Communication();
		$publicProperties = get_object_vars($postCommunication);
		foreach ($publicProperties as $key=>$value) {
			if (isset($_POST[$key])) {
				$postCommunication->$key = $_POST[$key];
			}
		}
		
		return $this->communicationService->save($postCommunication);
	}
	
	/**
	 * Returns devices with just a few fields
	 * @param unknown $options
	 * @return multitype:multitype:NULL
	 */
	public function getShortList($options) {
		$result = array();
		foreach (Session::getConfig()->devices as $device) {
			$result[] = array("id"=>$device->id, "name"=>$device->name,"type"=>$device->type);
		}
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $request
	 * @param unknown $options
	 */
	public function DELETE($request, $options){
		return $this->deviceService->delete($options[0]);
	}
	
	public function getStartTest($request) {
		$communicationId = Common::getValue("communicationId", -1);
		$deviceId = Common::getValue("deviceId", -1);
		return array("results"=>"in development", "communicationId"=>$communicationId, "deviceId"=>$deviceId);
	}
}
?>