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
		


		$total['usedBeforeMeterKWH'] = $total['production']['KWH'] - $total['metering']['returnKWH'];
		$total['usedBeforeMeterCosts'] = $locale['currency_symbol']." ".round($total['totalUsagekWh'] / $this->config->co2kwh,2);
		$total['usedBeforeMeterCO2'] = round(($total['usedBeforeMeterKWH']*$this->config->co2kwh)/1000,3);
		$total['usedBeforeMeterTrees'] = round(($total['usedBeforeMeterCO2']*1000)/ $this->config->co2CompensationTree,3);
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
		$total['totalUsagekWh'] =  (floatval($total['production']['KWH']) - floatval($total['metering']['returnKWH'])) + floatval($total['metering']['usageKWH']);
		$total['totalUsageKWHCosts'] = round(($total['totalUsagekWh'] * $this->config->costkwh)/100,2);
		$total['totalUsageKWHCO2'] = round(($total['totalUsagekWh']*$this->config->co2kwh)/1000,2);
		$total['totalUsageKWHTrees'] = round(($total['totalUsageKWHCO2']*1000)/ $this->config->co2CompensationTree,2);

		$total['householdCO2'] = $total['totalUsageKWHCO2']+$total['metering']['gasUsageCO2'];
		$total['householdUsage'] = $total['metering']['usageKWH'];
		$total['householdTrees'] = round(($total['totalUsageKWHCO2']+$total['metering']['gasUsageCO2'])/($this->config->co2CompensationTree/1000),0);
		
		if($total['householdTrees']>=0){
			$lang['subscriptTrees'] = _('needed');
			$total['householdTrees'] = abs($total['householdTrees']);
		}else{
			$lang['subscriptTrees'] = _('compensated');
		}
		
		
		$total['co2CompensationTree'] = $this->config->co2CompensationTree;
		$total['moneySign'] = $this->config->moneySign;
		if($total['weather']['degreeDays']>0){
			$total['degreeDays'] = round((18-$total['weather']['degreeTemp']),2);
			$total['m3PerdegreeDays'] = round($total['metering']['gasUsage']/$total['degreeDays'],3);
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
		
		
		$dataEnergyTables = self::handleFigures($dataEnergyTables);
		$total = self::handleFigures($total);
		
		return array("data"=>$dataEnergyTables,"totals"=>$total,"lang"=>$lang);
	}
	
	public function handleFigures($total){
		$i = 0;
		$deviceApis = array_keys($total);
		foreach ($total as $deviceResult){
			$countValues = count($deviceResult);
			$counter = 0;
			if(is_array($deviceResult)){
				foreach ($deviceResult as $key => $value){
					if(is_array($value)){ 
						$backupKey = $key;
						foreach ($value as $key => $value){
							if(stristr(strtolower($key), 'co2')){
								$total[$deviceApis[$i]][$backupKey][$key] = round($value,2);
							}
							if(stristr(strtolower($key), 'costs')){
								$total[$deviceApis[$i]][$backupKey][$key] = round($value,2);
							}
							if(stristr(strtolower($key), 'kwh')){
								$total[$deviceApis[$i]][$backupKey][$key] = round($value,2);
							}
							if(stristr(strtolower($key), 'tree')){
								$total[$deviceApis[$i]][$backupKey][$key] = round($value,2);
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
						if(stristr(strtolower($key), 'tree')){
							$total[$deviceApis[$i]][$key] = round($value,2);
						}
					}
				}
			}else{
				if(stristr(strtolower($key), 'co2')){
					$total[$key] = round($value,2);
				}
				if(stristr(strtolower($key), 'costs')){
					$total[$key] = round($value,2);
				}
				if(stristr(strtolower($key), 'kwh')){
					$total[$deviceApis[$i]][$key] = round($value,2);
				}
				if(stristr(strtolower($key), 'tree')){
					$total[$deviceApis[$i]][$key] = round($value,2);
				}
			}
			$i++;
		}
		return $total;
	}
}
?>