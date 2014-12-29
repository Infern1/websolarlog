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
		$lines = array();
		foreach ($inputArray as &$line) {
			if (!empty($line)) {
				$lines[] = $line;
			}
		}

		// Check if the record is okay
		$firstLine = reset($lines);
		$lastLine = end($lines);
		
		// Check if we hava an valid fist line
		if (empty($firstLine) || substr($firstLine, 0, 1) != "/") {
			throw new ConverterException("SmartMeter :: Invalid first line:\r\n".print_r($lines,true));
		}

		// Check if we have an valid last line
		if (empty($lastLine) || substr($lastLine, 0, 1) != "!") {
			throw new ConverterException("SmartMeter :: Invalid last line:\r\n".print_r($lines,true));
		}

		// Create LiveSmartMeter object
		$live = new LiveSmartMeter();

		// Go through all lines to fetch the data
                $counter = 0;
                
		foreach ($lines as $line) {
			$data = trim($line);
			$lineType = explode("(", $data)[0];
			
			switch ($lineType) {
				case "1-0:1.8.1": //tariff 1 1224kWh low usage 1.8.1
					$live->lowUsage = Util::telegramStringLineToInterUsage($data,"kWh");
					break;
				case "1-0:1.8.2": //tariff 2 822kWh high usage 1.8.2
					$live->highUsage = Util::telegramStringLineToInterUsage($data,"kWh");
					break;
				case "1-0:2.8.1": //tariff 1 233kWh low return 2.8.1
					$live->lowReturn = Util::telegramStringLineToInterUsage($data,"kWh");
					break;
				case "1-0:2.8.2": //tariff 2 571kWh high return 2.8.2
					$live->highReturn = Util::telegramStringLineToInterUsage($data,"kWh");
					break;
				case "0-0:96.14.0":
					$live->currentTariff = Util::telegramStringLineToInterUsage($data,"");
					break;
				case "1-0:1.7.0": // Current usage
					$live->liveUsage = Util::telegramStringLineToInterUsage($data,"kW");
					break;
				case "1-0:2.7.0": // Current return
					$live->liveReturn = Util::telegramStringLineToInterUsage($data,"kW");
					break;
                                    
                                case "0-1:24.2.1": // DMSR 4.0 Gas usage
                                        $live->gasUsage = Util::telegramStringLineToInterUsage($data,"m3DSMR40");
					break;
                                case "0-1:24.3.0":
                                        $element = $counter+1; //DSMR 2.0 gas is on the next line
                                        $data = $lines[$element];
                                        $live->gasUsage = Util::telegramStringLineToInterUsage($data,"m3DSMR20");
					break;
			}
                    $counter++;
		}
		
		return $live;
	}
}
?>