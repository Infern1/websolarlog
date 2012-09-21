<?php

/**
 * Timezone class
 *
 * @category  ..
 * @package   ..
 */
class Timezone{
	
	/**
	 * Return a select list with all available Timezone with there current time. 
	 *
	 * @param string  $timezone Timezone 'Europe\London'
	 * @return string
	 */
	function getTimeZones($timezone=""){
		static $regions = array(
		    'Africa' => DateTimeZone::AFRICA,
		    'America' => DateTimeZone::AMERICA,
		    'Antarctica' => DateTimeZone::ANTARCTICA,
		    'Asia' => DateTimeZone::ASIA,
		    'Atlantic' => DateTimeZone::ATLANTIC,
		    'Europe' => DateTimeZone::EUROPE,
		    'Indian' => DateTimeZone::INDIAN,
		    'Pacific' => DateTimeZone::PACIFIC
		);
		
		foreach ($regions as $name => $mask) {
		    $tzlist[] = DateTimeZone::listIdentifiers($mask);
		
		}
		
		echo '<select id="selectTimezone">';
		foreach ($tzlist as $name => $mask) {
			foreach ($mask as $name => $masks) {
				$date = new DateTime();
				$date->setTimezone(new DateTimeZone($masks));
				
				(strtolower($masks) == strtolower($timezone)) ? $select_tz = "SELECTED" : $select_tz="";
		
				echo '<option value="'.$masks.'" '.$select_tz.'>'.$masks.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('.$date->format('d-m H:i:s').')</option>';
			}
		}
		echo '</select>';
	}

	/*
 	* Get current timezone...

	if (date_default_timezone_get()) {
	   // echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
	}
	
	if (ini_get('date.timezone')) {
	   // echo 'date.timezone: ' . ini_get('date.timezone');
	}


 	* end Get current timezone
 	*/

}

?>