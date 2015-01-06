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
            $beginEndDate = Util::getBeginEndDate('today', 1);
            $parameters = array(":beginDate"=>$beginEndDate['beginDate'],":endDate"=>$beginEndDate['endDate']);
            // get the last record of the history table for today and this device
            $beans =  R::getAll('SELECT deviceId,name,kwh as KWHUsage,kwht,time from GetVeraDevice where name != "" AND time > :beginDate AND time < :endDate order by name',$parameters);
            $beginEndDate = Util::getBeginEndDate('month', 1);
            $parameters = array(":beginDate"=>$beginEndDate['beginDate'],":endDate"=>$beginEndDate['endDate']);
            $beansMonths =  R::getAll('SELECT name, sum(KWH) as KWHUsage from GetVeraDevice where name != "" AND time > :beginDate AND time < :endDate group by deviceId order by name',$parameters);
            
            return array("today"=>$beans,"month"=>$beansMonths);
	}
}