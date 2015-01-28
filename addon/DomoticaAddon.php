<?php
class DomoticaAddon {

	function __construct() {
		// Initialize objects
		$this->config = Session::getConfig();
	}
	
	public function onJob(){

	}
	
        public function getAlldevices(){
            return R::getAll('SELECT DISTINCT ( deviceid ) , name FROM getVeraDevice GROUP BY deviceId');
            
        }
        
	public function getAllData() {
            //$domotica = new DomoticaService();
            $today = Util::getBeginEndDate('today', 1);
            $parameters = array(":beginDate"=>$today['beginDate'],":endDate"=>$today['endDate']);
            // get the last record of the history table for today and this device
            $beans =  R::getAll('SELECT deviceId,name,kwh as KWHUsage,kwht,time from GetVeraDevice where name != "" AND time > :beginDate AND time < :endDate order by name',$parameters);
            $beginEndDate = Util::getBeginEndDate('month', 1);
            $parameters = array(":beginDate"=>$beginEndDate['beginDate'],":endDate"=>$beginEndDate['endDate']);
            
            $beansMonths =  R::getAll('SELECT deviceId,name, sum(KWH) as KWHUsage, avg(KWH) as KWHAvg from GetVeraDevice where name != "" AND time > :beginDate AND time < :endDate group by deviceId order by name',$parameters);

            $beginEndDate = Util::getBeginEndDate('year', 1);
            $parameters = array(":beginDate"=>$beginEndDate['beginDate'],":endDate"=>$beginEndDate['endDate']);
            $beansYear =  R::getAll('SELECT deviceId,name, sum(KWH) as KWHUsage, avg(KWH) as KWHAvg from GetVeraDevice where name != "" AND time > :beginDate AND time < :endDate group by deviceId order by name',$parameters);

            $parameters = array(":beginDate"=>0,":endDate"=>$today['endDate']);
            $beansAlltime =  R::getAll('SELECT deviceId,name, sum(KWH) as KWHUsage, avg(KWH) as KWHAvg from GetVeraDevice where name != "" AND time > :beginDate AND time < :endDate group by deviceId order by name',$parameters);

            return array("today"=>$beans,"month"=>$beansMonths,"year"=>$beansYear,"overall"=>$beansAlltime);
	}
}