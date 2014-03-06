<?php
class SummaryService {
	public static $tbl = "history";
	
	
	function __construct() {
		$this->config = Session::getConfig();
	}
	
	/**
	 * Save the object to the database
	 * @param Summary $object
	 * @return Summary
	 
	public function save(Summary $object) {
		$bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
		$bObject = $this->toBean($object, $bObject);
		$object->id = R::store($bObject);
		return $object;
	}
	*/

	
	public static function testen($value,$key){
		echo $value;
	}
	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Summary
	 */
	public function load($date) {
		
		$dataEnergyTables = array();
		foreach($this->config->devices as $device){
			$hookReturn = HookHandler::getInstance()->fire("mainSummary",$device,$date);
			if(count($hookReturn)>0){
				$dataEnergyTables[$device->type][] = (array)$hookReturn;
			}
		}

		$i=0;
		foreach ($dataEnergyTables as $deviceApi){
			$deviceApis = array_keys($dataEnergyTables);
			foreach ($deviceApi as $deviceResult){
				$countValues = count($deviceResult);
				$counter = 0;
				foreach ($deviceResult as $key => $value){
					
					if(is_array($value)){
						foreach ($value as $arrayKey => $arrayValue){
							$arrayValue = (float)$arrayValue;
							(!isset($total[$deviceApis[$i]][$arrayKey])) ? $total[$deviceApis[$i]][$arrayKey] = 0 : $total[$deviceApis[$i]][$arrayKey] = (float)$total[$deviceApis[$i]][$arrayKey];
							if(is_float	($arrayValue)){
								$total[$deviceApis[$i]][$arrayKey] =((float)$total[$deviceApis[$i]][$arrayKey]+(float)$arrayValue);
							}else{
								$total[$deviceApis[$i]][$arrayKey] =(float)$arrayValue;
							}							
						}
					}else{
						$value = (float)$value;
						(!isset($total[$deviceApis[$i]][$key])) ? $total[$deviceApis[$i]][$key] = 0 : $total[$deviceApis[$i]][$key] = (float)$total[$deviceApis[$i]][$key];
						if(is_float	($value)){
							$total[$deviceApis[$i]][$key] =((float)$total[$deviceApis[$i]][$key]+(float)$value);
						}else{
							$total[$deviceApis[$i]][$key] =(float)$value;
						}
					}
				}
			}
			$i++;
		}
		



		/*
		echo (float)$total['production']['KWH']."*";
		echo (float)$total['metering']['returnKWH']."*";
		
		echo floatval($total['production']['KWH']) - (floatval($total['metering']['returnKWH']) + floatval($total['metering']['usageKWH']))."*";
		*/
		
		
		//houseHoldUsage:
		
		//return: 3
		//usage: 4
		//generated: 5
		
		// householdUsage = (generated - return) + usage;
		// we generated 5 kWh, of that we return 3 kWh to the grid, so we used 2 kWh in the house. Also the smartMeter registered a usage of 4 kWh.
		// So the house used 2 kWh + 4 kWh = 6kWh
		// 30 - 28 = 2
		// 
		/*
		echo $total['production']['KWH']." ";
		echo $total['metering']['returnKWH']." ";
		echo $total['metering']['usageKWH']." ";
		*/
		if(array_key_exists('metering',$total)){
			$total['totalUsagekWh'] =  $total['production']['KWH'] - $total['metering']['returnKWH'] + $total['metering']['usageKWH'];
			$total['totalUsageKWHCosts'] = ($total['production']['KWH'] - $total['metering']['returnKWH'] + $total['metering']['usageKWH']) * $this->config->costkwh/100;
			$total['totalUsageKWHCO2'] = round(($total['totalUsagekWh']*$this->config->co2kwh)/1000,2);
			$total['totalUsageKWHTrees'] = round(($total['totalUsageKWHCO2']*1000)/ $this->config->co2CompensationTree,2);
			
			//echo $total['totalUsagekWh'];
			//echo $this->config->co2kwh;
			$total['usedBeforeMeterKWH'] = $total['production']['KWH'] - $total['metering']['returnKWH'];
			$total['usedBeforeMeterCosts'] = floatval($total['totalUsagekWh']) / $this->config->co2kwh;
			$total['usedBeforeMeterCO2'] = round(($total['usedBeforeMeterKWH']*$this->config->co2kwh)/1000,3);
			$total['usedBeforeMeterTrees'] = round(($total['usedBeforeMeterCO2']*1000)/ $this->config->co2CompensationTree,3);
			
			$total['householdCO2'] = $total['totalUsageKWHCO2']+$total['metering']['gasUsageCO2'];
			$total['householdUsage'] = $total['metering']['usageKWH'];
			$total['householdCosts'] = $total['metering']['usageKWH'] * $this->config->costkwh/100;
			$total['householdTrees'] = round(($total['totalUsageKWHCO2']+$total['metering']['gasUsageCO2'])/($this->config->co2CompensationTree/1000),0);
			
			if($total['householdTrees']>=0){
				$lang['subscriptTrees'] = _('needed');
				$total['householdTrees'] = abs($total['householdTrees']);
			}else{
				$lang['subscriptTrees'] = _('compensated');
			}
		}
		
		$total['co2CompensationTree'] = $this->config->co2CompensationTree;
		if($total['weather']['degreeDays']>0 and is_array($total['weather'])){
			$total['degreeDays'] = round(($total['weather']['degreeDays']),2);
			if(array_key_exists('metering',$total)){
				$total['m3PerdegreeDays'] = round($total['metering']['gasUsage']/$total['degreeDays'],3);
			}else{
				$total['m3PerdegreeDays'] = round(0,2);
			}
		}else{
			$total['degreeDays'] = round(0,2);
			$total['m3PerdegreeDays'] = round(0,2);
		}
		$total['costkwh'] = $this->config->costkwh/100;
		$total['costGas'] = $this->config->costGas/100;
		$total['sunDown'] = Util::isSunDown();
		

		//$total['costsTest'] = numfmt_format($fmt, "5,5");
		
		
		$lang['usage'] = _('usage');
		$lang['used'] = _('used');
		$lang['gas'] = _('gas');
		$lang['harvested'] = _('harvested');
		$lang['generated'] = _('generated');
		$lang['trees'] = _('trees');
		$lang['power'] = _('power');
		$lang['weather'] = _('weather');
		

		// timezone offset.
		$dtz = new DateTimeZone($this->config->timezone);
		$timezone = new DateTime('now', $dtz);
		$timezoneOffset = $dtz->getOffset( $timezone )/3600;
			
		array_walk_recursive ( $dataEnergyTables , "self::handle");
		array_walk_recursive ( $total , "self::handle");
		return array("data"=>$dataEnergyTables,"total"=>$total,"lang"=>$lang,"timezoneOffset" => $timezoneOffset);
	}
	
	public function handle(&$value,$key){
		if(stristr(strtolower($key), 'co2')){
			$value = round($value,2);
		}
		if(stristr(strtolower($key), 'costs')){
			$value = money_format('%(#1n', $value);
			//$value = $value;
		}
		if(stristr(strtolower($key), 'kwh') && !stristr(strtolower($key), 'costs')){
			$value = round($value,3);
		}
		if(stristr(strtolower($key), 'tree')){
			$value = round($value,2);
		}
	}
}
?>