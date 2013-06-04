<?php
class Util {

	/**
	 * Checks if the sun is down
	 * @param number $correction
	 * @return boolean
	 */
    public static function isSunDown($correction=300) {
        $sun_info = date_sun_info(time(), session::getConfig()->latitude , session::getConfig()->longitude);
        return time()<($sun_info['sunrise']+$correction) || time()>($sun_info['sunset']-$correction);
    }
    
    public static function getSunInfo($config,$startDate) {
    	
    	$startDate= strtotime($startDate);
    	
    	if($startDate == null){
    		$startDate = strtotime(date("Ymd"));
    	}else{
    		$startDate = strtotime(date("Y",$startDate)."".date("m",$startDate)."".date("d",$startDate));
    	}

    	return date_sun_info($startDate, $config->latitude , $config->longitude);
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
    	$hour = substr($text, 9, 2);
    	$minute = substr($text, 12, 2);
    	$seconde = substr($text, 15, 2);
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


    public static function checkIfModuleLoaded($module) {
    	if  (in_array  ($module, get_loaded_extensions())) {
			$status = 'Loaded';
    	}else{
    		$status = '-NOT- loaded';
    	}
    	return array('name'=>$module,'status'=>$status);
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
    
    public static function makeEventsReadable($events){
    	foreach ($events as $event){
    		$order   = array("\r\n", "\n", "\r");
    		$replace = ' ';
    		// Processes \r\n's first so they aren't converted twice.
    		$eventText= str_replace($order, $replace, $event['Event']);
    			
    		$eventText = preg_split('/(Alarm)/', $eventText, -1, PREG_SPLIT_DELIM_CAPTURE);
    			
    		if(count($eventText)>1){
    			$eventText = $eventText[1].' '.substr($eventText[2],4,strlen($eventText[2]));
    		}else{
    			$eventText = trim($eventText[0]);
    		}
    		$event['time'] = date('d-m-Y H:i:s',$event['time']);
    		$event['Event'] = $eventText;
    		$newEvents[] = $event;
    	}
    	return $newEvents;
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
}
?>