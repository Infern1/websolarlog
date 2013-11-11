<?php 

class PVoutputRest {
	function __construct() {
		$this->config = Session::getConfig();
	}
	public function addStatus() {
		foreach ($this->config->devices as $device) {
			HookHandler::getInstance()->fire('onInfo', "Run PVoutput add status");
			$vars = HookHandler::getInstance()->fire("onPVoutputAddStatus",$device);
			HookHandler::getInstance()->fire('onInfo', print_r($vars,true));
		}
	}
}
?>