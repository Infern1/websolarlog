<?php 

class Rb {
	
	function __construct() {
		$basepath = dirname(dirname(__FILE__));
		include($basepath . "/R.php");
		
    	$config = new Config();
    	R::setup('sqlite:C:/wamp/www/websolarlog/trunk/database/'.$config->dbHost,'a','a');
	
	} //end __contruct()
} //end Rb


?>