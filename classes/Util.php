<?php
class Util {

    public static function isSunDown($config, $correction=300) {
        $now = strtotime(date("Ymd H:i"));
        $sun_info = date_sun_info((strtotime(date("Ymd"))), $config->latitude , $config->longitude);
        return $now<($sun_info['sunrise']-$correction) || $now>($sun_info['sunset']+$correction);
    }

    public static function getDataDir($invtnum) {
    	$path = dirname(dirname(__FILE__));
        return $path."/data/invt$invtnum";
    }

    public static function getLiveTXT($invtnum)  {
    	return self::getDataDir($invtnum) . '/infos/live.txt';
    }

    public static function getDailyDataCSV($date,$invtnum)  {
    	return self::getDataDir($invtnum) . '/csv/'.$date.'.csv';
    }

    public static function getLastDaysCSV($year,$invtnum)  {
    	return self::getDataDir($invtnum) . '/production/energy'.$year.'.csv';
    }

    public static function getErrorFile($invtnum) {
        return self::getDataDir($invtnum) . '/errors/de.err';
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
     * @param int $invtnum
     * @param int $limit
     * @return bean object
     */
    public static function getFirstDayOfMonth($day,$month,$year) {
    	return strtotime(date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)));
    }

    /**
     * get Timestamp Of Date
     * @param int $invtnum
     * @param int $limit
     * @return bean object
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


    function getStartAndEndOfWeek($date) {
    	$ts = strtotime($date);
    	$start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
    	return array(strtotime(date('Y-m-d', $start)),strtotime(date('Y-m-d', strtotime('next sunday', $start))));
    }


    public static function formatEvent($event){
		$find = array(" W ", " E ");
		$replace = array("Warning ", "Error ");
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
     * @param date $startDate ("Y-m-d") ("1900-12-31"), when no date given, the date of today is used.
     * @param str $type options are: (to)day, yesterday,week,month,year
     * @param int $count multiplies the day's,weeks,months,year
     * @return array($beginDate, $endDate);
     */
    public function getBeginEndDate($type, $count,$startDate=null){
    	if(!$startDate){
    		$startDate = date("Y-m-d");
    	}

    	// Make de StartDate a timestamp
    	$startDate = strtotime($startDate);

    	// check what we must return
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
    			$endDate = Util::getTimestampOfDate(23,59,59,31, date("m",$startDate), date("Y",$startDate));
    			break;
    		case 'year':
    			$beginDate = Util::getTimestampOfDate(0,0,0, 1,1, date("Y",$startDate))-3600; // -3600 = correction daylightsavingtime;
    			$endDate = Util::getTimestampOfDate(23,59,59,31,12, date("Y",$startDate))-3600; // -3600 = correction daylightsavingtime;
    			break;
    		case 'lastday':
    			/*
    			 * TODO
    			 */
    			break;
    		case 'lastweek':
    			/*
    			 * TODO
    			 */
    			break;
    		case 'lastmonth':
    			/*
    			 * TODO
    			 */
    			break;
    		case 'lastyear':
    			/*
    			 * TODO
    			 */
    			break;
    		default:
    			echo "ERROR::UTIL::getBeginEndDate()::WRONG Type >> Choose from today,week,month,year";
    			break;
    	}
    	return array("beginDate"=>$beginDate,"endDate"=>$endDate);
    }

    public function formatPower($value,$decimals){
    	return ($value>1000) ? round(($value/1000),$decimals)." kWh": round($value,$decimals)." W";
    }
}
?>