<?php
class DomoticaAddon {

	function __construct() {
		// Initialize objects
		$this->config = Session::getConfig();
	}
	
	public function onJob(){

	}
	
	public function getAllData() {
		//$domotica = new DomoticaService();
		
		$beans =  R::getAll('SELECT deviceId,name,kwh,kwht,time from GetVeraDevice where name != "" order by deviceId,time');
		return $beans;
	}
}