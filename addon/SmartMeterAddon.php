<?php
class SmartMeterAddon {
	private $adapter;
	private $config;
	private $deviceService;
	private $liveSmartMeterService;
	private $addon = "smartMeter";
	private $install = false;
	
	function __construct() {
		$this->adapter = PDODataAdapter::getInstance();
		$this->deviceService = new DeviceHistoryService();
		$this->liveSmartMeterService = new LiveSmartMeterService();
		$this->config = Session::getConfig();
		
		foreach ($this->config->devices as $device) {
			if($device->type == "metering"){
				$this->install = true;
			}
		}
	}

	function __destruct() {
		$this->liveSmartMeterService = null;
		$this->deviceService = null;
		$this->config = null;
		$this->adapter = null;
		$this->install = null;
	}

	
	public function installGraph(){
		if($this->install==true){
			HookHandler::getInstance()->fire("onDebug", "install SmartMeter");
			$graph = R::dispense('graph');
	
			$graph->series = self::defaultSeries();
			//var_dump(json_decode($graph->series[0]['json'])->label);
			$graph->metaData = array(
					'legend'=>array(
						"show"=>true,
						"location"=>'s',
						"placement"=>'outsideGrid',
						"renderer"=>'EnhancedLegendRenderer',
						"rendererOptions"=>array(
								"seriesToggle"=>'normal',
								"numberRows"=>2
						),
						"left"=>10,
						"width"=>700
					)
			);
			return $graph;
		}else{
			return;
		}
	}
	
	/**
	 * Handle hook onHistory
	 * @param unknown $args
	 */
	public function onSmartMeterHistory($args) {
		$device = $args[1];
		$live = $args[2];
		$timestamp = $args[3];
		$this->addSmartMeterHistory($device->id, $live, $timestamp);

		// We are live, but db things offline
		if ($device->state == 0) {
			$this->deviceHistory->changeDeviceStatus($device, 1);
			$device->state == 1;
		}
		$sessionKey = 'noLiveCounter-' . $device->id;
		if (isset($_SESSION[$sessionKey])) {
			unset($_SESSION[$sessionKey]);
		}
	}

	public function onSmartMeterEnergy($args) {
		$device = $args[1];

		$arHistory = $this->readSmartMeterHistory($device->id, null);

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

			$gasUsage = $gasUsageEnd - $gasUsageStart;
			$highReturn = $highReturnEnd - $highReturnStart;
			$lowReturn = $lowReturnEnd - $lowReturnStart;
			$highUsage = $highUsageEnd - $highUsageStart;
			$lowUsage = $lowUsageEnd - $lowUsageStart;
		}

		// Set the new values and save it
		$energy = new EnergySmartMeter();
		$energy->time = $args[2];
		$energy->INV = $device->id;
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
		$this->addSmartMeterEnergy($device->id, $energy);

		HookHandler::getInstance()->fire("newSmartMeterEnergy", $device, $energy);
	}


	/**
	 * Handle hook onLiveSmartMeterData
	 * @param unknown $args
	 */
	public function onLiveSmartMeterData($args) {
		$device = $args[1];
		$live = $args[2];
		
		if ($device == null) {
			HookHandler::getInstance()->fire("onError", "CoreAddon::onLiveSmartMeterData() device == null");
			return;
		}
		
		// Get the current live object
		$dbLive = $this->liveSmartMeterService->getLiveByDevice($device);
		$live->liveGas = 0;
		if ($dbLive) {
			$live->id = $dbLive->id;
			
			// Calculate the gass
			$live->liveGas = ($live->gasUsage - $dbLive->gasUsage);
		}
		
		$this->liveSmartMeterService->save($live);

		HookHandler::getInstance()->fire("newLiveData", $device, $live);
	}

	/**
	 * add the live info to the history
	 * @param int $invtnum
	 * @param Live $live
	 * @param string date
	 */
	public function addSmartMeterHistory($invtnum, LiveSmartMeter $live,$timestamp) {
		$live->deviceId = $invtnum;
		$live->invtnum = $invtnum;
		
		$bean = R::dispense('historySmartMeter');

		// check if we have a "valid" Bean to store.
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
			$bean = R::dispense('energySmartMeter');
		} else {
			$oldGasUsageT = $energy->gasUsageT;
			$oldHighReturnT = $energy->highReturnT;
			$oldLowReturnT = $energy->lowReturnT;
			$oldHighUsageT = $energy->highUsageT;
			$oldLowUsageT = $energy->lowUsageT;
		}

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

		return R::store($bean);
	}

	public function defaultAxes(){
		$show = 'false';
		foreach ($this->config->devices as $device) {
			if($device->type == "metering"){
				$show = 'true';
			}
		}
		
			$axe = R::dispense('axe',2);
			
		//$axe[0]['json'] = json_encode(json_encode(array('axe'=>'yaxis','label'=>'Avg. Power(Wh)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer'));
		$axe[0]['json'] = json_encode(array('axe'=>'y3axis','label'=>_("Gas").' '._("(L)"),'min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer'));
		$axe[0]['show'] = $show;
		$axe[0]['AxeOrder'] = 3;
		$axe[0]['addon'] = $addon;
		$axe[1]['json'] = json_encode(array('axe'=>'y4axis','label'=>_("Actual").''._("(W)"),'labelRenderer'=>'CanvasAxisLabelRenderer'));
		$axe[1]['show'] = $show;
		$axe[1]['AxeOrder'] = 4;
		$axe[1]['addon'] = $addon;

		return $axe;
	}

	public function defaultSeries(){
		$show = 'false';
		foreach ($this->config->devices as $device) {
			if($device->type == "metering"){
				$show = 'true';
			}
		}

		$serie = R::dispense('serie',11);
		//$serie[0]['json'] = json_encode(json_encode(array('label'=>'Cum. Power(Wh)','yaxis'=>'y2axis'));
		$serie[0]['json'] = json_encode(array('label'=>'Cum Gas (l)','yaxis'=>'y2axis'));
		$serie[0]['name'] = 'cumGasL';
		$serie[0]['disabled'] = 'false';
		$serie[0]['show'] = $show;
		$serie[0]['addon'] = $addon;
		
		$serie[1]['json'] = json_encode(array('label'=>'Smooth Gas (l)','yaxis'=>'y2axis'));
		$serie[1]['name'] = 'smoothGasL';
		$serie[1]['disabled'] = 'false';
		$serie[1]['show'] = $show;
		$serie[1]['addon'] = $addon;
		
		$serie[2]['json'] = json_encode(array('label'=>'Cum low usage (W)','yaxis'=>'y2axis'));
		$serie[2]['name'] = 'cumLowUsageW';
		$serie[2]['disabled'] = 'false';
		$serie[2]['show'] = $show;
		$serie[2]['addon'] = $addon;
		
		$serie[3]['json'] = json_encode(array('label'=>'Cum high usage (W)','yaxis'=>'y2axis'));
		$serie[3]['name'] = 'cumHighUsageW';
		$serie[3]['disabled'] = 'false';
		$serie[3]['show'] = $show;
		$serie[3]['addon'] = $addon;
		
		$serie[4]['json'] = json_encode(array('label'=>'Cum low return (W)','yaxis'=>'y2axis'));
		$serie[4]['name'] = 'cumLowReturnW';
		$serie[4]['disabled'] = 'false';
		$serie[4]['show'] = $show;
		$serie[4]['addon'] = $addon;
		
		$serie[5]['json'] = json_encode(array('label'=>'Cum high return (W)','yaxis'=>'y2axis'));
		$serie[5]['name'] = 'cumHighReturnW';
		$serie[5]['disabled'] = 'false';
		$serie[5]['show'] = $show;
		$serie[5]['addon'] = $addon;
		
		$serie[6]['json'] = json_encode(array('label'=>'Low usage (W)' ,'yaxis'=>'y3axis'));
		$serie[6]['name'] = 'lowUsageW';
		$serie[6]['disabled'] = 'false';
		$serie[6]['show'] = $show;
		$serie[6]['addon'] = $addon;
		
		$serie[7]['json'] = json_encode(array('label'=>'High usage (W)' ,'yaxis'=>'y3axis'));
		$serie[7]['name'] = 'highUsageW';
		$serie[7]['disabled'] = 'false';
		$serie[7]['show'] = $show;
		$serie[7]['addon'] = $addon;
		
		$serie[8]['json'] = json_encode(array('label'=>'Low return (W)','yaxis'=>'y3axis'));
		$serie[8]['name'] = 'lowReturnW';
		$serie[8]['disabled'] = 'false';
		$serie[8]['show'] = $show;
		$serie[8]['addon'] = $addon;
		
		$serie[9]['json'] = json_encode(array('label'=>'High return (W)','yaxis'=>'y3axis'));
		$serie[9]['name'] = 'highReturnW';
		$serie[9]['disabled'] = 'false';
		$serie[9]['show'] = $show;
		$serie[9]['addon'] = $addon;
		
		$serie[10]['json'] = json_encode(array('label'=>'Actual usage (W)','yaxis'=>'y4axis'));
		$serie[10]['name'] = 'actualUsageW';
		$serie[10]['disabled'] = 'false';
		$serie[10]['show'] = $show;
		$serie[10]['addon'] = $addon;
		
		return $serie;
	}
	/**
	 * 
	 */
	public function addSeries(){
		$graph = new Graph();
		$graph = $this->defaultSeries();
	}
	
	/**
	 * 
	 * @param unknown $beans
	 * @param unknown $startDate
	 * @return Graph
	 */
	public function DayBeansToGraphPoints($beans,$startDate,$disabledSeries){
$graph = new Graph();
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
			$graph->points['cumGasUsage'][] = array ($UTCdate ,$bean['gasUsage']-$firstBean['gasUsage'],date("H:i, d-m-Y",$bean['time']));
			if($i==0){
				$graph->points['smoothGasUsage'][] = array ($UTCdate ,$firstBean['gasUsage']-$bean['gasUsage']);
			}
			
			if( $bean['gasUsage']-$firstBean['gasUsage'] != $preBean['gasUsage']-$firstBean['gasUsage']){
				$graph->points['smoothGasUsage'][] = array ($UTCdate,$bean['gasUsage']-$firstBean['gasUsage']);
			}
			$graph->points['cumLowUsage'][] = array ($UTCdate,$bean['lowUsage']-$firstBean['lowUsage']);
			$graph->points['cumHighUsage'][] = array ($UTCdate ,$bean['highUsage']-$firstBean['highUsage']);
			$graph->points['cumLowReturn'][] = array ($UTCdate ,$bean['lowReturn']-$firstBean['lowReturn']);
			$graph->points['cumHighReturn'][] = array ($UTCdate ,$bean['highReturn']-$firstBean['highReturn']);
			
			$lowUsage = Formulas::calcAveragePower($preBean['lowUsage'], $bean['lowUsage'], $preBean['time']-$bean['time'])/1000;
			
			$highUsage = Formulas::calcAveragePower($preBean['highUsage'], $bean['highUsage'], $preBean['time']-$bean['time'])/1000;
			$lowReturn = Formulas::calcAveragePower($preBean['lowReturn'], $bean['lowReturn'], $preBean['time']-$bean['time'])/1000;
			$highReturn = Formulas::calcAveragePower($preBean['highReturn'], $bean['highReturn'], $preBean['time']-$bean['time'])/1000;

			$lowActual = $lowUsage-$lowReturn;
			$highActual =  $highUsage-$highReturn;
			$actualUsage = (int)0;
			($lowActual!=0) ?	$actualUsage = $lowActual :	$actualUsage = $highActual;

			$graph->points['lowUsage'][] = array ($UTCdate ,$lowUsage);
			$graph->points['highUsage'][] = array ($UTCdate ,$highUsage);
			$graph->points['lowReturn'][] = array ($UTCdate ,$lowReturn);
			$graph->points['highReturn'][] = array ($UTCdate ,$highReturn);
			(!isset($minActual)) ? $minActual = 0 : $minActual = $minActual;
			(!isset($maxActual)) ? $maxActual = 0 : $maxActual = $maxActual;
			($actualUsage<$minActual) ? $minActual = $actualUsage : $actualUsage = $actualUsage;
			($actualUsage>$maxActual) ? $maxActual = $actualUsage : $actualUsage = $actualUsage;

			$graph->points['actualUsage'][] = array ($UTCdate ,round(trim($actualUsage),0));				
			$preBean = $bean;
			$i++;
		}
		
		// see if we have more then 1 bean (the dummy bean)
		if($i > 1){
			
			$graph->timestamp = Util::getBeginEndDate('day', 1,$startDate);
			($maxActual>0) ? $maxActual = ceil( ( ($maxActual*1.1)+100) / 100 ) * 100 : $maxActual= $maxActual;
			($minActual<0) ? $minActual = ceil( ( ($minActual*1.1)+100) / 100 ) * 100 : $minActual = $minActual;
			
			
		}else{
			$graph->points=null;
		}
		return $graph;
	}


	/**
	 * return a array with GraphPoints
	 * @param date $labels[] = 'High Power(W)';$startDate ("Y-m-d") ("1900-12-31"), when no date given, the date of today is used.
	 * @return array($beginDate, $endDate);
	 */
	// Hook fired with ("GraphDayPoints",$invtnum,$startDate,$type,$hiddenSeries);
	public function GraphDayPoints($args){
		(strtolower($args[3]) == 'today')?$type='day':$type=$args[3];
		$graphDataService = new GraphDataService();
		$beans = $graphDataService->readTablesPeriodValues($args[1], 'historySmartMeter', $type, $args[2]);
		$beans = $this->DayBeansToGraphPoints($beans,$args[2],$args[4]);
		return $beans;
	}
}
?>