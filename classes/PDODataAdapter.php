<?php
class PDODataAdapter {
	private static $instance;
	
	// Singleton
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new PDODataAdapter();
		}
		return self::$instance;
	}
	
	
	function __construct() {
		$config = new Config;
		R::setup('sqlite:'.$config->dbHost );
		R::debug(false);
		R::setStrictTyping(false);
	}

	function __destruct() {
		//print "Destroying " . $this->name . "\n";
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
	public function readMaxPowerToday($invtnum) {
		$bean =  R::findOne('pMaxOTD',
				' INV = :INV AND SDTE LIKE :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.date('Ymd').'%'
				)
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
	public function addHistory($invtnum, Live $live) {
		$bean = R::dispense('history');

		$bean->SDTE = $live->SDTE;
		$bean->time = $live->time;
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

		//Store the bean
		$id = R::store($bean);
		return $id;

	}

	/**
	 * Read the history file
	 * @param int $invtnum
	 * @param string $date
	 * @return array<Live> $live (No Live but BEAN object!!)
	 */
	// TODO :: There's no Live object returned....?!

	public function readHistory($invtnum, $date) {
		$bean =  R::findAndExport(
				'history',
				' INV = :INV AND SDTE like :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.date('Ymd').'%'
				)
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
	 * add an energy line
	 * @param int $invtnum
	 * @param MaxPowerToday $energy
	 * @param int $year
	 */
	public function addEnergyOld($invtnum, Energy $energy, $year = null) {
		$bean = R::dispense('energy');

		$bean->INV = $invtnum;
		$bean->SDTE = $energy->SDTE;
		$bean->time = $energy->time;
		$bean->KWH = $energy->KWH;
		$bean->KWHT = $energy->KWHT;
		$bean->co2 = $energy->co2;

		$id = R::store($bean);
	}

	/**
	 * write the max power today to the file
	 * @param int $invtnum
	 * @param MaxPowerToday $mpt
	 */
	public function addEnergy($invtnum, Energy $energy) {
		$bean =  R::findOne('energy',
				' INV = :INV AND SDTE LIKE :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.date('Ymd').'%'
				)
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
	 * read the Energy of today
	 * @param int $invtnum
	 * @return MaxPowerToday
	 */
	public function readEnergyDay($invtnum,$date) {
		$date = ($date == 0) ? time(): $date;

		$bean =  R::findOne('energy',
				' INV = :INV AND Time LIKE :date ',
				array(':INV'=>$invtnum,
						':date'=> '%'.$date.'%'
				)
		);
		return $bean;
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
	public function DayBeansToGraphPoints($beans){
		$i=0;
		$firstBean = array();
		$preBean = array();
		$points = array();
		$KWHT = 0;

		foreach ($beans as $bean){
			if ($i==0){
				$firstBean = $bean;
				$preBean = $bean;
				//$preBeanUTCdate = Util::getUTCdate($bean['SDTE']);
				$preBeanUTCdate = $bean['time'];
			}
			//$UTCdate = Util::getUTCdate($bean['SDTE']);
			$UTCdate = $bean['time'];
			$UTCtimeDiff = $UTCdate - $preBeanUTCdate;

			//$tempCum = $bean['KWHT']-$firstBean['KWHT'];
			//($tempCum<1)? $cumPower = $tempCum * 1000: $cumPower = $tempCum;
			$cumPower = round(($bean['KWHT']-$firstBean['KWHT'])*1000,0);


			$avgPower = Formulas::calcAveragePower($bean['KWHT'], $preBean['KWHT'], $UTCtimeDiff,0,0);

			$points[] = array ($UTCdate * 1000,$cumPower,$avgPower,date("H:i, d-m-Y",$bean['time']),);

			$preBeanUTCdate = $bean['time'];
			$preBean = $bean;
			$i++;
		}

		$plantPower = $this->readPlantPower();

		$kWhkWp = number_format(($cumPower/1000) / ($plantPower/1000),2,',','');
		if($cumPower >= 1000){
			$cumPower = number_format($cumPower /=1000,2,',','');
			$cumPowerUnit = "kWh";
		}else{
			$cumPowerUnit = "W";
		}
		$lastDays = new LastDays();
		$lastDays->points=$points;
		$lastDays->KWHT=$cumPower;
		$lastDays->KWHTUnit=$cumPowerUnit;
		$lastDays->KWHKWP=$kWhkWp;
		return $lastDays;
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
		$bean->location = $config->location;
		$bean->latitude = $config->latitude;
		$bean->longitude = $config->longitude;

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

		$bean->co2kwh = $config->co2kwh;
		
		$bean->adminpasswd = $config->adminpasswd;

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
			$config->location = $bean->location;
			$config->latitude = $bean->latitude;
			$config->longitude = $bean->longitude;

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

			$config->co2kwh = ($bean->co2kwh > 0) ? $bean->co2kwh : $config->co2kwh;

			$config->inverters = $this->readInverters();
			
			$config->adminpasswd = ($bean->adminpasswd != "") ? $bean->adminpasswd : $config->adminpasswd;
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
		$bean->name = $inverter->name;
		$bean->description = $inverter->description;
		$bean->initialkwh = $inverter->initialkwh;
		$bean->expectedkwh = $inverter->expectedkwh;
		$bean->heading = $inverter->heading;
		$bean->correctionFactor = $inverter->correctionFactor;
		$bean->comAddress = $inverter->comAddress;
		$bean->comLog = $inverter->comLog;
		$bean->syncTime = $inverter->syncTime;

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
		$inverter->name = $bean->name;
		$inverter->description = $bean->description;
		$inverter->initialkwh = $bean->initialkwh;
		$inverter->expectedkwh = $bean->expectedkwh;
		$inverter->heading = $bean->heading;
		$inverter->correctionFactor = $bean->correctionFactor;
		$inverter->comAddress = $bean->comAddress;
		$inverter->comLog = $bean->comLog;
		$inverter->syncTime = $bean->syncTime;
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
			$inverter->name = $bean->name;
			$inverter->description = $bean->description;
			$inverter->initialkwh = $bean->initialkwh;
			$inverter->expectedkwh = $bean->expectedkwh;
			$inverter->heading = $bean->heading;
			$inverter->correctionFactor = $bean->correctionFactor;
			$inverter->comAddress = $bean->comAddress;
			$inverter->comLog = $bean->comLog;
			$inverter->syncTime = $bean->syncTime;
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
		if (in_array ( strtolower($table), array ("energy","history","pmaxotd"))){
			// get the begin and end date/time
			$beginEndDate = Util::getBeginEndDate($type, $count,$startDate);
			if ($invtnum > 0){
				$energyBeans = R::getAll("SELECT strftime ( '%H:%M:%S %d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date,* FROM '".$table."' WHERE INV = :INV AND time > :beginDate AND  time < :endDate ORDER BY time",array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			}else{
				$energyBeans = R::getAll("SELECT strftime ( '%H:%M:%S %d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date,* FROM '".$table."' WHERE time > :beginDate AND  time < :endDate ORDER BY time",array(':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
			}
		}
		return $energyBeans;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMonthEnergyPerDay($invtnum=0){
		if ($invtnum>0){
			$beans = R::getAll("SELECT INV,MAX ( kwh ) AS kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
		}else{
			$beans = R::getAll("SELECT INV,MAX ( kwh ) AS kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
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
	public function getMonthMaxPowerPerDay($invtnum=0,$startDate){
		$beginEndDate = Util::getBeginEndDate('month', 1,$startDate);
var_dump($beginEndDate);		
		
		
		if ($invtnum>0){
			$beans = R::getAll("
					SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date 
					FROM pMaxOTD WHERE INV = :INV AND  
					time > :beginDate AND time < :endDate GROUP BY strftime ( 'd%-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		}else{
			$beans = R::getAll("
					SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date 
					FROM pMaxOTD 
					time > :beginDate AND time < :endDate GROUP BY strftime ( 'd%-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
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
	public function getYearMaxEnergyPerMonth($invtnum=0){
		if ($invtnum>0){
			$beans = R::getAll("SELECT INV,MAX ( kwh ) AS kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
		}else{
			$beans = R::getAll("SELECT INV,MAX ( kwh ) AS kWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
		}
		return $beans;
	}


	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getYearEnergyPerMonth($invtnum=0,$year = null){
		(!$year) ? $year = date("Y") : $year=$year;
		if ($invtnum>0){
			$beans = R::getAll("SELECT INV,sum(kWh) AS kWh, strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
		}else{
			$beans = R::getAll("SELECT INV,sum(kWh) AS kWh, strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
		}
		return $beans;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMaxMinEnergyYear($invtnum=0){
		(!$year)?$year=date("Y"):$year=$year;
		if ($invtnum>0){
			$beansMax = R::getRow("SELECT INV,max(kWh) as maxkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
			$beansMin = R::getRow("SELECT INV,min(kWh) as minkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
		}else{
			$beansMax = R::getRow("SELECT INV,min(kWh) as minkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
			$beansMin = R::getRow("SELECT INV,max(kWh) as maxkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
		}

		return array("maxEnergy"=>$beansMax,"minEnergy"=>$beansMin);
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getYearMaxPowerPerMonth($invtnum=0){
		if ($invtnum>0){
			$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM pMaxOTD WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
		}else{
			$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM pMaxOTD GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
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
		$beginEndDate = Util::getBeginEndDate('today', 1,$startDate);
		
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
		$beginEndDate = Util::getBeginEndDate('today', 1,$startDate);

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
		$beans = array();
		$whichBeans = array();
		$compareBeans = array();
		$whichMonthDays =  cal_days_in_month(CAL_GREGORIAN, $whichMonth, $whichYear);
		
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
				$whichBeans[$i]['KWH'] = number_format($lastKWH,2,',','');
			}
			
			// get Compare beans
			$beans = $this->readEnergyValues($invtnum, 'month', 1, $compareYear."-".$compareMonth."-1");
			$compareBeans = $beans[0];
			$lastKWH = $compareBeans[count($compareBeans)]['KWH'];
			$compareMonthDays =  cal_days_in_month(CAL_GREGORIAN, $compareMonth, $compareYear);
			for ($i = count($compareBeans)-1; $i < $compareMonthDays; $i++) {
				$iExpectedDay = $i+1;
				$compareBeans[$i]['time'] = strtotime($compareYear."/".$compareMonth."/".$iExpectedDay);
				$compareBeans[$i]['KWH'] = number_format($lastKWH,2,',','');
			}
			// move compareBeans to expectedBeans, so we pass it to JSON.
			$expectedBeans  = $compareBeans;
			$type = "energy vs energy";
		}else{

			$beans = $this->readEnergyValues($invtnum, 'month', 1, $whichYear."-".$whichMonth."-1");
			//var_dump($beans);
			$whichBeans = $beans[0];

			// create
			$lastKWH = $whichBeans[count($whichBeans)]['KWH'];
			for ($i = count($whichBeans); $i < $whichMonthDays; $i++) {
				$iWhichDay = $i+1;
				$whichBeans[$i]['time'] = strtotime(date("Y")."/".$whichMonth."/".$iWhichDay);
				$whichBeans[$i]['KWH'] = number_format($expectedKwhPerDay,2,',','');
			}
			
			$expectedMonthDays =  cal_days_in_month(CAL_GREGORIAN, $compareMonth, date("Y"));
			// create string to get month percentage

			$expectedMonthString = 'expected'.strtoupper(date('M', strtotime($compareMonth."/01/".date("Y"))));

			// get month percentage from config object
			$expectedPerc = $config->inverters[$invtnum]->$expectedMonthString;
			
			//get year expected from config object
			$expectedkwhYear = $config->inverters[$invtnum]->expectedkwh;

			// calculate month kWh = (year/100*month perc)
			$expectedKWhMonth = ($expectedkwhYear / 100)*$expectedPerc;

			// calculate daily expected, based on month day (28,29,30,31 days)
			$expectedKwhPerDay = ($expectedKWhMonth/$expectedMonthDays);

			// create 
			for ($i = 0; $i < $expectedMonthDays; $i++) {
				$iCompareDay = $i+1;
				$expectedBeans[$i]['time'] = strtotime(date("Y")."/".$compareMonth."/".$iCompareDay);
				$expectedBeans[$i]['KWH'] = number_format($expectedKwhPerDay,2,',','');
			}
			$type = "energy vs expected";
		}
		return array(
				"expectedKWhMonth"=>$expectedKWhMonth,
				"expectedkwhYear"=>$expectedkwhYear,
				"expectedPerc"=>$expectedPerc,
				"whichMonthDays"=>$whichMonthDays,
				"compareMonthDays"=>$compareMonthDays,
				"compareBeans"=>$this->beansToGraphPoints($expectedBeans),
				"whichBeans"=>$this->beansToGraphPoints($whichBeans),
				"type"=>$type
				);
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
					$newBean[$i]['KWH'] = number_format(0,0,',','');
					$newBean[$i]['Exp'] = number_format($invExp[$i],0,',','');
					$newBean[$i]['Diff'] = number_format($newBean[$i]['KWH']-$newBean[$i]['Exp'],0,',','');
					if($iMonth > $lastMonth){
						$cumExp += $invExp[$i];
						$newBean[$i]['cumExp']=number_format($cumExp,0,',','');
					}
					$cumKWH += 0;
					$newBean[$i]['cumKWH']=number_format($cumKWH,0,',','');
					$newBean[$i]['what'] = 'prepend';
				}else{
					
					$newBean[$i]['time'] = $beans[$ii]['time'];
					$newBean[$i]['KWH'] =number_format($beans[$ii]['KWH'],0,',','');
					$newBean[$i]['Exp'] =number_format($invExp[$i],0,',','');
					$newBean[$i]['Diff'] = number_format($newBean[$i]['KWH']-$newBean[$i]['Exp'],0,',','');

					$cumExp += $invExp[$i];
					$newBean[$i]['cumExp']=number_format($cumExp,0,',','');

					$cumKWH += $beans[$ii]['KWH'];
					$newBean[$i]['cumKWH']=number_format($cumKWH,0,',','');
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
		$beginEndDate = Util::getBeginEndDate('today', 1,$startDate);
		$beans = R::getAll("SELECT INV, GP,KWHT, strftime ( '%H:%M %d-%m-%Y' , datetime ( time , 'unixepoch' ) ) AS date FROM history WHERE INV = :INV  AND time > :beginDate AND time < :endDate order by time DESC",array(':INV'=>$invtnum,':endDate'=>$beginEndDate['endDate'],':beginDate'=>$beginEndDate['beginDate']));
		return $beans;
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
			$cumPower += $bean['KWH'];
			//echo mktime(0, 0, 0,date("m",$bean['time']),date("d",$bean['time']),date("Y",$bean['time']))."   ";
			$points[] = array (
					mktime(0, 0, 0,date("m",$bean['time']),date("d",$bean['time']),date("Y",$bean['time']))*1000,
					$cumPower,
					date("d-m-Y",$bean['time'])
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
	public function CompareBeansToGraphPoints($beans){
		$points = array();
		$cumPower = 0;
		foreach ($beans as $bean){
			$cumPower += $bean['KWH'];
			$points[] = array (mktime(0, 0, 0,date("m",$bean['time']),1,date("Y",$bean['time']))*1000,
					(float)sprintf("%.2f", $bean['KWH']),
					"1-".date("m-Y",$bean['time']),
					(float)$bean['Exp'],
					(float)$bean['Diff'],
					(float)$bean['cumExp'],
					(float)$bean['cumKWH']
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
					(float)sprintf("%.2f", $bean['KWH']),
					date("Y-m-d",$bean['time']),
					(float)sprintf("%.2f", $cumPower)
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
						(float)sprintf("%.2f", $cumPower)
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
		$config = new Config;
		$energyBeans = $this->readTablesPeriodValues($invtnum, "energy", $type,$startDate);
		$Energy = array();
		foreach ($energyBeans as $energyBean){
			
			$invConfig = $this->readInverter($energyBean['INV']);
			$Energy['INV'] =  $energyBean['INV'];
			$Energy['KWHKWP'] = number_format($energyBean['KWH'] / ($invConfig->plantpower/1000),2,',','');
			$Energy['KWH'] = number_format($energyBean['KWH'],2,',','');
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

		$readMaxPowerValues = $this->getMaxTotalEnergyValues(0,"all",$config);
		$list['summary'] = $readMaxPowerValues;
		return $list;
	}

/**
 *
 */
	public function readPageIndexLiveValues() {
		// summary live data
		$list = array();
		$GP  = 0;
		$I1P = 0;
		$I2P = 0;
		$IP  = 0;
		$EFF = 0;
		$beans = R::findAndExport('inverter');
		$liveBean = array();
		foreach ($beans as $inverter){
			
			$oInverter = 	array();
			
			if(Util::isSunDown($this->config)){
				$live = new Live();
				$live->time = _('sleeping');
				$live->INV = $inverter['id'];
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
				$liveBean =  R::findOne('live',' INV = :INV ', array(':INV'=>$inverter['id']));
	
				$ITP = round($liveBean['I1P'],2)+round($liveBean['I2P'],2);
	
				$live = new Live();
				$live->time = date("H:i:s",$liveBean['time']);
				$live->INV = $liveBean['INV'];
				$live->GP = number_format($liveBean['GP'],0,',','');
				$live->GA = number_format($liveBean['GA'],0,',','');
				$live->GV = number_format($liveBean['GV'],0,',','');
				$live->I1P = number_format($liveBean['I1P'],2,',','');
				$live->I1A = number_format($liveBean['I1A'],2,',','');
				$live->I1V = number_format($liveBean['I1V'],2,',','');
				$live->I1Ratio = number_format($liveBean['I1Ratio'],2,',','');
				$live->I2P = number_format($liveBean['I2P'],2,',','');
				$live->I2A = number_format($liveBean['I2A'],2,',','');
				$live->I2V = number_format($liveBean['I2V'],2,',','');
				$live->I2Ratio = number_format($liveBean['I2Ratio'],2,',','');
				
				$live->IP = number_format($ITP,2,',','');
				$live->EFF = number_format($liveBean['EFF'],2,',','');
			}
			$oInverter["id"] = $liveBean['INV'];
			$oInverter["currentTime"] = time();
			$oInverter["live"] = $live;
			$GP  += $live->GP;
			$I1P += $live->I1P;
			$I2P += $live->I2P;
			$IP  += $live->IP;
			$EFF += $live->EFF;

			$list['inverters'][] = $oInverter;
		}
		
		($GP<1000)? $list['sum']['GP'] = number_format($GP,1,'.','') : $list['sum']['GP'] = number_format($GP,0,'','');
		($I1P<1000)? $list['sum']['I1P'] = number_format($I1P,1,'.','') : $list['sum']['I1P'] = number_format($I1P,0,'','');
		($I2P<1000)? $list['sum']['I2P'] = number_format($I2P,1,'.','') : $list['sum']['I2P'] = number_format($I2P,0,'','');
		($IP<1000)? $list['sum']['IP'] = number_format($IP,1,'.','') : $list['sum']['IP'] = number_format($IP,0,'','');
		($EFF<100)? $list['sum']['EFF'] = number_format($EFF,1,'.','') : $list['sum']['EFF'] = number_format($EFF,0,'','');
		$oInverter = array();
		//$totals = array("day"=>$KWHTD,"week"=>$KWHTW,"month"=>$KWHTM);

		return $list;
	}

	/**
	 * Get Max & (summed)Total Energy Values from Energy Tabel
	 * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
	 */
	public function getMaxTotalEnergyValues($invtnum,$type,$config,$limit=1){

		$type = strtolower($type);
		$avgEnergyBeansToday[] = array();

		if($type == "today" || $type == "day" || $type == "all"){
			$totalEnergyBeansToday = $this->readTablesPeriodValues(0, "energy", "today", date("d-m-Y"));
			$maxPowerBeansToday = $this->readTablesPeriodValues(0, "pMaxOTD", "today", date("d-m-Y"));



			if (count ( $maxPowerBeansToday )==0 ){

				$avgEnergyBeansToday= number_format(0,3,',','');
				$totalEnergyBeansToday[]['KWH']=number_format(0,3,',','');
			}else{

				for ($i = 0; $i < count($maxPowerBeansToday); $i++) {
					$maxPowerBeansToday[$i]['sumkWh'] = number_format($maxPowerBeansToday[$i]['sumkWh'],2,',','');
					$avgEnergyBeansToday= number_format($totalEnergyBeansToday[$i]['KWH'],3,',','');
					$totalEnergyBeansToday[$i]['KWH'] =number_format($totalEnergyBeansToday[$i]['KWH'],3,',','');
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
		}

		foreach ($config->inverters as $inverter){
			$initialkwh += (float)$inverter->initialkwh;
		}
		$tempTotal = 0;
		$tempTotal = $initialkwh + (float)$totalEnergyBeansOverall[0]['sumkWh'] ;
		$totalEnergyOverallTotal = number_format($tempTotal,2,',','');
		
		$energy = array(
				"maxPowerToday"=>$maxPowerBeansToday,
				"totalEnergyToday"=>$totalEnergyBeansToday,
				"avgEnergyToday"=>$avgEnergyBeansToday,

				"totalEnergyWeek"=>$totalEnergyBeansWeek,
				"avgEnergyWeek"=>$avgEnergyBeansWeek,

				"totalEnergyMonth"=>$totalEnergyBeansMonth,
				"avgEnergyMonth"=>$avgEnergyBeansMonth,

				"totalEnergyYear"=>$totalEnergyBeansYear,
				"avgEnergyYear"=>$avgEnergyBeansYear,

				"totalEnergyOverall"=>$totalEnergyBeansOverall,
				"avgEnergyOverall"=>$avgEnergyBeansOverall,
				"initialkwh" => $initialkwh,
				"totalEnergyOverallTotal"=> $totalEnergyOverallTotal
		);
		return $energy;
	}
}