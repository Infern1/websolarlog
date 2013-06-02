<?php
class DeviceRest {
	private $deviceService;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->deviceService = new DeviceService();
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
		$this->deviceService = null;
	}

	/**
	 * Rest functions 
	 */
	public function GET($request, $options) {
		$result = array();
		foreach (Session::getConfig()->devices as $device) {
			$result[] = $this->deviceService->load($device->id);
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
}
?>