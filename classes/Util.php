<?php
class Util {

    public static function isSunDown() {
        $now = strtotime(date("Ymd H:i"));
        $sun_info = date_sun_info((strtotime(date("Ymd"))), $LATITUDE, $LONGITUDE);
        return $now<($sun_info['sunrise']-300) || $now>($sun_info['sunset']+300);
    }

    public static function getDataDir($invtnum) {
    	$path = dirname(dirname(__FILE__));
    	//$path = "/home/marco/www/pvlog";
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
        return unlink(self::getDataLockFile());
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
}
?>