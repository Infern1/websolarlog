<?php 
class PanelRest {

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
	
	
	public function DELETE($request, $options) {
		return array("status"=>"Deleted: id=" . $options[0]);
	}
}

?>