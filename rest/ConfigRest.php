<?php
class ConfigRest {
	
	/**
	 * Constructor
	 */
	function __construct() {
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
	}

	/**
	 * Rest functions 
	 */
	
	public function GET($request, $options) {
		$option = "";
		if (count($options) > 0) {
			$option = strtolower($options[0]);
		}
		if ($option == "upgrademessageshow") {
			return Session::getConfig()->upgradeMessageShow;
		}
		if ($option == "upgrademessage") {
			return isset(Session::getConfig()->upgradeMessage) ? Session::getConfig()->upgradeMessage : "";
		}
		return $option;
	}
}
?>