<?php
class PDODataAdapter implements DataAdapter {
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
    public function addEnergy($invtnum, MaxPowerToday $energy, $year = null) {
    	$bean = R::dispense('Energy');
    	 
    	$bean->INV = $invtnum;
    	$bean->SDTE = $energy->SDTE;
    	$bean->KWHT = $energy->GP;
    	
    	$id = R::store($bean);
    }

    /**
     * add the events to the events
     * @param int $invtnum
     * @param Event $event
     */
    public function addEvent($invtnum, Event $Oevent) {
    	$bean = R::dispense('Event');
    	
    	$bean->INV = $invtnum;
    	$bean->SDTE = $event->SDTE;
    	$bean->Event = $event->event;
    	$bean->Type = $event->type;
    	$id = R::store($bean);
    	
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
    
    
    /**
     * Drop Lock from DB
     * @param int $invtnum
     * @return bean object
     */
    public function dropLock() {
    	$bean =  R::find('Lock',
    			' Type = :type  ',
    			array(':type'=>'LockPort'
    			)
    	);
    	(count($bean)>0) ? R::trashAll( $bean ) : R::trash( $bean );
    	
    }
    
    
    /**
     * Write the Lock to DB
     * @param int $invtnum
     * @return bean object
     */
    public function writeLock(Lock $lock) {
    	$bean = R::dispense('Lock');

    	$bean->SDTE = $lock->SDTE;
    	$bean->Type = $lock->type;
    
    	//Store the bean
    	$id = R::store($bean);
    
    	return $bean;
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
    	$points = $this->beansToDataArray($bean);
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
    	$points = $this->beansToDataArray($bean);
    	$lastDays = new LastDays();
    	$lastDays->points=$points[0];
    	return $lastDays;

    }
    
    
    public function dropLastDaysData($invtnum) {
    	// TODO :: ??
    }
    
    
    public function beansToDataArray($beans){
    	foreach ($beans as $bean){
    		$UTCdate = Util::getUTCdate($bean['SDTE']) * 1000;
    		$KWHT = $bean['KWHT']*1;
    		$points[] = array ($UTCdate,$KWHT);
    	}
    	return array($points,$KWHT);
    }    

    
}