<?php
class Util {

	/**
	 * Checks if the sun is down
	 * @param number $correction
	 * @return boolean
	 */
    public static function isSunDown($correction=300) {
    	if (Session::getConfig()->isValidCoords()) {
	        $sun_info = date_sun_info(time(), Session::getConfig()->latitude , Session::getConfig()->longitude);
	        return time()<($sun_info['sunrise']+$correction) || time()>($sun_info['sunset']-$correction);
    	}
		return false;    	
    }
    
    public static function getSunInfo($config,$startDate) {
    	if (Session::getConfig()->isValidCoords()) {
	    	$startDate= strtotime($startDate);
	    	
	    	if($startDate == null){
	    		$startDate = strtotime(date("Ymd"));
	    	}else{
	    		$startDate = strtotime(date("Y",$startDate)."".date("m",$startDate)."".date("d",$startDate));
	    	}
	
	    	return date_sun_info($startDate, $config->latitude , $config->longitude);
    	}
    	// Return nothing
    }
    
    public static function getDataLockFile() {
    	return dirname(dirname(__FILE__))."/data/lock";
    }

    public static function createLockFile() {
        return touch(self::getDataLockFile());
    }

    public static function removeLockFile() {
        if (file_exists(self::getDataLockFile())) {
            return unlink(self::getDataLockFile());
        }
    }

    public static function getUTCdate($text) {
    	$year = substr($text, 0, 4);
    	$month = substr($text, 4, 2);
    	$day = substr($text, 6, 2);
    	$hour = 0;
    	$minute = 0;
    	$seconde = 1;
    	if (strlen($text) > 8) {
	    	$hour = substr($text, 9, 2);
	    	$minute = substr($text, 12, 2);
	    	$seconde = substr($text, 15, 2);
    	}
    	return strtotime ($year."-".$month."-".$day." ".$hour.":".$minute.":".$seconde);
    }

	/**
	 * get First Day Of Month
	 * @param unknown $day
	 * @param unknown $month
	 * @param unknown $year
	 */
    public static function getFirstDayOfMonth($day,$month,$year) {
    	return strtotime(date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)));
    }

   /**
    * 
    * @param number $hour
    * @param number $minute
    * @param number $second
    * @param unknown $day
    * @param unknown $month
    * @param unknown $year
    */
    public static function getTimestampOfDate($hour=0,$minute=0,$second=0,$day,$month,$year) {
    	return mktime($hour,$minute,$second, $month, $day, $year);
    }

    /**
     * get Last Day Of Month
     * @param int $invtnum
     * @param int $limit
     * @return bean object
     */
    public static function getLastDayOfMonth($day,$month,$year) {
    	return strtotime(date('Y-m-t', mktime(0, 0, 0, $month, 1, $year)));
    }

	/**
 	* Add timestamp 
 	* @param int $ts 
 	*/
    public static function getStartAndEndOfWeek($ts) {

    	$start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
    	return array(strtotime(date('Y-m-d', $start)),strtotime(date('Y-m-d', strtotime('next sunday', $start))));
    }


    public static function formatEvent($event){
		$find = array(" W", " E");
		$replace = array(" Warning ", " Error ");
		$event = str_ireplace($find, $replace, $event);
    	return $event;
    }


    public static function checkIfModuleLoaded($module,$type) {
    	if  (in_array  ($module, get_loaded_extensions())) {
			$status = true;
    	}else{
    		$status = false;
    	}
    	return array('name'=>$module,'status'=>$status,'type'=>$type);
    }

    /**
     * return the begin and end date for a given period for a given date.
     * @param date $startDate ("d-m-Y") ("31-12-1900"), when no date given, the date of today is used.
     * @param str $type options are: (to)day, yesterday,week,month,year
     * @param int $count multiplies the day's,weeks,months,year
     * @return array($beginDate, $endDate);
     */
    public static function getBeginEndDate($type, $count,$startDate=null){
    	if(!$startDate){
    		$startDate = date("d-m-Y");
    	}
    	// Make de StartDate a timestamp
    	
    	$startDate = strtotime($startDate);


	    	switch (strtolower($type)) {
	    		case 'today':
	    		case 'day':
	    			$beginDate = Util::getTimestampOfDate(0,0,0,date("d",$startDate), date("m",$startDate), date("Y",$startDate));
	    			$endDate = Util::getTimestampOfDate(23,59,59,date("d",$startDate), date("m",$startDate), date("Y",$startDate));
	    			break;
	    		case 'yesterday':
	    			$beginDate = Util::getTimestampOfDate(0,0,0,date("d",time()-86400), date("m",time()-86400), date("Y",time()-86400));
	    			$endDate = Util::getTimestampOfDate(23,59,59,date("d",time()-86400), date("m",time()-86400), date("Y",time()-86400));
	    			break;
	    		case 'week':
	    			$beginEndDate = Util::getStartAndEndOfWeek($startDate);
	    			$beginDate = $beginEndDate[0];
	    			$endDate = $beginEndDate[1];
	    			break;
	    		case 'month':
	    			$beginDate = Util::getTimestampOfDate(0,0,0, 1, date("m",$startDate), date("Y",$startDate));
	    			$endDate = Util::getTimestampOfDate(23,59,59,date("t",$startDate), date("m",$startDate), date("Y",$startDate));
	    			break;
	    		case 'year':
	    			$beginDate = Util::getTimestampOfDate(0,0,0, 1,1, date("Y",$startDate))-3600; // -3600 = correction daylightsavingtime;
	    			$endDate = Util::getTimestampOfDate(23,59,59,31,12, date("Y",$startDate))-3600; // -3600 = correction daylightsavingtime;
	    			break;
	    		default:
	    			echo "ERROR::UTIL::getBeginEndDate()::WRONG Type >> Choose from today,week,month,year";
	    			break;
	    	}
    	
    	return array("beginDate"=>$beginDate,"endDate"=>$endDate);
    }

    public static function formatPower($value,$decimals){
    	return ($value>1000) ? round(($value/1000),$decimals)." kWh": round($value,$decimals)." W";
    }
      
    public static function serverUptime(){
    	$ut = strtok( file_get_contents('/proc/uptime'), "." );
    	if($ut){
	    	$days = sprintf( "%2d", ($ut/(3600*24)) );
	    	$hours = sprintf( "%2d", ( ($ut % (3600*24)) / 3600) );
	    	$min = sprintf( "%2d", ($ut % (3600*24) % 3600)/60  );
	    	$sec = sprintf( "%2d", ($ut % (3600*24) % 3600)%60  );
    	}else{
    		$days = 0;
	    	$hours = 0;
	    	$min = 0;
	    	$sec = 0;
    	}
    	return array( 'day'=>$days, 'hour'=>$hours, 'min'=>$min, 'sec'=>$sec );
    }
    
    public static function timeBetweenRange($startDate, $endDate, $todayDate){
    	$startTimestamp = strtotime($startDate);
    	$endTimestamp = strtotime($endDate);
    	$todayTimestamp = strtotime($todayDate);

    	return (($todayTimestamp >= $startTimestamp) && ($todayTimestamp <= $endTimestamp));
    }
    
    
    public static function getCurrentUrl()
    {
    	if( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 )
    		|| 	isset( $_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
    	){
    		$protocol = 'https://';
    	} else {
    		$protocol = 'http://';
    	}
    
    	$url = $protocol . (isset($_SERVER['HTTP_HOST']) === true ? $_SERVER['HTTP_HOST'] : "www.suncounter.nl");
    
    	// use port if non default
    	$url .= isset( $_SERVER['SERVER_PORT'] )
    		&&( ($protocol === 'http://' && $_SERVER['SERVER_PORT'] != 80) || ($protocol === 'https://' && $_SERVER['SERVER_PORT'] != 443) )
    		? ':' . $_SERVER['SERVER_PORT']
    		: '';
    	$url .= $_SERVER['PHP_SELF'];
    
    	// return current url
    	return $url;
    }

    /**
     * Retrieves the languages selected in the browser
     * @return Array of languages
     */
    public static function getBrowserDefaultLanguage() {
    	if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    		// Probably run from server/worker script
    		return null;
    	}
    	
    	$languages = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    	
    	// Strip off the ; part
    	$cleanLanguages = array();
    	foreach ($languages as $language) {
    		$tmpLanguage = explode(";", $language);
    		$cleanLanguages[] = $tmpLanguage[0];
    	}
    	return $cleanLanguages;
    }
    
    public static function telegramStringLineToInterUsage($string,$input){
    	$pattern = "/\((.*)\)/";
    	preg_match_all($pattern,$string,$match);
    	$value="";
    	if($input=="kWh"){
    		$value = str_replace("*kWh","",str_replace(".","",$match[1]));
    		$value = ltrim($value[0],0);
    	}
    	if($input=="kW"){
    		$value = str_replace("*kW","",str_replace(".","",$match[1]));
    		$value = $value[0]."0";
    	}
    	if($input=="m3"){
    		$value = str_replace("m3","",str_replace(".","",$match[1]));
    		$value = ltrim($value[0],0);
    	}
    
    	return $value." ";
    }
    
    /**
     * Creates an job on a fix time every day
     * @param $hour
     * @param $minute
     * @return time
     */
    public static function createOnceADayJob($hour, $minute){
    	$today_text = date("Y-m-d ", time()) . $hour . ":" . $minute . ":00";
    	$today = strtotime($today_text);
    	$tomorrow = strtotime($today_text . ' +24 hour');
    	if ($today > time()) {
    		return $today;
    	}
    	return $tomorrow;
    }
    
    /**
     * Creates an time based on the first coming interval
     * @param unknown $interval
     * @return number
     */
    public static function createTimeForWholeInterval($interval) {
    	return (time() + $interval) - (time() % $interval);
    }
    
    public static function file_perms($file, $octal = false)
    {
    	if(!file_exists($file)) return false;
    	$fileExists = true;
    	$perms = fileperms($file);
    
    	$cut = $octal ? 2 : 3;
    
    	
    	if (($perms & 0xC000) == 0xC000) {
    		// Socket
    		$info = 's';
    	} elseif (($perms & 0xA000) == 0xA000) {
    		// Symbolic Link
    		$info = 'l';
    	} elseif (($perms & 0x8000) == 0x8000) {
    		// Regular
    		$info = '-';
    	} elseif (($perms & 0x6000) == 0x6000) {
    		// Block special
    		$info = 'b';
    	} elseif (($perms & 0x4000) == 0x4000) {
    		// Directory
    		$info = 'd';
    	} elseif (($perms & 0x2000) == 0x2000) {
    		// Character special
    		$info = 'c';
    	} elseif (($perms & 0x1000) == 0x1000) {
    		// FIFO pipe
    		$info = 'p';
    	} else {
    		// Unknown
    		$info = 'u';
    	}
    	$owner = array();
    	$group = array();
    	$world = array();
    	$rightsDigits = array();
    	
    	$rightsDigits = str_split(substr(decoct($perms), $cut));
    	
    	// Owner
    	$owner[] = $rightsDigits[0];
    	$owner[] = (($perms & 0x0100) ? array('r','read') : array('-','-'));
    	$owner[] = (($perms & 0x0080) ? array('w','write') : array('-','-'));
    	$owner[] = (($perms & 0x0040) ? (($perms & 0x0800) ? array('s','s') : array('x','execute') ) : (($perms & 0x0800) ? array('S','S') : array('-','-')));
    	
    	// Group
    	$group[] = $rightsDigits[1];
    	$group[] = (($perms & 0x0020) ? array('r','read') : array('-','-'));
    	$group[] = (($perms & 0x0010) ? array('w','write') : array('-','-'));
    	$group[] = (($perms & 0x0008) ? (($perms & 0x0400) ? array('s','s') : array('x','execute') ) : (($perms & 0x0400) ? array('S','S') : array('-','-')));
    	
    	// World
    	$world[] = $rightsDigits[2];
    	$world[] = (($perms & 0x0004) ? array('r','read') : array('-','-'));
    	$world[] = (($perms & 0x0002) ? array('w','write') : array('-','-'));
    	$world[] = (($perms & 0x0001) ? (($perms & 0x0200) ? array('t','t') : array('x','execute') ) : (($perms & 0x0200) ? array('T','T') : array('-','-')));
    	
    	
    	
    	$fileMinRW = ($rightsDigits[0]>=6) ? true : false;
    	
    	return array(
    			'fileExists'=>$fileExists,
    			'rights'=> array('owner'=>$owner,'group'=>$group,'world'=>$world), 
    			'RWXNumber'=>$rightsDigits,
    			'fileMinRW'=>$fileMinRW);
    }
    
    public static function createErrorMessage (Exception $exception) {
    	$msg = $exception->getMessage() . " in file " . $exception->getFile() . '[' . $exception->getLine() . ']';
    	return array('result'=>'error', 'success'=>false, 'exception'=>get_class($exception), 'message'=>$msg);
    }
    
    /**
     * 
     * @param unknown $array
     * @param unknown $key
     */
    
    public static function aasort (&$array, $key) {
			$sorter=array();
			$ret=array();
			reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii]=$va[$key];
			}
			asort($sorter);
			foreach ($sorter as $ii => $va) {
				$ret[$ii]=$array[$ii];
			}
			$array=$ret;
		}
}
?>