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
		$inverter = $args[1];
		$live = $args[2];
		$timestamp = $args[3];
		$this->addSmartMeterHistory($inverter->id, $live, $timestamp);

		HookHandler::getInstance()->fire("newHistory", $inverter, $timestamp);

		// We are live, but db things offline
		if ($inverter->state == 0) {
			$this->adapter->changeInverterStatus($inverter, 1);
			$inverter->state == 1;
		}
		$sessionKey = 'noLiveCounter-' . $inverter->id;
		if (isset($_SESSION[$sessionKey])) {
			unset($_SESSION[$sessionKey]);
		}
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
		$inverter = $args[1];
		$live = $args[2];

		if ($inverter == null) {
			HookHandler::getInstance()->fire("onError", "CoreAddon::onLiveSmartMeterData() inverter == null");
			return;
		}

		// Save the live information
		$this->writeLiveSmartMeterInfo($inverter->id, $live);
		HookHandler::getInstance()->fire("newLiveData", $inverter, $live);
	}

	/**
	 * add the live info to the history
	 * @param int $invtnum
	 * @param Live $live
	 * @param string date
	 */
	public function addSmartMeterHistory($invtnum, LiveSmartMeter $live,$timestamp) {
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

	/**
	 * write the LiveSmartMeter info to the file
	 * @param int $invtnum
	 * @param Live $live
	 */
	public function writeLiveSmartMeterInfo($invtnum,LiveSmartMeter $live) {
		$bean =  R::findOne('liveSmartMeter','invtnum = :invtnum ',array(':invtnum'=>$invtnum));

		if (!$bean){
			$bean = R::dispense('liveSmartMeter');
		}

		$readLiveBean = $this->readLiveSmartMeterInfo($invtnum);

		if ($readLiveBean->gasUsage < $live->gasUsage) {
			$liveGas = ($live->gasUsage - $readLiveBean->gasUsage);
		} else {
			$liveGas = $readLiveBean->liveGas;
		}
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
		return R::store($bean);
	}

	/**
	 * read the live info from an file
	 * @param int $invtnum
	 * @return LiveSmartMeter
	 */
	public function readLiveSmartMeterInfo($invtnum) {
		$bean =  R::findOne('liveSmartMeter',' invtnum = :invtnum ', array(':invtnum'=>$invtnum));

		$live = new LiveSmartMeter();
		if ($bean) {
			$live->invtnum = $bean->invtnum;
			$live->liveGas = $bean->liveGas;
			$live->gasUsage = $bean->gasUsage;
			$live->highReturn = $bean->highReturn;
			$live->lowReturn = $bean->lowReturn;
			$live->highUsage = $bean->highUsage;
			$live->lowUsage = $bean->lowUsage;
			$live->liveReturn = $bean->liveReturn;
			$live->liveUsage = $bean->liveUsage;
			$live->liveEnergy = intval($live->liveUsage) - intval($live->liveReturn);
		}

		return $live;
	}


	/**
	 * will remove the live file
	 */
	public function dropSmartMeterLiveInfo($invtnum) {
		$bean =  R::findOne('liveSmartMeter',' invtnum = :invtnum  ',array(':invtnum'=>$invtnum));
		R::trash( $bean );
	}

	public function defaultAxes(){
		$graph = new Graph();
		$graph->axes['y2axis'] = array('label'=>_("Cum.").''._("(W)"),'min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
		$graph->axes['y3axis'] = array('label'=>_("Gas").''._("(L)"),'min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
		$graph->axes['y4axis'] = array('label'=>_("Actual").''._("(W)"),'min'=>$minActual,'max'=>$maxActual,'labelRenderer'=>'CanvasAxisLabelRenderer');
					
	}

	public function defaultSeries(){
		$graph = new Graph();
		$graph->series[] = array('label'=>_("Cum.").' '._("Gas").''._("(l)"),'yaxis'=>'y2axis');
		$graph->series[] = array('label'=>_("Smooth").' '._("Gas").''._("(l)") ,'yaxis'=>'y2axis');
		$graph->series[] = array('label'=>_("Cum.").' '._("low").' '._("usage").''._("(W)"),'yaxis'=>'y2axis');
		$graph->series[] = array('label'=>_("Cum.").' '._("high").' '._("usage").''._("(W)"),'yaxis'=>'y2axis');
		$graph->series[] = array('label'=>_("Cum.").' '._("low").' '._("return").''._("(W)"),'yaxis'=>'y2axis');
		$graph->series[] = array('label'=>_("Cum.").' '._("high").' '._("return").''._("(W)") ,'yaxis'=>'y2axis');
		$graph->series[] = array('label'=>_("Low").' '._("usage").''._("(W)") ,'yaxis'=>'y3axis');
		$graph->series[] = array('label'=>_("High").' '._("usage").''._("(W)") ,'yaxis'=>'y3axis');
		$graph->series[] = array('label'=>_("Low").' '._("return").''._("(W)"),'yaxis'=>'y3axis');
		$graph->series[] = array('label'=>_("High").' '._("return").''._("(W)"),'yaxis'=>'y3axis');
		$graph->series[] = array('label'=>_("Actual").' '._("usage").''._("(W)"),'yaxis'=>'y4axis');
	}
	
	public function addSeries(){
		$graph = new Graph();
		$graph = $this->defaultSeries();
		
	}
	
	
	
	public function DayBeansToGraphPoints($beans,$startDate){
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
			(!$minActual) ? $minActual = 0 : $minActual = $minActual;
			(!$maxActual) ? $maxActual = 0 : $maxActual = $maxActual;
			($actualUsage<$minActual) ? $minActual = $actualUsage : $actualUsage = $actualUsage;
			($actualUsage>$maxActual) ? $maxActual = $actualUsage : $actualUsage = $actualUsage;

			$graph->points['actualUsage'][] = array ($UTCdate ,round(trim($actualUsage),0));				
			$preBean = $bean;
			$i++;
		}
		
		// see if we have more then 1 bean (the dummy bean)
		if($i > 1){
			/* Set Serie Labels, The order of the labels needs to be the same as the points above*/
			/*$graph->series[] = array('label'=>_("Cum.").' '._("Gas").''._("(l)"),'yaxis'=>'y2axis');
			$graph->series[] = array('label'=>_("Smooth").' '._("Gas").''._("(l)") ,'yaxis'=>'y2axis');
			$graph->series[] = array('label'=>_("Cum.").' '._("low").' '._("usage").''._("(W)"),'yaxis'=>'y2axis');
			$graph->series[] = array('label'=>_("Cum.").' '._("high").' '._("usage").''._("(W)"),'yaxis'=>'y2axis');
			$graph->series[] = array('label'=>_("Cum.").' '._("low").' '._("return").''._("(W)"),'yaxis'=>'y2axis');
			$graph->series[] = array('label'=>_("Cum.").' '._("high").' '._("return").''._("(W)") ,'yaxis'=>'y2axis');
			$graph->series[] = array('label'=>_("Low").' '._("usage").''._("(W)") ,'yaxis'=>'y3axis');
			$graph->series[] = array('label'=>_("High").' '._("usage").''._("(W)") ,'yaxis'=>'y3axis');
			$graph->series[] = array('label'=>_("Low").' '._("return").''._("(W)"),'yaxis'=>'y3axis');
			$graph->series[] = array('label'=>_("High").' '._("return").''._("(W)"),'yaxis'=>'y3axis');
			$graph->series[] = array('label'=>_("Actual").' '._("usage").''._("(W)"),'yaxis'=>'y4axis');
			*/
			$graph->timestamp = Util::getBeginEndDate('day', 1,$startDate);
			($maxActual>0) ? $maxActual = ceil( ( ($maxActual*1.1)+100) / 100 ) * 100 : $maxActual= $maxActual;
			($minActual<0) ? $minActual = ceil( ( ($minActual*1.1)+100) / 100 ) * 100 : $minActual = $minActual;
			/*$graph->axes['y2axis'] = array('label'=>_("Cum.").''._("(W)"),'min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
			$graph->axes['y3axis'] = array('label'=>_("Gas").''._("(L)"),'min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
			$graph->axes['y4axis'] = array('label'=>_("Actual").''._("(W)"),'min'=>$minActual,'max'=>$maxActual,'labelRenderer'=>'CanvasAxisLabelRenderer');
			*/
			// default hide graph lines.
			$graph->metaData['hideSeries']= array(
					'label'=>array(
							$graph->series[0]['label'],
							$graph->series[1]['label'],
							$graph->series[2]['label'],
							$graph->series[3]['label'],
							$graph->series[4]['label'],
							$graph->series[5]['label'],
							$graph->series[6]['label'],
							$graph->series[7]['label'],
							$graph->series[8]['label'],
							$graph->series[9]['label']),
					);
			
			//graph specific legenda.
			$graph->metaData['legend']= array(
							"show"=>true,
							"location"=>'s',
							"placement"=>'outsideGrid',
							"renderer"=>'EnhancedLegendRenderer',
							"rendererOptions"=>array(
									"seriesToggle"=>'normal',
									"numberRows"=>2
						),
					"left"=>10,
					"width"=>700,
			);
			
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
	// Hook fired with ("GraphDayPoints",$invtnum,$startDate,$type);
	public function GraphDayPoints($args){

		($args[3] == 'today')?$type='day':$type=$args[3];
		$beans = $this->adapter->readTablesPeriodValues($args[1], 'historySmartMeter', $type, $args[2]);
		$beans = $this->DayBeansToGraphPoints($beans,$args[2]);
		return $beans;
	}

	/*
	 public function getSmartMeterTotals(){
	$energySmartMeter = $this->adapter->readTablesPeriodValues(0, "energySmartMeter", "today", date("d-m-Y"));
	var_dump();
	}*/


}?>