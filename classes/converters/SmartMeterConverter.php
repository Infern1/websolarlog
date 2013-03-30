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
		
		// Remove empty lines from the result
		//$data = array();
		//foreach ($inputArray as &$line) {
		//	if (!empty($line)) {
		//		$data[] = $line;
		//	}
		//}

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
			
			$dataSplit = $data;
			foreach ($dataSplit as $data) {
				//tariff 1 1224kWh low usage 1.8.1
				if(substr($data, 0, 9)=="1-0:1.8.1"){
					$live->lowUsage = Util::telegramStringLineToInterUsage($data,"kWh");
				}
				//tariff 2 822kWh high usage 1.8.2
				if(substr($data, 0, 9)=="1-0:1.8.2"){
					$live->highUsage = Util::telegramStringLineToInterUsage($data,"kWh");
				}
				//tariff 1 233kWh low return 2.8.1
				if(substr($data, 0, 9)=="1-0:2.8.1"){
					$live->lowReturn = Util::telegramStringLineToInterUsage($data,"kWh");
				}
						//tariff 2 571kWh high return 2.8.2
				if(substr($data, 0, 9)=="1-0:2.8.2"){
					$live->highReturn = Util::telegramStringLineToInterUsage($data,"kWh");
				}
				//tariff 2 571kWh high return 2.8.2
				if(substr($data, 0, 11)=="0-0:96.14.0"){
					$live->currentTariff = Util::telegramStringLineToInterUsage($data,"");
				}
				// Current usage
				if(substr($data, 0, 9)=="1-0:1.7.0"){
					$live->liveUsage = Util::telegramStringLineToInterUsage($data,"kW");
				}
				// Current return
				if(substr($data, 0, 9)=="1-0:2.7.0"){
					$live->liveReturn = Util::telegramStringLineToInterUsage($data,"kW");
				}
				// Current Gas usage
				$data = trim($data, "\r"); // Strip off the carriage return given by the ISKRA
				if(substr($data, 0, 1)=="(" && substr($data, -1)==")"){
					$live->gasUsage = Util::telegramStringLineToInterUsage($data,"m3");
				}
				return $live;
			}
		}else{
			//echo "\r\nLive Null\r\n";
			return null;
		}
	}
}
?>