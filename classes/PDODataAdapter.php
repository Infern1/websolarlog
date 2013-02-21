<?php
class PDODataAdapter {
	private static $instance;
	public $ProcessTime;

	// Singleton
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new PDODataAdapter();
		}
		return self::$instance;
	}


	function __construct() {
		$config = new Config; // We dont need data from dbase
		R::setup('sqlite:'.$config->dbHost );
		R::debug(false);
		R::setStrictTyping(false);
	}

	function __destruct() {
		//print "Destroying " . $this->name . "\n";
	}

	
	public static function setProcessTime($timestamp=null){
		($timestamp) ? $this->ProcessTime = $timestamp : $ $this->ProcessTime=time();
	}
	
	/**
	 * write the live info to the file
	 * @param int $invtnum
	 * @param Live $live
	 */
	public function writeLiveInfo($invtnum, Live $live) {
		$bean =  R::findOne('live',' INV = :INV ', array(':INV'=>$invtnum));

		if (!$bean){
			$bean = R::dispense('live');
		}

		$bean->SDTE = date("Ymd-H:i:s");
		$bean->time = time();
		$bean->INV = $invtnum;

		$bean->I1V = $live->I1V;
		$bean->I1A = $live->I1A;
		$bean->I1P = $live->I1P;


		$bean->I2V = $live->I2V;
		$bean->I2A = $live->I2A;
		$bean->I2P = $live->I2P;
		$IP = $live->I1P+$live->I2P;

		// Prevent division by zero error
		if (!empty($IP)) {
			$bean->I1Ratio = ($live->I1P/$IP)*100;
			$bean->I2Ratio = ($live->I2P/$IP)*100;
		}

		$bean->GV = $live->GV;
		$bean->GA = $live->GA;
		$bean->GP = $live->GP;

		$bean->FRQ = $live->FRQ;
		$bean->EFF = $live->EFF;
		$bean->INVT = $live->INVT;

		$bean->BOOT = $live->BOOT;
		$bean->KWHT = $live->KWHT;

		//Store the bean
		$id = R::store($bean,$bean->id);
		return $id;
	}

	/**
	 * read the live info from an file
	 * @param int $invtnum
	 * @return Live
	 */
	public function readLiveInfo($invtnum) {
		$bean =  R::findOne('live',' INV = :INV ', array(':INV'=>$invtnum));

		$live = new Live();
		if ($bean) {
			$live->INV = $bean->INV;
			$live->I1V = $bean->I1V;
			$live->I1A = $bean->I1A;
			$live->I1P = $bean->I1P;
			$live->I1Ratio = $bean->I1Ratio;

			$live->I2V = $bean->I2V;
			$live->I2A = $bean->I2A;
			$live->I2P = $bean->I2P;
			$live->I2Ratio = $bean->I2Ratio;

			$live->GA = $bean->GA;
			$live->GP = $bean->GP;
			$live->GV = $bean->GV;

			$live->FRQ = $bean->FRQ;
			$live->EFF = $bean->EFF;
			$live->INVT = $bean->INVT;

			$live->time = $bean->time;
			$live->BOOT = $bean->BOOT;
			$live->KWHT = $bean->KWHT;
		}

		return $live;
	}

	/**
	 * will remove the live file
	 */
	public function dropLiveInfo($invtnum) {
		$bean =  R::findOne('live',
				' INV = :INV  ',
				array(':INV'=>$invtnum
				)
		);
		R::trash( $bean );
	}





	/**
	 * write the max power today to the file
	 * @param int $invtnum
	 * @param MaxPowerToday $mpt
	 */
	public function writeMaxPowerToday($invtnum, MaxPowerToday $mpt) {
		$bean =  R::findOne('pMaxOTD',
				' INV = :INV AND SDTE LIKE :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.date('Ymd').'%'
				)
		);

		if (!$bean){
			$bean = R::dispense('pMaxOTD');
		}
		$bean->INV = $invtnum;
		$bean->SDTE = $mpt->SDTE;
		$bean->time = $mpt->time;
		$bean->GP = $mpt->GP;

		//Store the bean
		$id = R::store($bean,$bean->id);
		return $id;
	}

	/**
	 * read the MaxPowerToday from an file
	 * @param int $invtnum
	 * @return MaxPowerToday
	 */
	public function readMaxPowerToday($invtnum, $date=null) {
		(isset($date)) ? $date = $date : $date = date('d-m-Y');
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);

		$bean =  R::findOne( 'pMaxOTD',
				' INV = :INV AND time > :beginDate AND  time < :endDate order by time',
				array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);

		return $bean;
	}

	/**
	 * will remove the max power today file
	 * @param int $invtnum
	 */
	public function dropMaxPowerToday($invtnum) {
		$bean =  R::findOne('pMaxOTD',
				' INV = :INV AND SDTE LIKE :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.date('Ymd').'%'
				)
		);
		R::trash( $bean );
	}

	/**
	 * add the live info to the history
	 * @param int $invtnum
	 * @param Live $live
	 * @param string date
	 */
	public function addHistory($invtnum, Live $live,$timestamp) {
		$bean = R::dispense('history');

		$bean->SDTE = $live->SDTE;
		$bean->time = $timestamp;
		$bean->INV = $invtnum;
		$bean->I1V = $live->I1V;
		$bean->I1A = $live->I1A;
		$bean->I1P = $live->I1P;

		$bean->I2V = $live->I2V;
		$bean->I2A = $live->I2A;
		$bean->I2P = $live->I2P;

		$bean->GV = $live->GV;
		$bean->GA = $live->GA;
		$bean->GP = $live->GP;

		$bean->FRQ = $live->FRQ;
		$bean->EFF = $live->EFF;
		$bean->INVT = $live->INVT;

		$bean->BOOT = $live->BOOT;
		$bean->KWHT = $live->KWHT;

		$bean->pvoutput = false;

		$IP = $live->I1P+$live->I2P;

		// Prevent division by zero error
		if (!empty($IP)) {
			$bean->I1Ratio = ($live->I1P/$IP)*100;
			$bean->I2Ratio = ($live->I2P/$IP)*100;
		}

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

	public function readHistory($invtnum, $date) {
		(!$date)? $date = date('d-m-Y') : $date = $date;
		$beginEndDate = Util::getBeginEndDate('day', 1,$date);

		$bean =  R::findAndExport( 'history',
				' INV = :INV AND time > :beginDate AND  time < :endDate order by time',
				array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate'])
		);
		return $bean;
	}

	/**
	 * Return the amount off history records
	 * @param int $invtnum
	 * @param string $date
	 * @return int $count
	 */
	public function getHistoryCount($invtnum, $date) {
		$bean =  R::find('history',
				' INV = :INV AND SDTE LIKE :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.date('Ymd').'%'
				)
		);
		return count($bean);
	}


















	/**
	 * write the max power today to the file
	 * @param int $invtnum
	 * @param MaxPowerToday $mpt
	 */
	public function addEnergy($invtnum, Energy $energy) {
		$bean =  R::findOne('energy',
				' INV = :INV AND SDTE LIKE :date ',array(':INV'=>$invtnum,':date'=> '%'.date('Ymd').'%')
		);
		$oldKWH = 0;
		if (!$bean){
			$bean = R::dispense('energy');
		} else {
			$oldKWH = $bean->KWH;
		}
		$bean->INV = $invtnum;
		$bean->SDTE = $energy->SDTE;
		$bean->time = $energy->time;
		$bean->KWH = $energy->KWH;
		$bean->KWHT = $energy->KWHT;
		$bean->co2 = $energy->co2;

		//Only store the bean when the value
		$id = -1;
		if ($energy->KWH > $oldKWH) {
			$id = R::store($bean,$bean->id);
		}
		return $id;
	}

	/**
	 * write the max power today to the file
	 * @param int $invtnum
	 * @param MaxPowerToday $mpt
	 */
	public function addNewEnergy($invtnum, Energy $energy) {
		$bean = R::dispense('energy');
		$bean->INV = $invtnum;
		$bean->SDTE = $energy->SDTE;
		$bean->time = $energy->time;
		$bean->KWH = $energy->KWH;
		$bean->KWHT = $energy->KWHT;
		$bean->co2 = $energy->co2;
		$id = R::store($bean,$bean->id);
		return $id;
	}



	/**
	 * add the events to the events
	 * @param int $invtnum
	 * @param Event $event
	 */
	public function addEvent($invtnum, Event $event) {
		$bean = R::dispense('event');

		$bean->INV = $invtnum;
		$bean->SDTE = $event->SDTE;
		$bean->time = $event->time;
		$bean->Event = $event->event;
		$bean->Type = $event->type;
		$bean->alarmSend = $event->alarmSend;
		$id = R::store($bean);

	}


	/**
	 * Read the events file
	 * @param int $invtnum
	 * @return bean object
	 */
	public function readEvent($invtnum,$limit=10) {
		$bean = R::getAll('select * from event ORDER BY id DESC LIMIT :limit ',
				array(':limit'=>$limit)
		);

		return $bean;
	}



	/**
	 * Reads the events of a certain Type
	 * @param int $invtnum
	 * @return bean object
	 */
	public function readTypeEvents($invtnum,$type,$limit=20) {
		($type=='Info')?$limit=1:$limit=$limit;

		$bean = R::getAll("
				select *,
				strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date,
				strftime ( '%H:%M' , time ( time , 'unixepoch' ) ) AS HumanTime
				FROM event
				WHERE Type = :type
				ORDER BY id DESC
				LIMIT :limit ",
				array(':limit'=>$limit,':type'=>$type)
		);
		return $bean;
	}




	/**
	 * will remove Event
	 * @param int $invtnum
	 */
	public function dropEvent($invtnum) {
		$bean =  R::findOne('event',
				' INV = :INV ',
				array(':INV'=>$invtnum
				)
		);
		R::trash( $bean );
	}

	public function writeDailyData($invtnum,Day $day) {
		// TODO :: ??
	}

	/**
	 * Read Daily Data
	 * @param string $date
	 * @param int $invtnum
	 * @return bean object
	 */
	public function readDailyData($date, $invtnum) {
		$bean =  R::findAndExport(
				'history',
				' INV = :INV AND SDTE like :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.$date.'%'
				)
		);

		$points = $this->DayBeansToDataArray($bean);
		$lastDays = new LastDays();
		$lastDays->points=$points[0];
		$lastDays->KWHT=$points[1];
		return $lastDays;
	}
	/**
	 *
	 * @param unknown_type $invtnum
	 */
	public function dropDailyData($invtnum) {
		// TODO :: ??
	}
	/**
	 *
	 * @param unknown_type $invtnum
	 * @param Day $day
	 */
	public function writeLastDaysData($invtnum,Day $day) {
		// TODO :: ??
	}

	/**
	 * read Last Days Data
	 * @param int $invtnum
	 * @param int $limit
	 * @return bean object
	 */
	public function readLastDaysData($invtnum, $limit = 60) {
		$bean =  R::findAndExport(
				'energy',
				' INV = :INV  ',
				array(':INV'=>$invtnum
				)
		);
		$points = $this->DayBeansToDataArray($bean);
		$lastDays = new LastDays();
		$lastDays->points=$points[0];
		return $lastDays;

	}


	public function dropLastDaysData($invtnum) {
		// TODO :: ??
	}

	/**
	 *
	 * @param unknown_type $beans
	 */
	public function DayBeansToGraphPoints($beans,$graph,$startDate){
		$config = Session::getConfig();
		
		$i=0;
		$firstBean = array();
		$preBean = array();

		$KWHT = 0;
		$lastDays = new LastDays();
		
			foreach ($beans as $bean){
				if ($i==0){
					$firstBean = $bean;
					$preBean = $bean;
					$preBeanUTCdate = $bean['time'];
				}
				$UTCdate = $bean['time'];
				$UTCtimeDiff = $UTCdate - $preBeanUTCdate;
				$cumPower = round(($bean['KWHT']-$firstBean['KWHT'])*1000,0);
				// 09/30/2010 00:00:00
				$avgPower = Formulas::calcAveragePower($bean['KWHT'], $preBean['KWHT'], $UTCtimeDiff,0,0);
				$graph->points['cumPower'][] = array (  $UTCdate*1000 ,$cumPower,date("H:i, d-m-Y",$bean['time']));
				$graph->points['avgPower'][] = array (  $UTCdate*1000 ,$avgPower,date("H:i, d-m-Y",$bean['time']));
				$preBeanUTCdate = $bean['time'];
				$preBean = $bean;
				$i++;
			}

			if($i>0){
				$plantPower = $this->readPlantPower();
				$kWhkWp = number_format(($cumPower/1000) / ($plantPower/1000),2,',','');
				
				if($cumPower >= 1000){
					$cumPower = number_format($cumPower /=1000,2,',','');
					$cumPowerUnit = "kWh";
				}else{
					$cumPowerUnit = "W";
				}
				//set timestamp to overrule standard timestamp
				$timestamp = Util::getSunInfo($config,$startDate);
				$graph->timestamp = array("beginDate"=>$timestamp['sunrise']-3600,"endDate"=>$timestamp['sunset']+3600);
				
				$graph->metaData['KWH']=array('cumPower'=>$cumPower,'KWHTUnit'=>$cumPowerUnit,'KWHKWP'=>$kWhkWp);

				$graph->series[] = array('label'=>'Cum. Power(W)','yaxis'=>'yaxis');
				$graph->series[] = array('label'=>'Avg. Power(W)','yaxis'=>'yaxis');
				
				$graph->axes['yaxis']  = array('label'=>'Cum. Power(W)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
				$graph->axes['y2axis'] = array('label'=>'Avg. Power(W)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer');
				
				//$graph->metaData['hideSeries']= array();
				$graph->metaData['hideSeries']= array();
			
			}

			return $graph;
	}



	/**
	 *
	 */
	function readPlantPower(){

		$inverters = $this->readInverters();
		$plantPower = 0;

		foreach ($inverters as $inverter) {
			$inverter->panels = $this->readPanelsByInverter($inverter->id);

			foreach ($inverter->panels as $panel) {
				//$panel = new Panel();
				$plantPower += ($panel->amount * $panel->wp);
			}

		}
		return $plantPower;
	}

	/**
	 *
	 * @param Config $config
	 */
	public function writeConfig(Config $config) {
		// Only save the object self not the arrays
		$bean = R::findOne('config');

		if (!$bean){
			$bean = R::dispense('config');
		}

		$bean->version_title = $config->version_title;
		$bean->version_revision = $config->version_revision;

		$bean->title = $config->title;
		$bean->subtitle = $config->subtitle;
		$bean->url = $config->url;
		$bean->location = $config->location;
		$bean->latitude = $config->latitude;
		$bean->longitude = $config->longitude;
		$bean->timezone = $config->timezone;
		$bean->debugmode = $config->debugmode;

		$bean->comPort = $config->comPort;
		$bean->comOptions = $config->comOptions;
		$bean->comDebug = $config->comDebug;

		$bean->emailFromName = $config->emailFromName;
		$bean->emailFrom = $config->emailFrom;
		$bean->emailTo = $config->emailTo;
		$bean->emailAlarms = $config->emailAlarms;
		$bean->emailEvents = $config->emailEvents;
		$bean->emailReports = $config->emailReports;

		$bean->smtpServer = $config->smtpServer;
		$bean->smtpPort = $config->smtpPort;
		$bean->smtpSecurity = $config->smtpSecurity;
		$bean->smtpUser = $config->smtpUser;
		$bean->smtpPassword = $config->smtpPassword;

		$bean->template = $config->template;
		$bean->aurorapath = $config->aurorapath;
		$bean->smagetpath = $config->smagetpath;
		$bean->smartmeterpath = $config->smartmeterpath;

		$bean->co2kwh = $config->co2kwh;

		$bean->googleAnalytics = $config->googleAnalytics;
		$bean->piwikServerUrl = $config->piwikServerUrl;
		$bean->piwikSiteId = $config->piwikSiteId;

		$bean->adminpasswd = $config->adminpasswd;
		
		$bean->pauseWorker = $config->pauseWorker;
		$bean->restartWorker = $config->restartWorker;

		//Store the bean
		R::store($bean);
	}
	/**
	 *
	 */
	public function readConfig() {
		$bean = R::findOne('config');

		$config = new Config();

		if ($bean) {
			$config->version_title = $bean->version_title;
			$config->version_revision = $bean->version_revision;

			$config->title = $bean->title;
			$config->subtitle = $bean->subtitle;
			$config->url = ($bean->url != "") ? $bean->url : $config->url;;
			$config->location = $bean->location;
			$config->latitude = $bean->latitude;
			$config->longitude = $bean->longitude;
			$config->timezone = $bean->timezone;
			$config->debugmode = ($bean->debugmode != "") ? $bean->debugmode : $config->debugmode;;

			$config->comPort = $bean->comPort;
			$config->comOptions = $bean->comOptions;
			$config->comDebug = $bean->comDebug;

			$config->emailFromName = $bean->emailFromName;
			$config->emailFrom = $bean->emailFrom;
			$config->emailTo = $bean->emailTo;
			$config->emailAlarms = $bean->emailAlarms;
			$config->emailEvents = $bean->emailEvents;
			$config->emailReports = $bean->emailReports;

			$config->smtpServer = $bean->smtpServer;
			$config->smtpPort = $bean->smtpPort;
			$config->smtpSecurity = $bean->smtpSecurity;
			$config->smtpUser = $bean->smtpUser;
			$config->smtpPassword = $bean->smtpPassword;

			$config->template = ($bean->template != "") ? $bean->template : $config->template;
			$config->aurorapath = ($bean->aurorapath != "") ? $bean->aurorapath : $config->aurorapath;
			$config->smagetpath = ($bean->smagetpath != "") ? $bean->smagetpath : $config->smagetpath;
			$config->smartmeterpath = ($bean->smartmeterpath != "") ? $bean->smartmeterpath : $config->smartmeterpath;
				
			$config->co2kwh = ($bean->co2kwh > 0) ? $bean->co2kwh : $config->co2kwh;
			$config->inverters = $this->readInverters();
				
			$config->googleAnalytics = $bean->googleAnalytics;
			$config->piwikServerUrl = $bean->piwikServerUrl;
			$config->piwikSiteId = $bean->piwikSiteId;
				
			$config->adminpasswd = ($bean->adminpasswd != "") ? $bean->adminpasswd : $config->adminpasswd;
			
			$config->pauseWorker = ($bean->pauseWorker != "") ? $bean->pauseWorker : $config->pauseWorker;
			$config->restartWorker = ($bean->restartWorker != "") ? $bean->restartWorker : $config->restartWorker;
		}

		return $config;
	}
	/**
	 *
	 * @param Inverter $inverter
	 */
	public function writeInverter(Inverter $inverter) {
		// Only save the object self not the arrays
		$bean = R::load('inverter',$inverter->id);

		if (!$bean){
			$bean = R::dispense('inverter');
		}

		$bean->deviceApi = $inverter->deviceApi;
		$bean->type = $inverter->type;
		$bean->name = $inverter->name;
		$bean->description = $inverter->description;

		$bean->liveOnFrontend = $inverter->liveOnFrontend;
		$bean->graphOnFrontend = $inverter->graphOnFrontend;

		$bean->initialkwh = $inverter->initialkwh;
		$bean->expectedkwh = $inverter->expectedkwh;
		$bean->heading = $inverter->heading;
		$bean->correctionFactor = $inverter->correctionFactor;
		$bean->comAddress = $inverter->comAddress;
		$bean->comLog = $inverter->comLog;
		$bean->syncTime = $inverter->syncTime;
		$bean->pvoutputEnabled = $inverter->pvoutputEnabled;
		$bean->pvoutputApikey = $inverter->pvoutputApikey;
		$bean->pvoutputSystemId = $inverter->pvoutputSystemId;
		$bean->state = $inverter->state;

		$bean->expectedJAN = $inverter->expectedJAN;
		$bean->expectedFEB = $inverter->expectedFEB;
		$bean->expectedMAR = $inverter->expectedMAR;
		$bean->expectedAPR = $inverter->expectedAPR;
		$bean->expectedMAY = $inverter->expectedMAY;
		$bean->expectedJUN = $inverter->expectedJUN;
		$bean->expectedJUL = $inverter->expectedJUL;
		$bean->expectedAUG = $inverter->expectedAUG;
		$bean->expectedSEP = $inverter->expectedSEP;
		$bean->expectedOCT = $inverter->expectedOCT;
		$bean->expectedNOV = $inverter->expectedNOV;
		$bean->expectedDEC = $inverter->expectedDEC;

		//Store the bean
		R::store($bean);
	}
	/**
	 *
	 * @param unknown_type $id
	 */
	public function readInverter($id) {
		$bean = R::load('inverter',$id);

		$inverter = new Inverter();
		$inverter->id = $bean->id;
		$inverter->deviceApi = $bean->deviceApi;
		$inverter->type = $bean->type;
		$inverter->name = $bean->name;
		$inverter->description = $bean->description;
		$inverter->liveOnFrontend = $bean->liveOnFrontend;
		$inverter->graphOnFrontend = $bean->graphOnFrontend;
		$inverter->initialkwh = $bean->initialkwh;
		$inverter->expectedkwh = $bean->expectedkwh;
		$inverter->heading = $bean->heading;
		$inverter->correctionFactor = $bean->correctionFactor;
		$inverter->comAddress = $bean->comAddress;
		$inverter->comLog = $bean->comLog;
		$inverter->syncTime = $bean->syncTime;
		$inverter->pvoutputEnabled = ($bean->pvoutputEnabled != "") ? $bean->pvoutputEnabled : $inverter->pvoutputEnabled;
		$inverter->pvoutputApikey = $bean->pvoutputApikey;
		$inverter->pvoutputSystemId = $bean->pvoutputSystemId;
		$inverter->panels = $this->readPanelsByInverter($inverter->id);
		$inverter->state = $bean->state;

		$inverter->expectedJAN = $bean->expectedJAN;
		$inverter->expectedFEB = $bean->expectedFEB;
		$inverter->expectedMAR = $bean->expectedMAR;
		$inverter->expectedAPR = $bean->expectedAPR;
		$inverter->expectedMAY = $bean->expectedMAY;
		$inverter->expectedJUN = $bean->expectedJUN;
		$inverter->expectedJUL = $bean->expectedJUL;
		$inverter->expectedAUG = $bean->expectedAUG;
		$inverter->expectedSEP = $bean->expectedSEP;
		$inverter->expectedOCT = $bean->expectedOCT;
		$inverter->expectedNOV = $bean->expectedNOV;
		$inverter->expectedDEC = $bean->expectedDEC;

		$inverter->plantpower = 0;
		foreach ($inverter->panels as $panel) {
			//$panel = new Panel();
			$inverter->plantpower += ($panel->amount * $panel->wp);
		}

		return $inverter;
	}
	/**
	 *
	 */
	private function readInverters() {
		$list = array();
		foreach(R::find('inverter') as $bean) {
			$inverter = new Inverter();
			$inverter->id = $bean->id;
			$inverter->deviceApi = $bean->deviceApi;
			$inverter->type = $bean->type;
			$inverter->name = $bean->name;
			$inverter->description = $bean->description;
			$inverter->liveOnFrontend = $bean->liveOnFrontend;
			$inverter->graphOnFrontend = $bean->graphOnFrontend;
			$inverter->initialkwh = $bean->initialkwh;
			$inverter->expectedkwh = $bean->expectedkwh;
			$inverter->heading = $bean->heading;
			$inverter->correctionFactor = $bean->correctionFactor;
			$inverter->comAddress = $bean->comAddress;
			$inverter->comLog = $bean->comLog;
			$inverter->syncTime = $bean->syncTime;
			$inverter->pvoutputEnabled = ($bean->pvoutputEnabled != "") ? $bean->pvoutputEnabled : $inverter->pvoutputEnabled;
			$inverter->pvoutputApikey = $bean->pvoutputApikey;
			$inverter->pvoutputSystemId = $bean->pvoutputSystemId;
			$inverter->panels = $this->readPanelsByInverter($inverter->id);

			$inverter->expectedJAN = $bean->expectedJAN;
			$inverter->expectedFEB = $bean->expectedFEB;
			$inverter->expectedMAR = $bean->expectedMAR;
			$inverter->expectedAPR = $bean->expectedAPR;
			$inverter->expectedMAY = $bean->expectedMAY;
			$inverter->expectedJUN = $bean->expectedJUN;
			$inverter->expectedJUL = $bean->expectedJUL;
			$inverter->expectedAUG = $bean->expectedAUG;
			$inverter->expectedSEP = $bean->expectedSEP;
			$inverter->expectedOCT = $bean->expectedOCT;
			$inverter->expectedNOV = $bean->expectedNOV;
			$inverter->expectedDEC = $bean->expectedDEC;

			$inverter->plantpower = 0;
			foreach ($inverter->panels as $panel) {
				//$panel = new Panel();
				$inverter->plantpower += ($panel->amount * $panel->wp);
			}

			$list[] = $inverter;
		}

		return $list;
	}

	/**
	 *
	 * @param Panel $panel
	 */
	public function writePanel(Panel $panel) {
		// Only save the object self not the arrays
		$bean = R::load('panel', $panel->id);

		if (!$bean){
			$bean = R::dispense('panel');
		}

		$bean->inverterId = $panel->inverterId;
		$bean->description = $panel->description;
		$bean->roofOrientation = $panel->roofOrientation;
		$bean->roofPitch = $panel->roofPitch;
		$bean->amount = $panel->amount;
		$bean->wp = $panel->wp;

		//Store the bean
		R::store($bean);
	}

	/**
	 *
	 * @param unknown_type $id
	 */

	public function readPanel($id) {
		$bean = R::load('panel', $id);

		$panel = new Panel();
		$panel->id = $bean->id;
		$panel->inverterId = $bean->inverterId;
		$panel->description = $bean->description;
		$panel->roofOrientation = $bean->roofOrientation;
		$panel->roofPitch = $bean->roofPitch;
		$panel->amount = $bean->amount;
		$panel->wp = $bean->wp;
		return $panel;
	}
	/**
	 *
	 * @param unknown_type $inverterId
	 */
	private function readPanelsByInverter($inverterId) {
		$list = array();
		$beans = R::find('panel',' inverterId = :id ', array( ":id"=>$inverterId ));
		foreach ($beans as $bean){
			$panel = new Panel();
			$panel->id = $bean->id;
			$panel->inverterId = $bean->inverterId;
			$panel->description = $bean->description;
			$panel->roofOrientation = $bean->roofOrientation;
			$panel->roofPitch = $bean->roofPitch;
			$panel->amount = $bean->amount;
			$panel->wp = $bean->wp;
			$list[] = $panel;
		}

		return $list;
	}

	/**
	 * Create and run the query to getAll Values for a given Period
	 * @Param string $table
	 * @Param string $type
	 * @Param date $startDate
	 */
	public function readTablesPeriodValues($invtnum, $table, $type, $startDate){
		$count = 0;
		
		// get the begin and end date/time
		$beginEndDate = Util::getBeginEndDate($type, $count,$startDate);
		if ($invtnum > 0){
			$energyBeans = R::getAll("
					SELECT strftime ( '%H:%M:%S %d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date,*
					FROM '".$table."'
					WHERE INV = :INV AND time > :beginDate AND  time < :endDate
					ORDER BY time",array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}else{
			$energyBeans = R::getAll("
					SELECT strftime ( '%H:%M:%S %d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date,*
					FROM '".$table."'
					WHERE time > :beginDate AND  time < :endDate
					ORDER BY time",array(':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}
		
		return $energyBeans;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMonthEnergyPerDay($invtnum=0,$startDate){
		$beginEndDate = Util::getBeginEndDate('month', 1,$startDate);

		if ($invtnum>0){
			$beans = R::getAll("
					SELECT INV, strftime ( '%d-%m-%Y %H:%M' , datetime ( time , 'unixepoch' ) ) AS date, KWH
					FROM energy WHERE INV = :INV AND time > :beginDate AND time < :endDate order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}else{
			$beans = R::getAll("
					SELECT INV, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date, KWH
					FROM energy  WHERE time > :beginDate AND time < :endDate  order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}
		for ($i = 0; $i < count($beans); $i++) {
			$beans[$i]['KWH'] = number_format($beans[$i]['KWH'],2,',','');
		}
		return $beans;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMonthMaxPowerPerDay($invtnum=0,$startDate){
		$beginEndDate = Util::getBeginEndDate('month', 1,$startDate);

		if ($invtnum>0){
			$beans = R::getAll("
					SELECT INV, strftime ( '%d-%m-%Y %H:%M' , datetime ( time , 'unixepoch' ) ) AS date, GP as maxGP
					FROM pMaxOTD WHERE INV = :INV AND time > :beginDate AND time < :endDate order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}else{
			$beans = R::getAll("
					SELECT INV, strftime ( '%d-%m-%Y %H:%M' , datetime ( time , 'unixepoch' ) ) AS date, GP as maxGP
					FROM pMaxOTD  WHERE time > :beginDate AND time < :endDate  order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}
		for ($i = 0; $i < count($beans); $i++) {
			$beans[$i]['maxGP'] = number_format($beans[$i]['maxGP'],2,',','');
		}
		return $beans;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getYearMaxEnergyPerMonth($invtnum=0,$date){
		$beginEndDate = Util::getBeginEndDate('year', 1,$date);
		if ($invtnum>0){
			$beans = R::getAll("
					SELECT INV,MAX ( kwh ) AS KWH, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE INV = :INV AND time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) )
					ORDER BY time DESC",array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}else{
			$beans = R::getAll("
					SELECT INV,MAX ( kwh ) AS KWH, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy WHERE time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) )
					ORDER BY time DESC",
					array(':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}
		return $beans;
	}


	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getYearEnergyPerMonth($invtnum=0,$date){
		$beginEndDate = Util::getBeginEndDate('year', 1,$date);
		if ($invtnum>0){
			$beans = R::getAll("
					SELECT INV,sum(kWh) AS KWH, strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE INV = :INV AND time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) )
					ORDER BY time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}else{
			$beans = R::getAll("
					SELECT INV,sum(kWh) AS KWH, strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy WHERE time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) )
					ORDER BY time DESC",
					array(':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}
		return $beans;
	}










	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMaxMinEnergyYear($invtnum=0,$date){
		$beginEndDate = Util::getBeginEndDate('year', 1,$date);
		if ($invtnum>0){
			$beansMax = R::getRow("
					SELECT INV,max(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE INV = :INV AND time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) )
					order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			$beansMin = R::getRow("
					SELECT INV,min(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE INV = :INV AND time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) )
					order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			$beansMax['kWh'] = number_format($beansMax['kWh'],2,',','');
			$beansMin['kWh'] = number_format($beansMin['kWh'],2,',','');

			$return = array(
					"maxEnergy"=>$beansMax,
					"minEnergy"=>$beansMin
			);
		}else{
			$beansMax = R::getRow("
					SELECT INV,max(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE  time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) )
					order by time DESC",
					array('beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			$beansMin = R::getRow("
					SELECT INV,min(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) )
					order by time DESC",
					array('beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			for ($i = 0; $i < count($beansMax); $i++) {
				$beansMax[$i]['kWh'] = number_format($beansMax[$i]['kWh'],2,',','');
			}
			for ($i = 0; $i < count($beansMin); $i++) {
				$beansMin[$i]['kWh'] = number_format($beansMin[$i]['kWh'],2,',','');
			}
			$return = array("maxEnergy"=>$beansMax,"minEnergy"=>$beansMin);
		}
		return $return;
	}



















	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMaxMinEnergyMonth($invtnum=0,$date){
		$beginEndDate = Util::getBeginEndDate('month', 1,$date);
		if ($invtnum>0){
			$beansMax = R::getRow("
					SELECT INV,max(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE INV = :INV AND time > :beginDate AND time < :endDate
					order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			$beansMin = R::getRow("
					SELECT INV,min(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE INV = :INV AND time > :beginDate AND time < :endDate
					order by time DESC",
					array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			$beansMax['kWh'] = number_format($beansMax['kWh'],2,',','');
			$beansMin['kWh'] = number_format($beansMin['kWh'],2,',','');

			$return = array(
					"maxEnergy"=>$beansMax,
					"minEnergy"=>$beansMin
			);
		}else{
			$beansMax = R::getRow("
					SELECT INV,max(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE  time > :beginDate AND time < :endDate
					order by time DESC",
					array('beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			$beansMin = R::getRow("
					SELECT INV,min(kWh) as kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy
					WHERE time > :beginDate AND time < :endDate
					order by time DESC",
					array('beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			for ($i = 0; $i < count($beansMax); $i++) {
				$beansMax[$i]['kWh'] = number_format($beansMax[$i]['kWh'],2,',','');
			}
			for ($i = 0; $i < count($beansMin); $i++) {
				$beansMin[$i]['kWh'] = number_format($beansMin[$i]['kWh'],2,',','');
			}
			$return = array("maxEnergy"=>$beansMax,"minEnergy"=>$beansMin);
		}
		return $return;
	}


	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getYearMaxPowerPerMonth($invtnum=0,$date){
		$beginEndDate = Util::getBeginEndDate('year', 1,$date);
		if ($invtnum>0){
			$beans = R::getAll("
					SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM pMaxOTD
					WHERE INV = :INV AND  time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) )
					order by time DESC",
					array(':INV'=>$invtnum,'beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}else{
			$beans = R::getAll("
					SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM pMaxOTD
					WHERE  time > :beginDate AND time < :endDate
					GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) )
					order by time DESC",
					array('beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
		}
		for ($i = 0; $i < count($beans); $i++) {
			$beans[$i]['maxGP'] = number_format($beans[$i]['maxGP'],2,',','');
		}
		return $beans;
	}


	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getDayEnergyPerDay($invtnum=0){
		$beginEndDate = Util::getBeginEndDate('today', 1);

		if ($invtnum>0){

			$beans = R::getAll("SELECT INV,MAX ( kwh ) AS kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy WHERE INV = :INV  AND time > :beginDate AND time < :endDate GROUP BY strftime ( 'd%-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}else{
			$beans = R::getAll("SELECT INV,MAX ( kwh ) AS kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM energy WHERE time > :beginDate AND time < :endDate GROUP BY strftime ( 'd%-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}
		for ($i = 0; $i < count($beans); $i++) {
			$beans[$i]['kWh'] = number_format($beans[$i]['kWh'],2,',','');
		}
		return $beans;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getDayMaxPowerPerDay($invtnum=0){
		$beginEndDate = Util::getBeginEndDate('today', 1);

		if ($invtnum>0){
			$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM pMaxOTD WHERE INV = :INV AND time > :beginDate AND time < :endDate GROUP BY strftime ( 'd%-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}else{
			$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date
					FROM pMaxOTD WHERE time > :beginDate AND time < :endDate GROUP BY strftime ( 'd%-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));

		}
		for ($i = 0; $i < count($beans); $i++) {
			$beans[$i]['maxGP'] = number_format($beans[$i]['maxGP'],2,',','');
		}
		return $beans;
	}

	/**
	 *
	 * @param unknown_type $invtnum
	 * @param unknown_type $startDate
	 */

	public function getDetailsHistory($invtnum,$startDate){

		$beginEndDate = Util::getBeginEndDate('day', 1,$startDate);

		if ($invtnum>0){
			$beans = R::getAll("SELECT * FROM History WHERE INV = :INV AND time > :beginDate AND time < :endDate order by time ASC",
					array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}else{
			$beans = R::getAll("SELECT * FROM History WHERE time > :beginDate AND time < :endDate order by time ASC",
					array(':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}
		$historyColumns = R::getColumns('history');
		$labels[]= _('Grid')." "._('Power');
		$labels[]= _('Grid')." "._('Voltage');
		$labels[]= _('Grid')." "._('Amps');
		$labels[]= _('Grid')." "._('Frequency');

		$labels[]= "MPP1 "._('Power');
		$labels[]= "MPP1 "._('Voltage');
		$labels[]= "MPP1 "._('Amps');
		$labels[]= "MPP1 "._('Ratio');

		$labels[]= "MPP2 "._('Power');
		$labels[]= "MPP2 "._('Voltage');;
		$labels[]= "MPP2 "._('Amps');
		$labels[]= "MPP2 "._('Ratio');

		$labels[]= "DC>AC "._('Efficiency');
		$labels[]= "Boos. "._('Temperature');
		$labels[]= "Inv. "._('Temperature');

		$switches['P'][] = 0;
		$switches['P'][] = 4;
		$switches['P'][] = 8;
		$switches['V'][] = 1;
		$switches['V'][] = 5;
		$switches['V'][] = 9;
		$switches['A'][] = 2;
		$switches['A'][] = 6;
		$switches['A'][] = 10;
		$switches['R'][] = 7;
		$switches['R'][] = 11;
		$switches['G'][] = 0;
		$switches['G'][] = 1;
		$switches['G'][] = 2;
		$switches['G'][] = 3;
		$switches['I1'][] = 4;
		$switches['I1'][] = 5;
		$switches['I1'][] = 6;
		$switches['I1'][] = 7;
		$switches['I2'][] = 8;
		$switches['I2'][] = 9;
		$switches['I2'][] = 10;
		$switches['I2'][] = 11;
		$switches['EFF'][] = 12;
		$switches['TEMP'][] = 13;
		$switches['TEMP'][] = 14;

		foreach($beans as $bean){
			$bean['time'] =$bean['time']*1000;
			$live->GP[] 	= array($bean['time'],(float)$bean['GP']);
			($bean['GP'] > $max['P'])? $max['P'] = (float)$bean['GP'] : $max['P'] = $max['P'];

			$live->GV[] 	= array($bean['time'],(float)$bean['GV']);
			($bean['GV'] > $max['V'])? $max['V'] = (float)$bean['GV'] : $max['V'] = $max['V'];

			$live->GA[] 	= array($bean['time'],(float)$bean['GA']);
			($bean['GA'] > $max['A'])? $max['A'] = (float)$bean['GA'] : $max['A'] = $max['A'];

			$live->FRQ[] 	= array($bean['time'],(float)$bean['FRQ']);
			($bean['FRQ'] > $max['FRQ'])? $max['FRQ'] = (float)$bean['FRQ'] : $max['FRQ'] = $max['FRQ'];

			$live->I1P[] 	= array($bean['time'],(float)$bean['I1P']);
			($bean['I1P'] > $max['P'])? $max['P'] = (float)$bean['I1P'] : $max['P'] = $max['P'];

			$live->I1V[] 	= array($bean['time'],(float)$bean['I1V']);
			($bean['I1V'] > $max['V'])? $max['V'] = (float)$bean['I1V'] : $max['V'] = $max['V'];

			$live->I1A[] 	= array($bean['time'],(float)$bean['I1A']);
			($bean['I1A'] > $max['A'])? $max['A'] = (float)$bean['I1A'] : $max['A'] = $max['A'];

			$live->I1Ratio[]= array($bean['time'],(float)$bean['I1Ratio']);
			($bean['I1Ratio'] > $max['Ratio'])? $max['Ratio'] = (float)$bean['I1Ratio'] : $max['Ratio'] = $max['Ratio'];

			$live->I2P[] 	= array($bean['time'],(float)$bean['I2P']);
			($bean['I2P'] > $max['P'])? $max['P'] = (float)$bean['I2P'] : $max['P'] = $max['P'];

			$live->I2V[] 	= array($bean['time'],(float)$bean['I2V']);
			($bean['I2V'] > $max['V'])? $max['V'] = (float)$bean['I2V'] : $max['V'] = $max['V'];

			$live->I2A[] 	= array($bean['time'],(float)$bean['I2A']);
			($bean['I2A'] > $max['A'])? $max['A'] = (float)$bean['I2A'] : $max['A'] = $max['A'];

			$live->I2Ratio[]= array($bean['time'],(float)$bean['I2Ratio']);
			($bean['I2Ratio'] > $max['Ratio'])? $max['Ratio'] = (float)$bean['I2Ratio'] : $max['Ratio'] = $max['Ratio'];

			$live->EFF[] 	= array($bean['time'],(float)$bean['EFF']);
			($bean['EFF'] > $max['EFF'])? $max['EFF'] = (float)$bean['EFF'] : $max['EFF'] = $max['EFF'];

			$live->BOOT[]	= array($bean['time'],(float)$bean['BOOT']);
			($bean['BOOT'] > $max['T'])? $max['T'] = (float)$bean['BOOT'] : $max['T'] = $max['T'];

			$live->INVT[] 	= array($bean['time'],(float)$bean['INVT']);
			($bean['INVT'] > $max['T'])? $max['T'] = (float)$bean['INVT'] : $max['T'] = $max['T'];
		}


		if(!$beans){
			$live->GP[] 	= array(time()*1000,0);//0
			$live->GV[] 	= array(time()*1000,0);//1
			$live->GA[] 	= array(time()*1000,0);//2
			$live->FRQ[] 	= array(time()*1000,0);//3
			$live->I1P[] 	= array(time()*1000,0);//4
			$live->I1V[] 	= array(time()*1000,0);//5
			$live->I1A[] 	= array(time()*1000,0);//6
			$live->I1Ratio[]= array(time()*1000,0);//7
			$live->I2P[] 	= array(time()*1000,0);//8
			$live->I2V[] 	= array(time()*1000,0);//9
			$live->I2A[] 	= array(time()*1000,0);//10
			$live->I2Ratio[]= array(time()*1000,0);//11
			$live->EFF[] 	= array(time()*1000,0);//12
			$live->BOOT[]	= array(time()*1000,0);//13
			$live->INVT[] 	= array(time()*1000,0);//14
			$max['P'] = 1;
			$max['V'] = 1;
			$max['A'] = 1;
			$max['EFF'] = 1;
			$max['FRQ'] = 1;
			$max['Ratio'] = 1;
			$max['T'] = 1;
		}
		return array("details"=>$live,"labels"=>$labels,"switches"=>$switches,"max"=>$max);
	}

	/**
	 *
	 * @param int $invtnum
	 */
	public function getYearsMonthCompareFilters(){
		$month = array();
		$year = array();
		$year = R::getAll("SELECT distinct(strftime ( '%Y' , date ( time , 'unixepoch' ) )) AS date FROM Energy GROUP BY strftime ( '%m-%Y' , date ( time ,'unixepoch' ) ) ORDER BY date ASC");
		$month[] = array("number"=>1,"name"=>"Jan");
		$month[] = array("number"=>2,"name"=>"Feb");
		$month[] = array("number"=>3,"name"=>"Mar");
		$month[] = array("number"=>4,"name"=>"Apr");
		$month[] = array("number"=>5,"name"=>"May");
		$month[] = array("number"=>6,"name"=>"Jun");
		$month[] = array("number"=>7,"name"=>"Jul");
		$month[] = array("number"=>8,"name"=>"Aug");
		$month[] = array("number"=>9,"name"=>"Sep");
		$month[] = array("number"=>10,"name"=>"Oct");
		$month[] = array("number"=>11,"name"=>"Nov");
		$month[] = array("number"=>12,"name"=>"Dec");
		return array('month'=> $month, 'year'=>$year );
	}


	/**
	 *
	 * @param unknown_type $invtnum
	 * @param unknown_type $whichMonth
	 * @param unknown_type $whichYear
	 * @param unknown_type $compareMonth
	 * @param unknown_type $compareYear
	 */
	public function getCompareGraph($invtnum,$whichMonth,$whichYear,$compareMonth,$compareYear){
		$config = Session::getConfig();
		$beans = array();
		$whichBeans = array();
		$compareBeans = array();
		//var_dump($invtnum,$whichMonth,$whichYear,$compareMonth,$compareYear);
		if($whichMonth >0 AND $whichYear>0){
			$whichMonthDays    =  cal_days_in_month(CAL_GREGORIAN, $whichMonth, $whichYear);
			$expectedMonthDays =  cal_days_in_month(CAL_GREGORIAN, $compareMonth, date("Y"));
			if ($compareYear > 0){
				// get Which beans
				$beans = $this->readEnergyValues($invtnum, 'month', 1, $whichYear."-".$whichMonth."-1");
				// lose one array
				$whichBeans = $beans[0];
				// get last KWH value from the Which array
				$lastKWH = $whichBeans[count($whichBeans)]['KWH'];
					
				// here we complete the month
				for ($i = count($whichBeans)-1; $i < $whichMonthDays; $i++) {
					$iWhichDay = $i+1;
					$whichBeans[$i]['time'] = strtotime($compareYear."/".$compareMonth."/".$iWhichDay);
					$whichBeans[$i]['KWH'] = sprintf("%01.2f",(float)$lastKWH);
					$whichBeans[$i]['displayKWH'] =  sprintf("%01.2f",(float)$lastKWH);
				}
				
				// get Compare beans
				$beans = $this->readEnergyValues($invtnum, 'month', 1, $compareYear."-".$compareMonth."-1");
				$compareBeans = $beans[0];
				$lastKWH = $compareBeans[count($compareBeans)]['KWH'];
				$compareMonthDays =  cal_days_in_month(CAL_GREGORIAN, $compareMonth, $compareYear);
				for ($i = count($compareBeans)-1; $i < $compareMonthDays; $i++) {
					$iExpectedDay = $i+1;
					$compareBeans[$i]['time'] = strtotime($compareYear."/".$compareMonth."/".$iExpectedDay);
					$compareBeans[$i]['KWH'] = sprintf("%01.2f",(float)$lastKWH);
					$compareBeans[$i]['displayKWH'] =  sprintf("%01.2f",(float)$lastKWH);
				}
				// move compareBeans to expectedBeans, so we pass it to JSON.
				$expectedBeans  = $compareBeans;
				$diff = $this->getDiffCompare($whichBeans,$expectedBeans);
				$type = "energy vs energy";
			}else{
				// harvested data.....
				$beans = $this->readEnergyValues($invtnum, 'month', 1, $whichYear."-".$whichMonth."-1");
				//var_dump($beans);
				$whichBeans = $beans[0];
				//get last kwh value
				$lastKWH = $whichBeans[count($whichBeans)-1]['KWH'];

				// complete array to days of month and fill it with the last KWH value
				for ($i = count($whichBeans); $i < $whichMonthDays; $i++) {
					$iWhichDay = $i+1;
					$whichBeans[$i]['time'] = strtotime(date("Y")."/".$whichMonth."/".$iWhichDay);
					$whichBeans[$i]['KWH'] = (float)$lastKWH;
					$whichBeans[$i]['displayKWH'] =  sprintf("%01.2f",(float)$lastKWH);
					$whichBeans[$i]['harvested'] =  0;
				}
				// create string to get month percentage
				$expectedMonthString = 'expected'.strtoupper(date('M', strtotime($compareMonth."/01/".date("Y"))));

				$inverter = $config->getInverterConfig($invtnum);
				$expectedPerc = $inverter->$expectedMonthString;
				$expectedkwhYear = $inverter->expectedkwh;

				// calculate month kWh = (year/100*month perc)
				$expectedKWhMonth = ($expectedkwhYear / 100)*$expectedPerc;

				// calculate daily expected, based on month day (28,29,30,31 days)
				$expectedKwhPerDay = ($expectedKWhMonth/$expectedMonthDays);

				// create expected
				for ($i = 0; $i < $expectedMonthDays; $i++) {
					$iCompareDay = $i+1;
					$expectedBeans[$i]['time'] = strtotime(date("Y")."/".$compareMonth."/".$iCompareDay);
					$expectedBeans[$i]['KWH'] =  (float)number_format((float)$expectedBeans[$i-1]['KWH']+(float)$expectedKwhPerDay,2,'.','');
					$expectedBeans[$i]['displayKWH'] =  sprintf("%01.2f",(float)$expectedBeans[$i-1]['KWH']+(float)$expectedKwhPerDay);
					$expectedBeans[$i]['harvested'] = (float)number_format((float)$expectedKwhPerDay,2,'.','');  
				}
				//var_dump($expectedBeans);
				$type = "energy vs expected";
				$diff = $this->getDiffCompare($whichBeans,$expectedBeans);
			}
		}
		
		return array(
				"expectedKWhMonth"=>$expectedKWhMonth,
				"expectedkwhYear"=>$expectedkwhYear,
				"expectedPerc"=>$expectedPerc,
				"whichMonthDays"=>$whichMonthDays,
				"compareMonthDays"=>$compareMonthDays,
				"compareBeans"=>$this->beansToGraphPoints($expectedBeans),
				"whichBeans"=>$this->beansToGraphPoints($whichBeans),
				"whichCompareDiff"=>$diff,
				"expectedMonthString"=>$expectedMonthString,
				"expectedPerc"=>$expectedPerc,
				"type"=>$type

		);
	}


	public function getDiffCompare($whichBeans,$expectedBeans){

		$whichCount = count($whichBeans);
		$expectedCount = count($expectedBeans);
		if($whichCount>=$expectedCount){
			for ($i = 0; $i < $whichCount; $i++) {
				$diffCalc = $whichBeans[$i]['KWH']-$expectedBeans[$i]['KWH'];
				$diffHarvestedCalc = $whichBeans[$i]['harvested']-$expectedBeans[$i]['harvested'];
				
				$diff[] = array("diff"=>sprintf("%01.2f",(float)$diffCalc),"diffHar"=>$diffHarvestedCalc);
			}
		}else{
			for ($i = 0; $i < $expectedCount; $i++) {
				$diffCalc = $whichBeans[$i]['KWH']-$expectedBeans[$i]['KWH'];
				$diffHarvestedCalc = $whichBeans[$i]['harvested']-$expectedBeans[$i]['harvested'];
				
				$diff[] = array("diff"=>sprintf("%01.2f",(float)$diffCalc),"diffHar"=>$diffHarvestedCalc);
			}

		}
		return $diff;
	}


	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getYearSumPowerPerMonth($invtnum,$startDate){
		$inverter = array();
		$beginEndDate = Util::getBeginEndDate('year', 1,$startDate);

		if ($invtnum>0){
			$beans = R::getAll("SELECT INV,SUM(KWH) as KWH, time
					FROM Energy WHERE INV = :INV AND time > :beginDate AND time < :endDate GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time ASC",
					array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}else{
			$beans = R::getAll("SELECT INV,SUM(KWH) as KWH, time
					FROM Energy WHERE time > :beginDate AND time < :endDate GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time ASC",
					array(':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}
		if(count($beans)==0){
			$newBean = null;
		}else{
			$firstMonth = date("n",$beans[0]['time']);
			$lastMonth = date("n",$beans[count($beans)-1]['time']);

			$inverter = $this->readInverter($invtnum);
			$expected = $inverter->expectedkwh;
			$invExp[0] = ($expected/100)*$inverter->expectedJAN;
			$invExp[1] = ($expected/100)*$inverter->expectedFEB;
			$invExp[2] = ($expected/100)*$inverter->expectedMAR;
			$invExp[3] = ($expected/100)*$inverter->expectedAPR;
			$invExp[4] = ($expected/100)*$inverter->expectedMAY;
			$invExp[5] = ($expected/100)*$inverter->expectedJUN;
			$invExp[6] = ($expected/100)*$inverter->expectedJUL;
			$invExp[7] = ($expected/100)*$inverter->expectedAUG;
			$invExp[8] = ($expected/100)*$inverter->expectedSEP;
			$invExp[9] = ($expected/100)*$inverter->expectedOCT;
			$invExp[10] = ($expected/100)*$inverter->expectedNOV;
			$invExp[11] = ($expected/100)*$inverter->expectedDEC;

			$ii	=0;
			for ($i = 0; $i < 12; $i++) {
				$iMonth = $i+1;
				// if $i <= 5
				if($iMonth<$firstMonth || $iMonth>$lastMonth){

					$newBean[$i]['time'] = strtotime(date("01-".$iMonth."-Y"));
					//$newBean[$i]['time'] = date ( "n"  ,$newBean[$i]['time'] );
					$newBean[$i]['KWH'] = number_format(0,0,',','');
					$newBean[$i]['Exp'] = number_format($invExp[$i],0,',','');
					$newBean[$i]['Diff'] = number_format($newBean[$i]['KWH']-$newBean[$i]['Exp'],0,',','');
					if($iMonth > $lastMonth){
						$cumExp += $invExp[$i];
						$newBean[$i]['cumExp']=number_format($cumExp,0,',','');
					}
					$cumKWH += 0;
					$newBean[$i]['cumKWH']=number_format($cumKWH,0,',','');
					$newBean[$i]['cumDiff']=number_format($cumKWH-$cumExp,0,',','');
					$newBean[$i]['what'] = 'prepend';
				}else{

					$newBean[$i]['time'] = $beans[$ii]['time'];
					//$newBean[$i]['time'] = date ( "n"  ,$beans[$ii]['time']);
					$newBean[$i]['KWH'] =number_format($beans[$ii]['KWH'],0,',','');
					$newBean[$i]['Exp'] =number_format($invExp[$i],0,',','');
					$newBean[$i]['Diff'] = number_format($newBean[$i]['KWH']-$newBean[$i]['Exp'],0,',','');

					$cumExp += $invExp[$i];
					$newBean[$i]['cumExp']=number_format($cumExp,0,',','');

					$cumKWH += $beans[$ii]['KWH'];
					$newBean[$i]['cumKWH']=number_format($cumKWH,0,',','');
					$newBean[$i]['cumDiff']=number_format($cumKWH-$cumExp,0,',','');
					$newBean[$i]['what'] = 'apprepend';
					$ii++;
				}
			}
		}
		//var_dump($newBean);
		return array("energy"=>$this->CompareBeansToGraphPoints($newBean),"expected"=>0);
	}


	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getDayHistoryPerRecord($invtnum=1){
		$beginEndDate = Util::getBeginEndDate('today', 1);
		$beans = R::getAll("SELECT INV, GP,KWHT, strftime ( '%H:%M %d-%m-%Y' , datetime ( time , 'unixepoch' ) ) AS date FROM history WHERE INV = :INV  AND time > :beginDate AND time < :endDate order by time DESC",array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		return $beans;
	}



	/**
	 * return a array with GraphPoints
	 * @param date $startDate ("Y-m-d") ("1900-12-31"), when no date given, the date of today is used.
	 * @return array($beginDate, $endDate);
	 */
	public function getGraphDayPoint($invtnum,$type, $startDate){
		($type == 'today')?$type='day':$type=$type;
		$graph = new Graph();
		
		
		$beans = $this->readTablesPeriodValues($invtnum, 'history', $type, $startDate);
		
		$graph->axes['xaxis'] = array('label'=>'','renderer'=>'DateAxisRenderer',
				'tickRenderer'=>'CanvasAxisTickRenderer','labelRenderer'=>'CanvasAxisLabelRenderer',
				'tickInterval'=>3600,'tickOptions'=>array('formatter'=>'DayDateTickFormatter','angle'=>-45));
		
		$graph = $this->DayBeansToGraphPoints($beans,$graph,$startDate);		
		
		$hookGraph = HookHandler::getInstance()->fire("GraphDayPoints",$invtnum,$startDate,$type);

		foreach ($hookGraph->axes as $key => $value){
			$graph->axes[$key] = $value;
		}	
		
		foreach($hookGraph->series as $series){
			$graph->series[] = $series;
		}

		foreach ($hookGraph->points as $key => $value){
			$graph->points[$key] = $value;
		}

		if($hookGraph->timestamp!=null){
			$graph->timestamp = $hookGraph->timestamp;
		}
		
		
		if($hookGraph->metaData != null){
			$graph->metaData= array_merge_recursive((array)$hookGraph->metaData,(array)$graph->metaData);
		}
		$array['graph'] = $graph; 


		return $array;
	}
	
	
	public function mergePointArrays($beans,$hookBeans){
		$array = array();
		$config = Session::getConfig();

		if(is_array($hookBeans->graph['timestamp'])){
			$array['timestamp'] = $hookBeans->graph['timestamp'];
		}
 
		if( $beans->graph['points']!=null AND $hookBeans->graph['points']!=null){
			//echo "both Points Beans en HookBeans";
			$array['graph'] = array_merge_recursive($beans->graph,$hookBeans->graph);
		}elseif($beans != null && is_array($beans->graph['points'])){
			//echo "Points Beans";
			$array['graph'] = $beans->graph;
		}elseif($hookBeans != null && is_array($hookBeans->graph['points'])){
			//echo "Points hookBeans";
			$array['graph'] = $hookBeans->graph;
		}else{
			//echo 'niets';
			$array['graph'] = array();
		}
		
		

		
		return $array;
	}

	/**
	 * return a array with GraphPoints
	 * @param date $startDate ("Y-m-d") ("1900-12-31"), when no date given, the date of today is used.
	 * @return array($beginDate, $endDate);
	 */
	public function getGraphPoint($invtnum,$type, $startDate){
		($type == 'today')?$type='day':$type=$type;
		// if $type
		(stristr(strtolower($type), 'day') === FALSE) ?	$table = "energy" : $table = "history";


		$beans = $this->readTablesPeriodValues($invtnum, $table, $type, $startDate);
		if(strtolower($table) == "history"){
			// NO history bean? Create a dummy bean...
			(!$beans) ? $beans[0] = array('time'=>time(),'KWH'=>0,'KWHT'=>0) : $beans = $beans;
			return $this->DayBeansToGraphPoints($beans);
		}else{
			return $this->PeriodBeansToGraphPoints($beans,$type,$startDate);
		}
	}
	

	/**
	 * return a array that can be understand by JQplot
	 * @param array $beans from $this->getGraphPoint()
	 * @return array($beginDate, $endDate);
	 */
	public function beansToGraphPoints($beans){
		$points = array();
		$cumPower = 0;

		foreach ($beans as $bean){
			//$cumPower += $bean['KWH'];
			//echo mktime(0, 0, 0,date("m",$bean['time']),date("d",$bean['time']),date("Y",$bean['time']))."   ";
			$points[] = array (
					mktime(0, 0, 0,date("m",$bean['time']),date("d",$bean['time']),date("Y",$bean['time']))*1000,
					date("d-m-Y",$bean['time']),
					$bean['KWH'],
					$bean['displayKWH'],
					$bean['harvested']
			);
			
			
		}
		//number_format($cumPower,2,'.',''),
		// if no data was found, create 1 dummy point for the graph to render
		if(count($points)==0){
			$cumPower = 0;
			$points[] = array (time()* 1000, 0,0);
		}

		$lastDays = new LastDays();
		$lastDays->points=$points;
		$lastDays->KWHT=$cumPower;
		return $lastDays;
	}




	/**
	 * return a array that can be understand by JQplot
	 * @param array $beans from $this->getGraphPoint()
	 * @return array($beginDate, $endDate);
	 */
	public function CompareBeansToGraphPoints($beans){
		//var_dump($beans);
		$points = array();
		$cumPower = 0;
		foreach ($beans as $bean){
			$cumPower += $bean['KWH'];
			$points[] = array (
					//$bean['time'],
					//mktime(0, 0, 0,date("m",$bean['time']),1,date("Y",$bean['time']))*1000,
					floatval($bean['KWH']),
					"1-".date("m-Y",$bean['time']),
					floatval($bean['Exp']),
					floatval($bean['Diff']),
					floatval($bean['cumExp']),
					floatval($bean['cumKWH']),
					floatval($bean['cumDiff']),
					$bean['time']
			);
		}

		// if no data was found, create 1 dummy point for the graph to render
		if(count($points)==0){
			$cumPower = 0;
			$points[] = array (time()* 1000, 0,0);
		}

		$lastDays = new LastDays();
		$lastDays->points=$points;
		$lastDays->KWHT=$cumPower;
		return $lastDays;
	}


	/**
	 * return a array that can be understand by JQplot
	 * @param array $beans from $this->getGraphPoint()
	 * @return array($beginDate, $endDate);
	 */
	public function PeriodBeansToGraphPoints($beans,$period,$date){
		$points = array();
		$cumPower=0;
		foreach ($beans as $bean){
			$cumPower += $bean['KWH'];
			$points[] = array (Util::getTimestampOfDate(0,0,0, date("d",$bean['time']),date("m",$bean['time']), date("Y",$bean['time']))*1000,
					floatval($bean['KWH']),
					date("Y-m-d",$bean['time']),
					floatval($cumPower),
					$bean['KWH']
			);
		}

		// if no data was found, create 1 dummy point for the graph to render
		if(count($points)==0){
			$cumPower = 0;
			$points[] = array (time()* 1000, 0,0);
		}else{
			if (strtolower($period) == 'month'){
				$endOfMonth =Util::getTimestampOfDate(0,0,0,  date('t'),date("m",strtotime($date)), date("Y"));

			}else{
				$beginEndWeek = Util::getStartAndEndOfWeek(strtotime($date));
				$endOfMonth = $beginEndWeek[1];
			}
			$countPoints = count($points)-1;

			$time = strtotime(date("d-m-Y",($points[$countPoints][0])/1000));
			$time += 86400;
			while ($time<$endOfMonth){
				$time += 86400;
				$points[] = array (
						($time)*1000,
						0,
						date("Y-m-d",$time),
						floatval($cumPower)
				);
			}
		}

		$lastDays = new LastDays();
		$lastDays->points=$points;
		$lastDays->KWHT=$cumPower;
		$lastDays->table=$table;
		return $lastDays;
	}



	/**
	 * read Energy Values
	 * @param int $invtnum
	 * @param str $type can be: today, week, month, year
	 * @param int $count
	 * @param date $startDate
	 * @param str $maxType
	 *
	 */
	public function readEnergyValues($invtnum, $type, $count, $startDate){
		$config = Session::getConfig();
		$energyBeans = $this->readTablesPeriodValues($invtnum, "energy", $type,$startDate);
		$Energy = array();

		foreach ($energyBeans as $energyBean){
			$invConfig = $this->readInverter($energyBean['INV']);
			$energyBean['KWH'] = (float)$energyBean['KWH'];
			$Energy['INV'] =  $energyBean['INV'];
			$Energy['KWHKWP'] = number_format($energyBean['KWH'] / ($invConfig->plantpower/1000),2,',','');
			$Energy['harvested'] = number_format((float)$energyBean['KWH'],2,'.','');
			$Energy['KWH'] += number_format((float)$energyBean['KWH'],2,'.','');
				
			$cum +=$energyBean['KWH'];
			$Energy['displayKWH'] = sprintf("%01.2f",(float)$cum);
			$Energy['CO2'] =Formulas::CO2kWh($energyBean['KWH'],$config->co2kwh);
			$Energy['time'] = $energyBean['time'];
			$Energy['KWHT'] = number_format($energyBean['KWHT'],2,',','');
			$KWHT += $energyBean['KWH'];
			$energy[] = $Energy;
		}
		return array($energy,$KWHT);
	}
	/**
	 *
	 * @param unknown_type $invtnum
	 * @param unknown_type $type
	 * @param unknown_type $count
	 * @param unknown_type $startDate
	 */
	public function readPmaxValues($invtnum, $type, $count, $startDate){

		$beans = $this->readTablesPeriodValues($invtnum, "pMaxOTD", $type,$startDate);

		foreach ($beans as $bean){
			$oMaxPowerToday = new MaxPowerToday();
			$oMaxPowerToday->GP =  number_format($bean['GP'],2,',','');
			$oMaxPowerToday->INV = $bean['INV'];
			$oMaxPowerToday->time = date("H:i:s d-m-Y",$bean['time']);;
			$maxPowerDay[] = $oMaxPowerToday;
		}
		return array($maxPowerDay);
	}
	/**
	 *
	 */
	public function readPageIndexData($config) {
		// summary live data
		$list = array();
		$readMaxPowerValues = $this->getMaxTotalEnergyValues(0,"all");
		$list['summary'] = $readMaxPowerValues;
		return $list;
	}

	/**
	 *
	 */
	public function readPageIndexLiveValues($config) {
		// summary live data
		$inverters = array();
		$GP  = 0;
		$I1P = 0;
		$I2P = 0;
		$IP  = 0;
		$EFF = 0;

		$liveBean = array();

		foreach ($config->inverters as $inverter){
			if($inverter->type=="production"){
				$liveBean =  R::findOne('live',' INV = :INV ', array(':INV'=>$inverter->id));

				$oInverter = 	array();

				
				if(Util::isSunDown(Session::getConfig())){
					$live = new Live();
					$live->name = $inverter->name;
					$live->status = _('sleeping');
					$live->time = date("H:i:s");
					$live->INV = $inverter->id;
					$live->GP = number_format(0,0,',','');
					$live->GA = number_format(0,0,',','');
					$live->GV = number_format(0,0,',','');
					$live->I1P = number_format(0,2,',','');
					$live->I1A = number_format(0,2,',','');
					$live->I1V = number_format(0,2,',','');
					$live->I1Ratio = number_format(0,2,',','');
					$live->I2P = number_format(0,2,',','');
					$live->I2A = number_format(0,2,',','');
					$live->I2V = number_format(0,2,',','');
					$live->I2Ratio = number_format(0,2,',','');

					$live->IP = number_format(0,2,',','');
					$live->EFF = number_format(0,2,',','');
				}else{
					$liveBean =  R::findOne('live',' INV = :INV ', array(':INV'=>$inverter->id));

					$ITP = round($liveBean['I1P'],2)+round($liveBean['I2P'],2);
					
					$GP  += $liveBean['GP'];
					$I1P += $liveBean['I1P'];
					$I2P += $liveBean['I2P'];
					$IP  += $ITP;
					$EFF += $liveBean['EFF'];
					

					$live = new Live();
					$live->name = $inverter->name;
					$live->status = _('awake');
					$live->time = date("H:i:s",$liveBean['time']);
					$live->INV = $liveBean['INV'];
					$live->GP = ($liveBean['GP']<1000) ? number_format($liveBean['GP'],1,'.','') : number_format($liveBean['GP'],0,'','');
					$live->GA = ($liveBean['GA']<1000) ? number_format($liveBean['GA'],1,'.','') : number_format($liveBean['GA'],0,'','');
					$live->GV = ($liveBean['GV']<1000) ? number_format($liveBean['GV'],1,'.','') : number_format($liveBean['GV'],0,'','');
					
					$live->I1P = ($liveBean['I1P']<1000) ? number_format($liveBean['I1P'],1,'.','') : number_format($liveBean['I1P'],0,'','');
					$live->I1A = ($liveBean['I1A']<1000) ? number_format($liveBean['I1A'],1,'.','') : number_format($liveBean['I1A'],0,'','');
					$live->I1V = ($liveBean['I1V']<1000) ? number_format($liveBean['I1V'],1,'.','') : number_format($liveBean['I1V'],0,'','');
					$live->I1Ratio = ($liveBean['I1Ratio']<1000) ? number_format($liveBean['I1Ratio'],1,'.','') : number_format($liveBean['I1Ratio'],0,'','');
					
					$live->I2P = ($liveBean['I2P']<1000) ? number_format($liveBean['I2P'],1,'.','') : number_format($liveBean['I2P'],0,'','');
					$live->I2A = ($liveBean['I2A']<1000) ? number_format($liveBean['I2A'],1,'.','') : number_format($liveBean['I2A'],0,'','');
					$live->I2V = ($liveBean['I2V']<1000) ? number_format($liveBean['I2V'],1,'.','') : number_format($liveBean['I2V'],0,'','');
					$live->I2Ratio = ($liveBean['I2Ratio']<1000) ? number_format($liveBean['I2Ratio'],1,'.','') : number_format($liveBean['I2Ratio'],0,'','');
					
					$live->IP = ($liveBean['IP']<1000) ? number_format($liveBean['IP'],1,'.','') : number_format($liveBean['IP'],0,'','');
					$live->EFF = ($liveBean['EFF']<1000) ? number_format($liveBean['EFF'],1,'.','') : number_format($liveBean['EFF'],0,'','');
					
				}

				$oInverter["id"] = $liveBean['INV'];
				$oInverter["currentTime"] = time();
				$oInverter["live"] = $live;

				$inverters[] = $oInverter;
			}
		}

		$sum = array();
		$sum['GP'] = ($GP<1000) ? number_format($GP,1,'.','') : number_format($GP,0,'','');
		$sum['I1P'] = ($I1P<1000) ? number_format($I1P,1,'.','') : number_format($I1P,0,'','');
		$sum['I2P'] = ($I2P<1000) ? number_format($I2P,1,'.','') : number_format($I2P,0,'','');
		$sum['IP'] = ($IP<1000) ? number_format($IP,1,'.','') : number_format($IP,0,'','');
		$sum['EFF'] = ($EFF<100) ? number_format($EFF,1,'.','') : number_format($EFF,0,'','');

		return array('inverters'=>$inverters,'sum'=>$sum);
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMaxTotalEnergyValues($invtnum,$type,$limit=1){
		// get config
		$config = Session::getConfig();
		// init var
		$sumPlantPower = 0;
		// loop through inverters
		foreach ($config->inverters as $inverter) {
			//sum plantpower of all inverters
			$sumPlantPower += $inverter->plantpower/1000;
		}

		// type to lowercase
		$type = strtolower($type);
		// init array
		$avgEnergyBeansToday[] = array();
		// init var
		$initialkwh = 0;


		if($type == "today" || $type == "day" || $type == "all"){
			$totalEnergyBeansToday = $this->readTablesPeriodValues(0, "energy", "today", date("d-m-Y"));
			$maxPowerBeansToday = $this->readTablesPeriodValues(0, "pMaxOTD", "today", date("d-m-Y"));
			
			if (count ( $maxPowerBeansToday )==0 ){
				$avgEnergyBeansToday= number_format(0,3,',','');
				$totalEnergyBeansToday[]['KWH']=0;
				$totalEnergyBeansTodayKWHKWP = number_format('0,000',3,',','');
			}else{
				$totalEnergyBeansTodayKWHKWP= number_format(($totalEnergyBeansToday[0]['KWH'] / $sumPlantPower),3,',','');
				for ($i = 0; $i < count($maxPowerBeansToday); $i++) {
					$maxPowerBeansToday[$i]['sumkWh'] = number_format($maxPowerBeansToday[$i]['sumkWh'],2,',','');
					$avgEnergyBeansToday= number_format($totalEnergyBeansToday[$i]['KWH'],3,',','');
					$totalEnergyBeansToday[$i]['KWH'] = number_format($totalEnergyBeansToday[$i]['KWH'],3,',','');
				}

			}
			
			if(count ( $maxPowerBeansToday )==0 ){
				$maxPowerBeansToday[]['GP']="0";
			}
		}

		if($type == "week" || $type == "all"){
			$totalEnergyBeansWeek = R::getAll("SELECT COUNT(kwh) as countkWh,MAX ( kwh ) AS kWh, SUM (kwh) AS sumkWh, strftime ( '%Y%W' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY date ORDER BY time DESC limit 0,:limit",array(':limit'=>$limit));
			$avgEnergyBeansWeek = number_format($totalEnergyBeansWeek[0]['sumkWh']/$totalEnergyBeansWeek[0]['countkWh'],2,',','');

			for ($i = 0; $i < count($totalEnergyBeansWeek); $i++) {
				$totalEnergyBeansWeek[$i]['sumkWh'] = number_format($totalEnergyBeansWeek[$i]['sumkWh'],2,',','');
			}
			$totalEnergyBeansWeekKWHKWP= number_format($totalEnergyBeansWeek[0]['sumkWh'] / $sumPlantPower,2,',','');

		}

		if($type == "month" ||  $type == "all"){
			if ($invtnum>0){
				$totalEnergyBeansMonth = R::getAll("SELECT INV,COUNT(kwh) as countkWh,MAX ( kwh ) AS kWh, SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit,':INV'=>$invtnum));
				$avgEnergyBeansMonth = number_format($totalEnergyBeansMonth[0]['sumkWh']/$totalEnergyBeansMonth[0]['countkWh'],2,',','');
				for ($i = 0; $i < count($totalEnergyBeansMonth); $i++) {
					$totalEnergyBeansMonth[$i]['sumkWh'] = number_format($totalEnergyBeansMonth[$i]['sumkWh'],2,',','');
				}
			}else{
				$totalEnergyBeansMonth = R::getAll("SELECT INV,COUNT(kwh) as countkWh, MAX ( kwh ) AS kWh, SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit));
				$avgEnergyBeansMonth = number_format($totalEnergyBeansMonth[0]['sumkWh']/$totalEnergyBeansMonth[0]['countkWh'],2,',','');
				for ($i = 0; $i < count($avgEnergyBeansMonth); $i++) {
					$totalEnergyBeansMonth[$i]['sumkWh'] = number_format($totalEnergyBeansMonth[$i]['sumkWh'],2,',','');
				}
			}
			$totalEnergyBeansMonthKWHKWP= number_format($totalEnergyBeansMonth[0]['sumkWh'] / $sumPlantPower,2,',','');
		}

		if($type == "year" || $type == "all"){
			if ($invtnum>0){
				$totalEnergyBeansYear = R::getAll("SELECT COUNT(kwh) as countkWh,MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh,strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit,':INV'=>$invtnum));
				$avgEnergyBeansYear = number_format($totalEnergyBeansYear[0]['sumkWh']/$totalEnergyBeansYear[0]['countkWh'],2,',','');
				for ($i = 0; $i < count($totalEnergyBeansYear); $i++) {
					$totalEnergyBeansYear[$i]['sumkWh'] = number_format($totalEnergyBeansYear[$i]['sumkWh'],2,',','');
				}
			}else{
				$totalEnergyBeansYear = R::getAll("SELECT COUNT(kwh) as countkWh,MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh,strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit));
				$avgEnergyBeansYear = number_format($totalEnergyBeansYear[0]['sumkWh']/$totalEnergyBeansYear[0]['countkWh'],2,',','');
				for ($i = 0; $i < count($totalEnergyBeansYear); $i++) {
					$totalEnergyBeansYear[$i]['sumkWh'] = number_format($totalEnergyBeansYear[$i]['sumkWh'],2,',','');
				}
			}
			$totalEnergyBeansYearKWHKWP= number_format($totalEnergyBeansYear[0]['sumkWh'] / $sumPlantPower,2,',','');
		}

		if($type == "overall" || $type == "all"){
			if ($invtnum>0){
				$totalEnergyBeansOverall = R::getAll("SELECT COUNT(kwh) as countkWh, MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV order by time limit 0,:limit",array(':limit'=>$limit,':INV'=>$invtnum));
				$avgEnergyBeansOverall = number_format($totalEnergyBeansOverall[0]['sumkWh']/$totalEnergyBeansOverall[0]['countkWh'],2,',','');
				for ($i = 0; $i < count($totalEnergyBeansOverall); $i++) {
					$totalEnergyBeansOverall[$i]['sumkWh'] = number_format($totalEnergyBeansOverall[$i]['sumkWh'],2,',','');
				}
			}else{
				$totalEnergyBeansOverall = R::getAll("SELECT COUNT(kwh) as countkWh, MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy order by time limit 0,:limit",array(':limit'=>$limit));
				$avgEnergyBeansOverall = number_format($totalEnergyBeansOverall[0]['sumkWh']/$totalEnergyBeansOverall[0]['countkWh'],2,',','');
				for ($i = 0; $i < count($totalEnergyBeansOverall); $i++) {
					$totalEnergyBeansOverall[$i]['sumkWh'] = number_format($totalEnergyBeansOverall[$i]['sumkWh'],2,',','');
				}
			}
			$totalEnergyBeansOverallKWHKWP= number_format($totalEnergyBeansOverall[0]['sumkWh'] / $sumPlantPower,2,',','');
		}
		

		foreach ($config->inverters as $inverter){
			$initialkwh += floatval($inverter->initialkwh);
		}
		$tempTotal = 0;
		$totalEnergyOverallTotal = number_format($initialkwh + floatval($totalEnergyBeansOverall[0]['sumkWh']),2,',','');
		$totalEnergyOverallTotalKWHKWP =  number_format($totalEnergyOverallTotal / $sumPlantPower,2,',','');


		$energy = array(
				"maxPowerToday"=>$maxPowerBeansToday,
				"totalEnergyToday"=>$totalEnergyBeansToday,
				"avgEnergyToday"=>$avgEnergyBeansToday,
				"totalEnergyBeansTodayKWHKWP"=>$totalEnergyBeansTodayKWHKWP,

				"totalEnergyWeek"=>$totalEnergyBeansWeek,
				"avgEnergyWeek"=>$avgEnergyBeansWeek,
				"totalEnergyBeansWeekKWHKWP"=>$totalEnergyBeansWeekKWHKWP,

				"totalEnergyMonth"=>$totalEnergyBeansMonth,
				"avgEnergyMonth"=>$avgEnergyBeansMonth,
				"totalEnergyBeansMonthKWHKWP"=>$totalEnergyBeansMonthKWHKWP,

				"totalEnergyYear"=>$totalEnergyBeansYear,
				"avgEnergyYear"=>$avgEnergyBeansYear,
				"totalEnergyBeansYearKWHKWP"=>$totalEnergyBeansYearKWHKWP,

				"totalEnergyOverall"=>$totalEnergyBeansOverall,
				"avgEnergyOverall"=>$avgEnergyBeansOverall,
				"totalEnergyBeansOverallKWHKWP"=>$totalEnergyBeansOverallKWHKWP,
				"initialkwh" => $initialkwh,
				"totalEnergyOverallTotal"=> $totalEnergyOverallTotal,
				"totalEnergyBeansOverallTotalKWHKWP"=>$totalEnergyOverallTotalKWHKWP
		);
		return $energy;
	}

	public function dropboxTokenExists(){
		$beans =  R::findAndExport('dropboxOauthTokens',' id >0');
		return (count($beans) > 0 ? true : false);
	}

	public function dropboxSaveFile($file){


		$bean = R::findOne('dropboxFilenameCaching',' path = :path ',array(':path'=>$file->path));

		if(!$bean){
			$bean = R::dispense('dropboxFilenameCaching');

			$bean->path = $file->path;
			$bean->fullPath = $file->fullPath;
			$bean->client_mtime = $file->client_mtime;
			$bean->bytes = $file->bytes;
			$bean->active = 1;
			//Store the bean

			$id = R::store($bean);
		}

	}
	public function dropboxDropFile($path){

		return R::exec( 'delete from dropboxFilenameCaching where path=:path',array(':path'=>$path));

	}


	public function dropboxCheckActive($files){
		R::exec( 'update dropboxFilenameCaching set active=0 where id>0');
			
		foreach($files as $file){
			R::exec( 'update dropboxFilenameCaching set active=1 where path=:path',array(':path'=>$file->path) );
		}
		R::exec( 'delete from dropboxFilenameCaching where active=0' );
	}


	public function dropboxGetFilesFromDB(){
		$beans =  R::findAndExport('dropboxFilenameCaching',' id > 0 ORDER BY client_mtime DESC');
		$i=0;
		if($beans){
			foreach ($beans as $bean) {
				$datas['client_mtime'] = date("D d-m-Y H:i",$bean['client_mtime']);
				$datas['fullPath'] = $bean['fullPath'];
				$datas['path'] = substr($bean['path'], 1, strlen($bean['path']));
				$datas['id'] = $i;
				$datas['num'] = $i+1;
				$datas['size'] = number_format($bean['bytes']/1000000,2,'.',''); // Bytes -> MegaByte
				$totalBackupSize += $bean['bytes'];
				$i++;
				$data['files'][] = $datas;
			}
			$data['noData'] = 0;
		}else{
			$data['noData'] = 1;
			$data['files'][0]['noData']='No backups';
			$data['files'][0]['noData2']='It could be that we are out of sync with your dropbox, please hit the Sync link below';
		}
		$data['totalBackups'] = $i+1;
		$data['totalBackupSize'] = number_format($totalBackupSize/1000000,2,'.',''); // Bytes -> MegaByte
		$data['avarageBackupSize'] = number_format(($totalBackupSize/($i+1))/1000000,2,'.',''); // Bytes -> MegaByte
			
		return $data;
	}

	
	
	public function remove_hybridauth_session($current_user_id, $type){
	
		$bean =  R::findOne('hybridUsersConnections',
				' user_id = :user_id AND type :type ',
				array(':user_id'=>$current_user_id,
						':type'=> $type
				)
		);
		R::trash( $bean );
		
	}
	
	/**
	 * 
	 * @param unknown $current_user_id  = id of user
	 * @param unknown $hybridauth_session_data  = Session data
	 * @param unknown $user_profile = user profile
	 * @param unknown $type = Twitter/Facebook
	 */
	
	public function save_hybridauth_session($current_user_id, $hybridauth_session_data,$user_profile, $type){
		$bean = R::findOne('hybridUsersConnections',' user_id = :user_id and type = :type LIMIT 1',array(':user_id'=>$current_user_id,':type'=>$type));

		if(!$bean){
			$bean = R::dispense('hybridUsersConnections');

			$bean->user_id = $current_user_id;
			$bean->hybridauth_session = $hybridauth_session_data;
			$bean->updated_at = strtotime("now");
			$bean->displayName = $user_profile->displayName;
			$bean->type= $type;

			//Store the bean

			$id = R::store($bean);
		}
	}

	/**
	 * 
	 * @param int $user_id = id of user
	 * @param str $type = Twitter/Facebook
	 * @return Ambigous <multitype:, multitype:NULL >
	 */
	function get_hybridauth_session( $user_id, $type ){
		$sessionData = '';
		$beans = R::findAndExport('hybridUsersConnections',' user_id = :user_id and type = :type LIMIT 1',array(':user_id'=>$user_id,':type'=>$type));
		if ($beans){
			foreach ($beans as $bean){
				$sessionData= $bean;
			}
		}
		if($sessionData){
			return $sessionData;
		}
	}
	/**
	 * changeInverterStatus
	 *
	 * @param int $status // 1=active, 0=sleep
	 * @param Inverter $inverter // Inverter object
	 * @return boolean // changed yes or no;
	 */

	function changeInverterStatus(Inverter $inverter, $state){
		// get inverter bean
		$bean = R::load('inverter', $inverter->id);

		// look if we have a bean
		if (!$bean){
			// If we can't find the bean there is a serious problem exit!
			HookHandler::getInstance()->fire("onError", "changeInverterStatus: Could not find inverter bean for id:" . $inverter->id);
			return null;
		}

		// check if we are going to change the inverter status
		$changed = false;
		if($bean->state != $state){
			// oo we are going to change the inverter, so we set it to TRUE
			$changed = true;
			// change the bean to the new status for this inverter
			$bean->state = $state;
				
			//Store the bean with the new inverter status
			R::store($bean,$bean->id);
		}
		return $changed;
	}

	/**
	 * setDeviceType
	 *
	 * @param Inverter $inverter // Inverter object
	 * @return boolean // changed true;
	 */

	function setDeviceType(Inverter $inverter){
		// get inverter bean
		$bean = R::load('inverter', $inverter->id);
		$bean->type = "production";
		//Store the bean with the new inverter type
		R::store($bean,$bean->id);
		return true;
	}
}