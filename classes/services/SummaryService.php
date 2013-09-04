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

	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Summary
	 */
	public function load($date) { 
		$dataEnergyTables = array();
		//var_dump($this->config->devices);
		foreach($this->config->devices as $device){
			$hookReturn = HookHandler::getInstance()->fire("mainSummary",$device,$date);
			if(count($hookReturn)>0){
				$dataEnergyTables[$device->type][] = (array)$hookReturn;
			}
		}
		$locale  = localeconv();
		$i=0;
		foreach ($dataEnergyTables as $deviceApi){
			$deviceApis = array_keys($dataEnergyTables);
			foreach ($deviceApi as $deviceResult){
				$countValues = count($deviceResult);
				$counter = 0;
				foreach ($deviceResult as $key => $value){
					(!isset($total[$deviceApis[$i]][$key])) ? $total[$deviceApis[$i]][$key] = 0 : $total[$deviceApis[$i]][$key] = $total[$deviceApis[$i]][$key];
					if(is_int($value)){
						$total[$deviceApis[$i]][$key] =($total[$deviceApis[$i]][$key]+$value);
					}else{
						$total[$deviceApis[$i]][$key] =$value;
					}
					
				}
			}
			$i++;
		}
		
		//houseHoldUsage: 1.322
		//return: 2.787
		//usage: 2.702
		//generated: 4.109


		$total['usedBeforeMeterKWH'] = $total['production']['KWH'] - $total['metering']['returnKWH'];
		$total['usedBeforeMeterCosts'] = $locale['currency_symbol']." ".round($total['totalUsagekWh'] / $this->config->co2kwh,2);
		$total['usedBeforeMeterCO2'] = round(($total['usedBeforeMeterKWH']*$this->config->co2kwh)/1000,3);
		$total['usedBeforeMeterTrees'] = round(($total['usedBeforeMeterCO2']*1000)/ $this->config->co2CompensationTree,3);
		

		$total['totalUsagekWh'] =  ($total['production']['KWH'] - $total['metering']['returnKWH']) + $total['metering']['usageKWH'];
		$total['totalUsageKWHCosts'] = round(($total['totalUsagekWh'] * $this->config->costkwh)/100,2);
		$total['totalUsageKWHCO2'] = round(($total['totalUsagekWh']*$this->config->co2kwh)/1000,2);
		$total['totalUsageKWHTrees'] = round(($total['totalUsageKWHCO2']*1000)/ $this->config->co2CompensationTree,2);

		$total['householdCO2'] = $total['totalUsageKWHCO2']+$total['metering']['gasUsageCO2'];
		$total['householdTrees'] = round(($total['totalUsageKWHCO2']+$total['metering']['gasUsageCO2'])/($this->config->co2CompensationTree/1000),0);
		$total['co2CompensationTree'] = $this->config->co2CompensationTree;
		$total['moneySign'] = $locale['currency_symbol'];
		if($total['weather']['degreeDays']>0){
			$total['degreeDays'] = round((18-$total['weather']['degreeTemp']),2);
			$total['m3PerdegreeDays'] = round($total['metering']['gasUsage']/$total['degreeDays'],3);
		}else{
			$total['degreeDays'] = round(0,2);
			$total['m3PerdegreeDays'] = round(0,2);
		}
		$total['costkwh'] = $this->config->costkwh/100;
		$total['costGas'] = $this->config->costGas/100;
		
		
		
		$deviceApis = array_keys($total);
		$i=0;
		foreach ($total as $deviceResult){
			
			$countValues = count($deviceResult);
			$counter = 0;
			if(is_array($deviceResult)){
				//echo 'is_array()';
				foreach ($deviceResult as $key => $value){
					if(stristr(strtolower($key), 'co2')){
						$total[$deviceApis[$i]][$key] = round($value,2);
					}
					if(stristr(strtolower($key), 'costs')){
						$total[$deviceApis[$i]][$key] = round($value,2);
					}
					if(stristr(strtolower($key), 'kwh')){
						$total[$deviceApis[$i]][$key] = round($value,2);
					}
				}
			}else{
				if(stristr(strtolower($key), 'co2')){
					$total[$deviceApis[$i]][$key] = round($value,2);
				}
				if(stristr(strtolower($key), 'costs')){
					$total[$deviceApis[$i]][$key] = round($value,2);
				}
				if(stristr(strtolower($key), 'kwh')){
					$total[$deviceApis[$i]][$key] = round($value,2);
				}
			}
			$i++;
		}
		//var_dump($total);
		return array("data"=>$dataEnergyTables,"totals"=>$total);
	}
	
}
?>