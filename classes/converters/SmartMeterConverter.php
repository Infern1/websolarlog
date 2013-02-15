<?php
class SmartMeterConverter
{
	/**
	 * Converts the result of getData to an SmartMeterLive object
	 * @param string $inputLine
	 * @return Live or null
	 */
	public static function toLiveSmartMeter($inputArray)
	{

		// Check if the input line is valid
		if (count($inputArray) == 0 || !is_array($inputArray)) {
			return null;
		}

		$data = $inputArray;
		// Check if the record is okay
		if (!empty($data[19]) && trim($data[19]) != "!") {
			//echo "\r\ntoLiveSmartMeter: null\r\n";
			return null;
		}
		//echo "\r\nMake LiveSmartMeter()\r\n";
		$live = new LiveSmartMeter();
		//echo "\r\nLiveSmartMeter() Made\r\n";
		if(substr($data[0], 0, 1)=="/" && $data[count($data)-1]=="!"){
			//echo "\r\nValid\r\n";
			$live->Valid = true;
		}else{
			//echo "\r\nNOT Valid\r\n";
			$live->Valid = false;
		}
		
		if($live->Valid){
			//tariff 1 1224kWh low usage 1.8.1
			if(substr($data[3], 0, 9)=="1-0:1.8.1"){
				$live->lowUsage = Util::telegramStringLineToInterUsage($data[3],"kWh");
			}
			//tariff 2 822kWh high usage 1.8.2
			if(substr($data[4], 0, 9)=="1-0:1.8.2"){
				$live->highUsage = Util::telegramStringLineToInterUsage($data[4],"kWh");
			}
			//tariff 1 233kWh low return 2.8.1
			if(substr($data[5], 0, 9)=="1-0:2.8.1"){
				$live->lowReturn = Util::telegramStringLineToInterUsage($data[5],"kWh");
			}
					//tariff 2 571kWh high return 2.8.2
			if(substr($data[6], 0, 9)=="1-0:2.8.2"){
				$live->highReturn = Util::telegramStringLineToInterUsage($data[6],"kWh");
			}
			//tariff 2 571kWh high return 2.8.2
			if(substr($data[7], 0, 11)=="0-0:96.14.0"){
				$live->currentTariff = Util::telegramStringLineToInterUsage($data[7],"");
			}
			// Current usage
			if(substr($data[8], 0, 9)=="1-0:1.7.0"){
				$live->liveUsage = Util::telegramStringLineToInterUsage($data[8],"kW");
			}
			// Current return
			if(substr($data[9], 0, 9)=="1-0:2.7.0"){
				$live->liveReturn = Util::telegramStringLineToInterUsage($data[9],"kW");
			}
			// Current Gas usage
			$data[17] = trim($data[17], "\r"); // Strip off the carriage return given by the ISKRA
			if(substr($data[17], 0, 1)=="(" && substr($data[17], -1)==")"){
				$live->gasUsage = Util::telegramStringLineToInterUsage($data[17],"m3");
			}
			return $live;
		}else{
			//echo "\r\nLive Null\r\n";
			return null;
		}
	}
}
?>