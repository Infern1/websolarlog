<?php
class SmartMeterAddon {
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

	/**
	 * Handle hook onHistory
	 * @param unknown $args
	 */
	public function onSmartMeterHistory($args) {
		//echo "CoreAddon onSmartMeterHistory";
		$inverter = $args[1];
		$live = $args[2];
		$timestamp = $args[3];
		//var_dump($live);
		$this->addSmartMeterHistory($inverter->id, $live,$timestamp);
		HookHandler::getInstance()->fire("newHistory", $inverter, $timestamp);
	}
	
	public function onSmartMeterEnergy($args) {
		$inverter = $args[1];
	
		$arHistory = $this->readSmartMeterHistory($inverter->id, null);
		
		// Initialize the variables we dont want errors in the logs
		$gasUsage = 0;
		$highReturn = 0;
		$lowReturn = 0;
		$highUsage = 0;
		$lowUsage = 0;
		$gasUsageEnd = 0;
		$highReturnEnd = 0;
		$lowReturnEnd = 0;
		$highUsageEnd = 0;
		$lowUsageEnd = 0;
	
		if(count($arHistory)>1){
			$first = reset($arHistory);
			$last = end($arHistory);
	
	
			$gasUsageStart = $first['gasUsage'];
			$gasUsageEnd = $last['gasUsage'];
				
			$highReturnStart = $first['highReturn'];
			$highReturnEnd = $last['highReturn'];
				
			$lowReturnStart = $first['lowReturn'];
			$lowReturnEnd = $last['lowReturn'];
				
			$highUsageStart = $first['highUsage'];
			$highUsageEnd = $last['highUsage'];
				
			$lowUsageStart = $first['lowUsage'];
			$lowUsageEnd = $last['lowUsage'];
	
			// Check if we passed 100.000kWh
			//if ($productionEnd < $productionStart) {
			//	$productionEnd += 100000;
				//}
				$gasUsage = $gasUsageEnd - $gasUsageStart;
				$highReturn = $highReturnEnd - $highReturnStart;
				$lowReturn = $lowReturnEnd - $lowReturnStart;
				$highUsage = $highUsageEnd - $highUsageStart;
				$lowUsage = $lowUsageEnd - $lowUsageStart;
			}	
	
			// Set the new values and save it
			$energy = new EnergySmartMeter();
			$energy->time = $args[2];
			$energy->INV = $inverter->id;
			$energy->gasUsage = $gasUsage;
			$energy->highReturn = $highReturn;
			$energy->lowReturn = $lowReturn;
			$energy->highUsage = $highUsage;
			$energy->lowUsage = $lowUsage;
	
			$energy->gasUsageT = $gasUsageEnd;
			$energy->highReturnT = $highReturnEnd;
			$energy->lowReturnT = $lowReturnEnd;
			$energy->highUsageT = $highUsageEnd;
			$energy->lowUsageT = $lowUsageEnd;
			$energy->co2 = Formulas::CO2gas($gasUsage, $this->config->co2kwh); // Calculate co2
			$this->addSmartMeterEnergy($inverter->id, $energy);
	
			HookHandler::getInstance()->fire("newSmartMeterEnergy", $inverter, $energy);
		}
	

	/**
	 * Handle hook onLiveSmartMeterData
	 * @param unknown $args
	 */
	public function onLiveSmartMeterData($args) {
		//echo "\r\nonLiveSmartMeterData\r\n";
		$inverter = $args[1];
		$live = $args[2];
	
		if ($inverter == null) {
			HookHandler::getInstance()->fire("onError", "CoreAddon::onLiveSmartMeterData() inverter == null");
			return;
		}
	
		// Save the live information
		//echo "\r\n".$inverter->id."\r\n";
		$this->writeLiveSmartMeterInfo($inverter->id, $live);
		HookHandler::getInstance()->fire("newLiveData", $inverter, $live);
	
		// Check the Max value
		//$this->checkMaxPowerValue($inverter, $live);
	}







	/**
	 * add the live info to the history
	 * @param int $invtnum
	 * @param Live $live
	 * @param string date
	 */
	public function addSmartMeterHistory($invtnum, LiveSmartMeter $live,$timestamp) {
		$bean = R::dispense('historySmartMeter');
	
		$bean->invtnum = $invtnum;
		$bean->gasUsage = $live->gasUsage;
		$bean->highReturn = $live->highReturn;
		$bean->lowReturn = $live->lowReturn;
		$bean->highUsage = $live->highUsage;
		$bean->lowUsage = $live->lowUsage;
		$bean->liveReturn = $live->liveReturn;
		$bean->liveUsage = $live->liveUsage;
		$bean->time = $timestamp;
	
		//Store the bean
		$id = R::store($bean);
		return $bean;
	}
	
	/**
	 * Read the history file
	 * @param int $invtnum
	 * @param string $date
	 * @return array<Live> $live (No Live but BEAN object!!)
	 */
	// TODO :: There's no Live object returned....?!
	
	public function readSmartMeterHistory($invtnum, $date) {
		(!$date)? $date = date('d-m-Y') : $date = $date;
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);
	
		$bean =  R::findAndExport( 'historySmartMeter',
				' invtnum = :invtnum AND time > :beginDate AND  time < :endDate order by time',
				array(':invtnum'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
		return $bean;
	}
	
	/**
	 * Return the amount off history records
	 * @param int $invtnum
	 * @param string $date
	 * @return int $count
	 */
	public function getSmartMeterHistoryCount($invtnum) {
		$date = date('d-m-Y');
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);
	
		$bean =  R::find('historySmartMeter',
				' invtnum = :invtnum AND time > :beginDate AND  time < :endDate order by time',
				array(':invtnum'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
		return count($bean);
	}
	
	
	
	/**
	 * write the max power today to the file
	 * @param int $invtnum
	 * @param MaxPowerToday $mpt
	 */
	public function addSmartMeterEnergy($invtnum, EnergySmartMeter $energy) {
		//echo "\r\naddSmartMeterEnergy\r\n";
	
		$date = date('d-m-Y');
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);
	
		$bean =  R::findone('energySmartMeter',
				' invtnum = :invtnum AND time > :beginDate AND  time < :endDate order by time',
				array(':invtnum'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
	
		$oldGasUsageT = 0;
		$oldHighReturnT = 0;
		$oldLowReturnT = 0;
		$oldHighUsageT = 0;
		$oldLowUsageT = 0;
		$oldLiveReturnT = 0;
		$oldLiveUsageT = 0;
	
	
		if (!$bean){
			//echo "\r\n dispence\r\n";
			$bean = R::dispense('energySmartMeter');
		} else {
			//echo "\r\n Non-Dispence \r\n";
			$oldGasUsageT = $energy->gasUsageT;
			$oldHighReturnT = $energy->highReturnT;
			$oldLowReturnT = $energy->lowReturnT;
			$oldHighUsageT = $energy->highUsageT;
			$oldLowUsageT = $energy->lowUsageT;
		}
	
		//var_dump($bean);
		$bean->invtnum = $invtnum;
		$bean->gasUsageT = $energy->gasUsageT;
		$bean->highReturnT = $energy->highReturnT;
		$bean->lowReturnT = $energy->lowReturnT;
		$bean->highUsageT = $energy->highUsageT;
		$bean->lowUsageT = $energy->lowUsageT;
	
		$bean->gasUsage = $energy->gasUsage;
		$bean->highReturn = $energy->highReturn;
		$bean->lowReturn = $energy->lowReturn;
		$bean->highUsage = $energy->highUsage;
		$bean->lowUsage = $energy->lowUsage;
		$bean->liveReturn = $energy->liveReturn;
		$bean->liveUsage = $energy->liveUsage;
		$bean->time = time();
	
	
		//Only store the bean when the value
		$id = -1;
	
		$id = R::store($bean,$bean->id);
	
		return $id;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * write the LiveSmartMeter info to the file
	 * @param int $invtnum
	 * @param Live $live
	 */
	public function writeLiveSmartMeterInfo($invtnum,LiveSmartMeter $live) {
		$bean =  R::findOne('liveSmartMeter','
				invtnum = :invtnum ',
				array(':invtnum'=>$invtnum)
		);
	
		if (!$bean){
			$bean = R::dispense('liveSmartMeter');
		}
	
		$readLiveBean = $this->readLiveSmartMeterInfo($invtnum);
	
		if($readLiveBean->gasUsage < $live->gasUsage){
			$liveGas = ($live->gasUsage - $readLiveBean->gasUsage);
		}else{
			$liveGas = $readLiveBean->liveGas;
		}
		//$bean->KWHT = $live->KWHT;
		$bean->gasUsage = $live->gasUsage;
		$bean->invtnum = $invtnum;
		$bean->liveGas = $liveGas;
		$bean->highReturn = $live->highReturn;
		$bean->lowReturn = $live->lowReturn;
		$bean->highUsage = $live->highUsage;
		$bean->lowUsage = $live->lowUsage;
		$bean->liveReturn = $live->liveReturn;
		$bean->liveUsage = $live->liveUsage;
		$bean->time = time();
	
		//Store the bean
		$id = R::store($bean,$bean->id);
		return $id;
	}
	
	/**
	 * read the live info from an file
	 * @param int $invtnum
	 * @return Live
	 */
	public function readLiveSmartMeterInfo($invtnum) {
		$bean =  R::findOne('liveSmartMeter',' invtnum = :invtnum ', array(':invtnum'=>$invtnum));
	
		$live = new LiveSmartMeter();
		if ($bean) {
			$live->liveGas = $bean->liveGas;
			$live->gasUsage = $bean->gasUsage;
			$live->highReturn = $bean->highReturn;
			$live->lowReturn = $bean->lowReturn;
			$live->highUsage = $bean->highUsage;
			$live->lowUsage = $bean->lowUsage;
			$live->liveReturn = $bean->liveReturn;
			$live->liveUsage = $bean->liveUsage;
	
		}
	
		return $live;
	}
	
	
	
	/**
	 * will remove the live file
	 */
	public function dropSmartMeterLiveInfo($invtnum) {
		$bean =  R::findOne('liveSmartMeter',
				' invtnum = :invtnum  ',
				array(':invtnum'=>$invtnum
				)
		);
		R::trash( $bean );
	}
	
	public function DayBeansToGraphPoints($beans){
		
		/*
		 * Generate Graph Point and series
		 */
		$i=0;
		foreach ($beans as $bean){
			
			
			if ($i==0){
				$firstBean = $bean;
				$preBean = $bean;
				$preBeanUTCdate = $bean['time'];
			}
			$UTCdate = $bean['time'];
			$UTCtimeDiff = $UTCdate - $preBean['time'];
			$graph['points']['cumGasUsage'][] = array ($UTCdate * 1000,$bean['gasUsage']-$firstBean['gasUsage'],date("H:i, d-m-Y",$bean['time']));
			if($i==0){
				$graph['points']['smoothGasUsage'][] = array ($UTCdate * 1000,$firstBean['gasUsage']-$bean['gasUsage'],date("H:i, d-m-Y",$bean['time']));
			}
			if( $bean['gasUsage']-$firstBean['gasUsage'] != 
				$preBean['gasUsage']-$firstBean['gasUsage']
				){
				$graph['points']['smoothGasUsage'][] = array ($UTCdate * 1000,$bean['gasUsage']-$firstBean['gasUsage'],date("H:i, d-m-Y",$bean['time']));
			}
			$graph['points']['cumLowUsage'][] = array ($UTCdate * 1000,$bean['lowUsage']-$firstBean['lowUsage'],date("H:i, d-m-Y",$bean['time']));
			$graph['points']['cumHighUsage'][] = array ($UTCdate * 1000,$bean['highUsage']-$firstBean['highUsage'],date("H:i, d-m-Y",$bean['time']));
			$graph['points']['cumLowReturn'][] = array ($UTCdate * 1000,$bean['lowReturn']-$firstBean['lowReturn'],date("H:i, d-m-Y",$bean['time']));
			$graph['points']['cumHighReturn'][] = array ($UTCdate * 1000,$bean['highReturn']-$firstBean['highReturn'],date("H:i, d-m-Y",$bean['time']));
			$preBean = $bean;
			$i++;
		}
		
		/*
		 * Set Serie Labels
		 * The order of the labels needs to be the same as the points above
		 */
		//$graph['labels'][] = 'Cum. Gas(l)';
		//$graph['labels'][] = 'Smooth Gas(l)';
		//$graph['labels'][] = 'Low Usage(W)';
		//$graph['labels'][] = 'High Usage (W)';
		//$graph['labels'][] = 'Low Return(W)';
		//$graph['labels'][] = 'High Return(W)';
		$lastDays = new LastDays();

		$graph['series'][] = array('label'=>'Cum. Gas(l)','yaxis'=>'y5axis');
		$graph['series'][] = array('label'=>'Smooth Gas(l)' ,'yaxis'=>'y5axis');
		$graph['series'][] = array('label'=>'Low Usage(W)' ,'yaxis'=>'y3axis');
		$graph['series'][] = array('label'=>'High Usage (W)','yaxis'=>'y4axis');
		$graph['series'][] = array('label'=>'Low Return(W)','yaxis'=>'y4axis');
		$graph['series'][] = array('label'=>'High Return(W)' ,'yaxis'=>'y4axis');

		
		
		$graph['axes']['y3axis'] = array('label'=>'Usage (W)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
		$graph['axes']['y4axis'] = array('label'=>'Return (W)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
		$graph['axes']['y5axis'] = array('label'=>'Gas (l)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');

		$lastDays->graph = $graph;
		return $lastDays;
	}	
	

	/**
	 * return a array with GraphPoints
	 * @param date $labels[] = 'High Power(W)';$startDate ("Y-m-d") ("1900-12-31"), when no date given, the date of today is used.
	 * @return array($beginDate, $endDate);
	 */
	public function GraphDayPoints($args){

		($args[3] == 'today')?$type='day':$type=$args[3];
		$beans = $this->DayBeansToGraphPoints($this->adapter->readTablesPeriodValues($args[1], 'historySmartMeter', $type, $args[2]));
		//var_dump($beans);
		return $beans;
	}
}?>