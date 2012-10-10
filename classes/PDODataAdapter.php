<?php
class PDODataAdapter {
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

        $plantPower=0;
        $oPanels = $this->readPanelsByInverter($invtnum);
        
        foreach($oPanels as $panel){
        	$plantPower += ($panel->amount*$panel->wp);
        }
        
        
        $bean->SDTE = date("Ymd-H:i:s");
        $bean->time = time();
        $bean->INV = $invtnum;
        
        $bean->I1V = $live->I1V;
        $bean->I1A = $live->I1A;
        $bean->I1P = $live->I1P;
        $bean->I1Ratio = round((($live->I1P/$plantPower)*100),1);

        $bean->I2V = $live->I2V;
        $bean->I2A = $live->I2A;
        $bean->I2P = $live->I2P;
        $bean->I2Ratio = round((($live->I2P/$plantPower)*100),1);

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
    	$bean = R::findAll(
    			'event',
    			' ORDER BY ID LIMIT :limit ',
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

    public function dropDailyData($invtnum) {
    	// TODO :: ??
    }

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
    		$cumPower = round($bean['KWHT']-$firstBean['KWHT'] *1,3);//cumalative power this day
    		$avgPower = Formulas::calcAveragePower($bean['KWHT'], $preBean['KWHT'], $UTCtimeDiff,0,2);

    		$points[] = array ($UTCdate * 1000,$cumPower,$avgPower,date("d-m-Y",$bean['time']),);

    		$preBeanUTCdate = $bean['time'];
    		$preBean = $bean;
    		$KWHT = $i."".$bean['KWHT']."*".$i."".$firstBean['KWHT'];
    		$i++;
    	}


    	$lastDays = new LastDays();
    	$lastDays->points=$points;
    	$lastDays->KWHT=$cumPower;
    	return $lastDays;
    }
    

    public function writeConfig(Config $config) {
        // Only save the object self not the arrays
        $bean = R::findOne('config');

        if (!$bean){
            $bean = R::dispense('config');
        }

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

        $bean->co2kwh = $config->co2kwh;

        //Store the bean
        R::store($bean);
    }

    public function readConfig() {
        $bean = R::findOne('config');

        $config = new Config();

        if ($bean) {
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

            $config->template = $bean->template;
            $config->aurorapath = ($bean->aurorapath != "") ? $bean->aurorapath : $config->aurorapath;

            $config->co2kwh = ($bean->co2kwh > 0) ? $bean->co2kwh : $config->co2kwh;

            $config->inverters = $this->readInverters();
        }

        return $config;
    }

    public function writeInverter(Inverter $inverter) {
        // Only save the object self not the arrays
        $bean = R::load('inverter',$inverter->id);

        if (!$bean){
            $bean = R::dispense('inverter');
        }

        $bean->name = $inverter->name;
        $bean->description = $inverter->description;
        $bean->initialkwh = $inverter->initialkwh;
        $bean->expectedkwh = $inverter->expectedkwh;
        $bean->heading = $inverter->heading;
        $bean->correctionFactor = $inverter->correctionFactor;
        $bean->comAddress = $inverter->comAddress;
        $bean->comLog = $inverter->comLog;

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

    public function readInverter($id) {
        $bean = R::load('inverter',$id);

        $inverter = new Inverter();
        $inverter->id = $bean->id;
        $inverter->name = $bean->name;
        $inverter->description = $bean->description;
        $inverter->initialkwh = $bean->initialkwh;
        $inverter->expectedkwh = $bean->expectedkwh;
        $inverter->heading = $bean->heading;
        $inverter->correctionFactor = $bean->correctionFactor;
        $inverter->comAddress = $bean->comAddress;
        $inverter->comLog = $bean->comLog;
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

    private function readInverters() {
        $list = array();
        foreach(R::find('inverter') as $bean) {
            $inverter = new Inverter();
            $inverter->id = $bean->id;
            $inverter->name = $bean->name;
            $inverter->description = $bean->description;
            $inverter->initialkwh = $bean->initialkwh;
            $inverter->expectedkwh = $bean->expectedkwh;
            $inverter->heading = $bean->heading;
            $inverter->correctionFactor = $bean->correctionFactor;
            $inverter->comAddress = $bean->comAddress;
            $inverter->comLog = $bean->comLog;
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
    public function getMonthMaxPowerPerDay($invtnum=0){
    	if ($invtnum>0){
    		$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM pMaxOTD WHERE INV = :INV GROUP BY strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
    	}else{
    		$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM pMaxOTD GROUP BY strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
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
    public function getYearEnergyPerMonth($invtnum=0){
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
    public function getYearMaxPowerPerMonth($invtnum=0){
    	if ($invtnum>0){
    		$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM pMaxOTD WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC",array(':INV'=>$invtnum));
    	}else{
    		$beans = R::getAll("SELECT INV,MAX(GP) AS maxGP, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM pMaxOTD GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC");
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
    	return $beans;
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
    	$type=strtolower($type);

    	(stristr($type, 'day') === FALSE) ?	$table = "energy" : $table = "history";

    	$beans = $this->readTablesPeriodValues($invtnum, $table, $type, $startDate);

    	if(strtolower($table) == "history"){
    		// NO history bean? Create a dummy bean...
    		(!$beans) ? $beans[0] = array('time'=>time(),'KWH'=>0,'KWHT'=>0) : $beans = $beans;
    		return $this->DayBeansToGraphPoints($beans);
    	}else{
    		return $this->PeriodBeansToGraphPoints($beans);
    	}
    }

    /**
     * return a array that can be understand by JQplot
     * @param array $beans from $this->getGraphPoint()
     * @return array($beginDate, $endDate);
     */
   public function PeriodBeansToGraphPoints($beans){
    	$points = array();

    	foreach ($beans as $bean){
    		$cumPower += $bean['KWH'];
    		$points[] = array (mktime(0, 0, 0,date("m",$bean['time']),date("d",$bean['time']),date("Y",$bean['time']))*1000,
    					(float)sprintf("%.2f", $bean['KWH']),
    					date("d-m-Y",$bean['time']),
	    				(float)sprintf("%.2f", $cumPower)
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
    public function readEnergyValues($invtnum, $type, $count, $startDate,$maxType){
    	$config = new Config;
		$energyBeans = $this->readTablesPeriodValues($invtnum, "energy", $type,$startDate);
		
		foreach ($energyBeans as $energyBean){
			$oEnergy = new Energy();
    		$invConfig = $this->readInverter($energyBean['INV']);
    		
    		
    		$oEnergy->INV =  $energyBean['INV'];
    		$oEnergy->KWHKWP = number_format($energyBean['KWH'] / ($invConfig->plantpower/1000),2,',','');


    		$oEnergy->KWH = number_format($energyBean['KWH'],2,',','');
    		$oEnergy->CO2 =Formulas::CO2kWh($energyBean['KWH'],$config->co2kwh);
    		$oEnergy->time = date("H:i:s d-m-Y",$energyBean['time']);
    		$oEnergy->KWHT = number_format($energyBean['KWHT'],2,',','');
    		$KWHT += $energyBean['KWH'];
    		$energy[] = $oEnergy;
    		$days[] = array("kwh"=>$energyBean['KWH'], "time"=>date("d-m-Y",$energyBean['time']));
		}		
    	return array($energy,$KWHT);
    }
    
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
    
    public function readPageIndexData() {
    	// summary live data
    	$list = array();

    	$readMaxPowerValues = $this->getMaxTotalEnergyValues(0,"all");
    	$list['summary'] = $readMaxPowerValues;
    	return $list;
    }
    
    
    
    public function readPageIndexLiveValues() {
    	// summary live data
    	$list = array();
    
    	// Initialize variables
    	$GP=0;
    	$I1P = 0;
    	$I2P = 0;
    	$IP  = 0;
    	$EFF = 0;
    
    	$beans = R::findAndExport('inverter');
    	foreach ($beans as $inverter){
    		$oInverter = array();
    
    		$liveBean =  R::findOne('live',' INV = :INV ', array(':INV'=>$inverter['id']));
    		
    		$ITP = round($liveBean['I1P'],2)+round($liveBean['I2P'],2);
    
    		$live = new Live();
    		$live->Time = date("H:i:s",$liveBean['time']);
    		$live->INV = $liveBean['INV'];
    		$live->GP = number_format($liveBean['GP'],0,',','');
    		
    
    		$live->I1P = number_format($liveBean['I1P'],2,',','');
    		$live->I1Ratio = number_format($liveBean['I1Ratio'],2,',','');
    		$live->I2P = number_format($liveBean['I2P'],2,',','');
    		$live->I2Ratio = number_format($liveBean['I2Ratio'],2,',','');
    
    		$live->IP = number_format($ITP,2,',','');
    		$live->EFF = number_format($liveBean['EFF'],2,',','');
    		//$list['inverters'][$liveBean['INV']]['live'] = $live;
    		$oInverter["id"] = $liveBean['INV'];
    		$oInverter["live"] = $live;
    		$GP  += $live->GP;
    		$I1P += $live->I1P;
    		$I2P += $live->I2P;
    		$IP  += $live->IP;
    		$EFF  += $live->EFF;
    
    		$list['inverters'][] = $oInverter;
    	}
    	
    	$list['sum']['GP'] = number_format($GP,0,',','');
    	$list['sum']['I1P'] = number_format($I1P,0,',','');
    	$list['sum']['I2P'] = number_format($I2P,0,',','');
    	$list['sum']['IP'] = number_format($IP,0,',','');
    	$list['sum']['EFF'] = number_format($EFF/count($oInverter),0,',','');
    
    	$oInverter = array();
    	//$totals = array("day"=>$KWHTD,"week"=>$KWHTW,"month"=>$KWHTM);

    	return $list;
    }
    
    /**
     * Get Max & (summed)Total Energy Values from Energy Tabel
     * Return a Array() with MaxEnergyDay, MaxEnergyMonth, MaxEnergyYear, MaxEnergyOverall
     */
    public function getMaxTotalEnergyValues($invtnum,$type,$limit=1){
    
    	$type = strtolower($type);
    
    	if($type == "today" || $type == "day" || $type == "all"){
    		$MaxEnergyBeansToday = $this->readTablesPeriodValues(0, "energy", "today", date("d-m-Y"));
    		$MaxPowerBeansToday = $this->readTablesPeriodValues(0, "pMaxOTD", "today", date("d-m-Y"));
    	}
    	
    	if($type == "week" || $type == "all"){
    		$MaxEnergyBeansWeek = R::getAll("SELECT MAX ( kwh ) AS kWh, SUM (kwh) AS sumkWh, strftime ( '%Y%W' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY date ORDER BY time DESC limit 0,:limit",array(':limit'=>$limit));
    	}
    	if($type == "month" ||  $type == "all"){
    		if ($invtnum>0){
    			$MaxEnergyBeansMonth = R::getAll("
    					SELECT INV,MAX ( kwh ) AS kWh, SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit,':INV'=>$invtnum));
    		}else{
    			$MaxEnergyBeansMonth = R::getAll("
    					SELECT INV,MAX ( kwh ) AS kWh, SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%m-%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit));
    		}
    	}
    	if($type == "year" || $type == "all"){
    		if ($invtnum>0){
    			$MaxEnergyBeansYear = R::getAll("SELECT MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh,strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit,':INV'=>$invtnum));
    		}else{
    			$MaxEnergyBeansYear = R::getAll("SELECT MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh,strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy GROUP BY strftime ( '%Y' , date ( time , 'unixepoch' ) ) order by time DESC limit 0,:limit",array(':limit'=>$limit));
    		}
    	}
    	if($type == "overall" || $type == "all"){
    		if ($invtnum>0){
    			$MaxEnergyBeansOverall = R::getAll("SELECT MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy WHERE INV = :INV order by time limit 0,:limit",array(':limit'=>$limit,':INV'=>$invtnum));
    		}else{
    			$MaxEnergyBeansOverall = R::getAll("SELECT MAX ( kwh )  AS kWh,  SUM (kwh) AS sumkWh, strftime ( '%d-%m-%Y' , date ( time , 'unixepoch' ) ) AS date FROM energy order by time limit 0,:limit",array(':limit'=>$limit));
    		}
    	}
    	$MaxEnergy = array(
    			"MaxPowerToday"=>$MaxPowerBeansToday,
    			"MaxEnergyToday"=>$MaxEnergyBeansToday,
    			"MaxEnergyWeek"=>$MaxEnergyBeansWeek,
    			"MaxEnergyMonth"=>$MaxEnergyBeansMonth,
    			"MaxEnergyYear"=>$MaxEnergyBeansYear,
    			"MaxEnergyOverall"=>$MaxEnergyBeansOverall);
    	return $MaxEnergy;
    }
    
    
}