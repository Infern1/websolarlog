<?php
class PDODataAdapter {
    function __construct() {
        $config = new Config;
    	R::setup('sqlite:'.$config->dbHost );
    	R::debug(false);
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

    	$bean =  R::findOne('Live',' INV = :INV ', array(':INV'=>$invtnum));

        if (!$bean){
        	$bean = R::dispense('Live');
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
    	$bean =  R::findOne('Live',' INV = :INV ', array(':INV'=>$invtnum));

        $live = new Live();
        $live->INV = $bean->INV;
        $live->I1V = $bean->I1V;
        $live->I1A = $bean->I1A;
        $live->I1P = $bean->I1P;

        $live->I2V = $bean->I2V;
        $live->I2A = $bean->I2A;
        $live->I2P = $bean->I2P;

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
    	$bean =  R::findOne('Live',
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
    	$bean =  R::findOne('Pmaxotd',
    				' INV = :INV AND SDTE LIKE :date ',
    				array(':INV'=>$invtnum,
    					':date'=> '%'.date('Ymd').'%'
    				)
    			);

    	if (!$bean){
    		$bean = R::dispense('Pmaxotd');
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
    	$bean =  R::findOne('Pmaxotd',
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
    	$bean =  R::findOne('Pmaxotd',
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
    	$bean = R::dispense('History');

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
    				'History',
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
    	$bean =  R::find('History',
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
    	$bean = R::dispense('Energy');

    	$bean->INV = $invtnum;
    	$bean->SDTE = $energy->SDTE;
    	$bean->time = $energy->time;
    	$bean->KWH = $energy->KWH;
    	$bean->KWHT = $energy->KWHT;

    	$id = R::store($bean);
    }

    /**
     * write the max power today to the file
     * @param int $invtnum
     * @param MaxPowerToday $mpt
     */
    public function addEnergy($invtnum, Energy $energy) {
        $bean =  R::findOne('Energy',
                ' INV = :INV AND SDTE LIKE :date ',
                array(':INV'=>$invtnum,
                        ':date'=> '%'.date('Ymd').'%'
                )
        );

        $oldKWH = 0;
        if (!$bean){
            $bean = R::dispense('Energy');
        } else {
            $oldKWH = $bean->KWH;
        }
        $bean->INV = $invtnum;
        $bean->SDTE = $energy->SDTE;
        $bean->time = $energy->time;
        $bean->KWH = $energy->KWH;
        $bean->KWHT = $energy->KWHT;

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
        $bean = R::dispense('Energy');
        $bean->INV = $invtnum;
        $bean->SDTE = $energy->SDTE;
        $bean->time = $energy->time;
        $bean->KWH = $energy->KWH;
        $bean->KWHT = $energy->KWHT;
        $id = R::store($bean,$bean->id);
        return $id;
    }

    /**
     * add the events to the events
     * @param int $invtnum
     * @param Event $event
     */
    public function addEvent($invtnum, Event $event) {
    	$bean = R::dispense('Event');

    	$bean->INV = $invtnum;
    	$bean->SDTE = $event->SDTE;
    	$bean->time = $event->time;
    	$bean->Event = $event->event;
    	$bean->Type = $event->type;
    	$bean->alarmSend = $event->alarmSend;
    	$id = R::store($bean);

    }
    /**
     * read the Energy of today
     * @param int $invtnum
     * @return MaxPowerToday
     */
    public function readEnergyDay($invtnum,$date) {
    	$date = ($date == 0) ? time(): $date;
    	
    	$bean =  R::findOne('Energy',
    			' INV = :INV AND Time LIKE :date ',
    			array(':INV'=>$invtnum,
    					':date'=> '%'.$date.'%'
    			)
    	);
    	return $bean;
    }
    
    /**
     * Read the events file
     * @param int $invtnum
     * @return bean object
     */
    public function readEvent($invtnum,$limit=10) {
    	$bean = R::findAll(
    			'Event',
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
    	$bean =  R::findOne('Event',
    			' INV = :INV ',
    			array(':INV'=>$invtnum
    			)
    	);
    	R::trash( $bean );
    }


    /**
     * Drop Inverter Info from DB
     * @param int $invtnum
     * @return bean object
     */
    public function dropInverterInfo($invtnum) {
    	$bean =  R::findOne('InverterInfo',
    			' INV = :INV  ',
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
    				'History',
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
    				'Energy',
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


    public function DayBeansToDataArray($beans){
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

    		$points[] = array ($UTCdate * 1000,$cumPower,$avgPower);

    		$preBeanUTCdate = $bean['time'];
    		$preBean = $bean;
    		$KWHT = round($bean['KWHT'] - $firstBean['KWHT'],3);
    		$i++;
    	}
    	
    	
    	$lastDays = new LastDays();
    	$lastDays->points=$points;
    	$lastDays->KWHT=$cumPower;
    	$lastDays->table=$table;
    	return $lastDays;
    }


    public function writeConfig(Config $config) {
        // Only save the object self not the arrays
        $bean = R::findOne('Config');

        if (!$bean){
            $bean = R::dispense('Config');
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
        $bean = R::findOne('Config');

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
        $bean = R::load('Inverter',$inverter->id);

        if (!$bean){
            $bean = R::dispense('Inverter');
        }

        $bean->name = $inverter->name;
        $bean->description = $inverter->description;
        $bean->initialkwh = $inverter->initialkwh;
        $bean->expectedkwh = $inverter->expectedkwh;
        $bean->plantpower = $inverter->plantpower;
        $bean->heading = $inverter->heading;
        $bean->correctionFactor = $inverter->correctionFactor;
        $bean->comAddress = $inverter->comAddress;
        $bean->comLog = $inverter->comLog;

        //Store the bean
        R::store($bean);
    }

    public function readInverter($id) {
        $bean = R::load('Inverter',$id);

        $inverter = new Inverter();
        $inverter->id = $bean->id;
        $inverter->name = $bean->name;
        $inverter->description = $bean->description;
        $inverter->initialkwh = $bean->initialkwh;
        $inverter->expectedkwh = $bean->expectedkwh;
        $inverter->plantpower = $bean->plantpower;
        $inverter->heading = $bean->heading;
        $inverter->correctionFactor = $bean->correctionFactor;
        $inverter->comAddress = $bean->comAddress;
        $inverter->comLog = $bean->comLog;
        $inverter->panels = $this->readPanelsByInverter($inverter->id);

        return $inverter;
    }

    private function readInverters() {
        $list = array();
        foreach(R::find('Inverter') as $bean) {
            $inverter = new Inverter();
            $inverter->id = $bean->id;
            $inverter->name = $bean->name;
            $inverter->description = $bean->description;
            $inverter->initialkwh = $bean->initialkwh;
            $inverter->expectedkwh = $bean->expectedkwh;
            $inverter->plantpower = $bean->plantpower;
            $inverter->heading = $bean->heading;
            $inverter->correctionFactor = $bean->correctionFactor;
            $inverter->comAddress = $bean->comAddress;
            $inverter->comLog = $bean->comLog;
            $inverter->panels = $this->readPanelsByInverter($inverter->id);
            $list[] = $inverter;
        }

        return $list;
    }

    public function writePanel(Panel $panel) {
        // Only save the object self not the arrays
        $bean = R::load('Panel', $panel->id);

        if (!$bean){
            $bean = R::dispense('Panel');
        }

        $bean->inverterId = $panel->inverterId;
        $bean->description = $panel->description;
        $bean->roofOrientation = $panel->roofOrientation;
        $bean->roofPitch = $panel->roofPitch;

        //Store the bean
        R::store($bean);
    }

    public function readPanel($id) {
        $bean = R::load('Panel', $id);

        $panel = new Panel();
        $panel->id = $bean->id;
        $panel->inverterId = $bean->inverterId;
        $panel->description = $bean->description;
        $panel->roofOrientation = $bean->roofOrientation;
        $panel->roofPitch = $bean->roofPitch;
        return $panel;
    }

    private function readPanelsByInverter($inverterId) {
        $list = array();
        $beans = R::find('Panel',' inverterId = :id ', array( ":id"=>$inverterId ));
        foreach ($beans as $bean){
            $panel = new Panel();
            $panel->id = $bean->id;
            $panel->inverterId = $bean->inverterId;
            $panel->description = $bean->description;
            $panel->roofOrientation = $bean->roofOrientation;
            $panel->roofPitch = $bean->roofPitch;
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
    public function readTablePeriodValues($invtnum, $table, $type, $startDate){
    	$count = 0;
    	
    	if (in_array ( $table, array ("Energy","History","Pmaxotd"))){
    		
	    	$beginEndDate = Util::getBeginEndDate($type, $startDate,$count);
	    	
    		if ($invtnum > 0){
    			echo $invtnum." ".$type." ".$table." ".$startDate."....";
	    		$energyBeans = R::getAll("SELECT * FROM '".$table."' WHERE inv = :INV AND time > :beginDate AND  time < :endDate ",array(':INV'=>$invtnum,':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
    		}else{
    			echo $invtnum." ".$type." ".$table." ".$startDate."....";
	    		$energyBeans = R::getAll("SELECT * FROM '".$table."' WHERE time > :beginDate AND  time < :endDate ",array(':beginDate'=>$beginEndDate['beginDate'],':endDate'=>$beginEndDate['endDate']));
    		}
    	}
    	return $energyBeans;
    }
    

    /**
     * return a array with GraphPoints for 
     * @param date $startDate ("Y-m-d") ("1900-12-31"), when no date given, the date of today is used.
     * @return array($beginDate, $endDate);
     */
    public function getGraphPoint($invtnum,$type, $startDate){
    	
    	$type=strtolower($type);
    	
    	(stristr($type, 'day') === FALSE) ?	$table = "Energy" : $table = "History";
    	
    	$beans = $this->readTablePeriodValues($invtnum, $table, $type, $startDate);
    	
    	if(strtolower($table) == "history"){
    		// NO history bean? Create a dummy bean...
    		(!$beans) ? $beans[0] = array('time'=>time(),'KWH'=>0) : $beans = $beans;
    		return $this->DayBeansToDataArray($beans);
    	}else{
    		return $this->PeriodBeansToDataArray($beans);
    	}
    }
    
    
   public function PeriodBeansToDataArray($beans){
    	$points = array();
    	
    	foreach ($beans as $bean){
    		$cumPower = $cumPower +$bean['KWH'];
    		$points[] = array (
    					mktime(0, 0, 0, 
    							date("m",$bean['time']), 
    							date("d",$bean['time']), 
    							date("Y",$bean['time']))*1000,
    					(float)sprintf("%.2f", $bean['KWH']),
    					date("m-d-Y",$bean['time']),
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
    
    
    
    public function readMaxPowerValues($invtnum, $type, $count, $startDate){
    
    	$PmaxotdBeans = $this->readTablePeriodValues($invtnum, "Pmaxotd", $type,$startDate);

    	$oMaxPowerToday = new MaxPowerToday();
    	$oMaxPowerToday->GP = $PmaxotdBeans[0]['GP'];
    	$oMaxPowerToday->INV =$PmaxotdBeans[0]['INV'];
    	$oMaxPowerToday->time = date("H:i:s d-m-Y",$PmaxotdBeans[0]['time']);
    	return $oMaxPowerToday;
    }
    
    
    public function readEnergyValues($invtnum, $type, $count, $startDate){
    	
		$energyBeans = $this->readTablePeriodValues($invtnum, "Energy", $type,$startDate);
		foreach ($energyBeans as $energyBean){
    		$oEnergy = new Energy();
    		$oEnergy->INV =$energyBean[0]['INV'];
    		$oEnergy->time = date("H:i:s d-m-Y",$energyBean[0]['time']);
    		$oEnergy->KWH =$energyBean[0]['KWH'];
    		$oEnergy->KWHT=$energyBean[0]['KWHT'];
		}
    	return $oEnergy;
    }
    
    public function readPageIndexData() {
    	// summary live data
    	$list = array();

    	// Initialize variables
    	$KWHTD = 0;
    	$KWHTW = 0;
    	$KWHTM = 0;


    	$beans = R::findAndExport('Inverter');
    	foreach ($beans as $inverter){
            $oInverter = array();
    		$liveBean =  R::findOne('Live',' INV = :INV ', array(':INV'=>$inverter['id']));
    		$ITP = round($liveBean['I1P'],2)+round($liveBean['I2P'],2);
    		$live = array(
    				array("field"=>"time", "value"=>date("H:i:s",$liveBean['time'])),
    				array("field"=>"INV", "value"=>$liveBean['INV']),
    				array("field"=>"GP", "value"=>round($liveBean['GP'],2)),
    				array("field"=>"I1P", "value"=>round($liveBean['I1P'],2)),
    				array("field"=>"I2P", "value"=>round($liveBean['I2P'],2)),
    				array("field"=>"ITP", "value"=>$ITP)
    		);
    		$oInverter["live"] = $live;
    		// get production
    		$beans = R::getAll("SELECT INV,KWH FROM 'Energy' WHERE INV = :inv ORDER BY SDTE DESC ,INV DESC LIMIT 0,:limit", array(':limit'=>30,':inv'=>$inverter['id']));
    		$i=0;
        	// Initialize variables
    		$KWHT = array();
        	$KWHT['dayKWHT'] = 0;
        	$KWHT['weekKWHT'] = 0;
        	$KWHT['monthKWHT'] = 0;
    		foreach ($beans as $bean){
    			if ($i<(1)){
    				$KWHT['dayKWHT'] = $KWHT['dayKWHT']+$bean['KWH'];
    			}
    			if ($i<(7)){
    				$KWHT['weekKWHT'] = $KWHT['weekKWHT']+$bean['KWH'];
    			}
    			if ($i<(30)){
    				$KWHT['monthKWHT'] = $KWHT['monthKWHT']+$bean['KWH'];
    			}
    			$i++;
    		}
    		$oInverter["day"] = $KWHT['dayKWHT'];
    		$oInverter["week"] = $KWHT['weekKWHT'];
    		$oInverter["month"] = $KWHT['monthKWHT'];
    		$list['inverters'][] = $oInverter;

    		$KWHTD = $KWHTD  + $KWHT['dayKWHT'];
    		$KWHTW = $KWHTW  + $KWHT['weekKWHT'];
    		$KWHTM = $KWHTM  + $KWHT['monthKWHT'];
    	}
    	$oInverter = array();
    	$totals = array("day"=>$KWHTD,"week"=>$KWHTW,"month"=>$KWHTM);

    	$list['summary'] = $totals;
    	return $list;
    }
}