<?php
class CacheAddon {
	private $adapter;
	private $config;
	
	function __construct() {
		$this->adapter = PDODataAdapter::getInstance();
		$this->config = Session::getConfig();
	}
	
	function __destruct() {
		$this->config = null;
		$this->adapter = null;
	}
	
	public function EnergyValues(){
		$invnum = 0;
		
		$energyArray = $this->adapter->getMaxTotalEnergyValues($invnum,'all');
		ksort($energyArray);
		
		$timestamp = time();
		foreach ($energyArray as $key=>$value){
			if (!is_array($value)) {
				//set Cache Object
				$cache = new Cache();
				$cache->key = $key.'-'.$invnum;
				$cache->value = $value;
				$cache->module = 'periodFigures';
				$cache->page = 'index';
				$cache->timestamp = $timestamp;
				//save cache
				$this->adapter->saveCache($cache);
				/////////////////////////
			}
		}
	}
	
	
	public function averagePower($args) {
		$timestamp = time();
		foreach (Session::getConfig()->inverters as $device){

			$deviceNum = $device->id;
			
			$recentBegin = time()-400;
			$recentEnd = time();
			
			$pastBegin = time()-800;
			$pastEnd = time()-400;
			if ($deviceNum > 0){
				$queryWhere = " inv = ". $deviceNum ." AND ";
			}else{
				$queryWhere = "";
			}
			$query = "SELECT  avg(GP) AS avgGP FROM 'history' WHERE ".$queryWhere." time > :begin AND  time < :end ORDER BY time DESC";
			
			$avgRecent =  R::getAll($query,array(':begin'=>$recentBegin,':end'=>$recentEnd));
			$avgPast   =  R::getAll($query,array(':begin'=>$pastBegin,':end'=>$pastEnd));
			
			
			
			$average['recent'] = ($avgRecent[0]['avgGP']>0)? $avgRecent[0]['avgGP']: '0';
			$average['past'] = ($avgPast[0]['avgGP']>0)? $avgPast[0]['avgGP']:'0';
			
			$live = $this->adapter->readLiveInfo($device->id);
			$average['recent'] = ($live->GP + $average['recent'])/2;
			
			if($average['recent']>$average['past']){
				$trend = "up";
			}elseif($average['recent']<$average['past']){
				$trend = "down";
			}else{
				$trend = "equal";
			}
			$cache = new Cache();

			//save trend
			$cache->key = 'trend-'.$device->id;
			$cache->value = $trend;
			$cache->module = 'live';
			$cache->page = 'index';
			$cache->timestamp = $timestamp;
			$this->adapter->saveCache($cache);
			/////////////////////////
			
			//save pastAvg
			/////////////////////////
			$cache->key = 'pastAvg-'.$device->id;
			$cache->value = $average['past'];
			$cache->module = 'live';
			$cache->page = 'index';
			$cache->timestamp = $timestamp;
			$this->adapter->saveCache($cache);
			/////////////////////////
			
			//save recentAvg
			/////////////////////////
			$cache->key = 'recentAvg-'.$device->id;
			$cache->value = $average['recent'];
			$cache->module = 'live';
			$cache->page = 'index';
			$cache->timestamp = $timestamp;
			$this->adapter->saveCache($cache);
			/////////////////////////

		}
	}
}
?>