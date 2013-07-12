<?php
class KostalPikoConverter
{

    /**
     * Converts the result of getData to an Live object
     * @param string $inputLine
     * @return Live or null
     */
    public static function toLive($inputLine)
    {
        // Check if the input line is valid
        if ($inputLine == null || trim($inputLine) == "") {
        	HookHandler::getInstance()->fire("onError", "Piko.py returned NULL/Nothing/Empty");
            return null;
        }

        // Split on a serie of spaces (not one)
        $dataLines = preg_split("/[\s]+/",$inputLine);

        foreach ($dataLines as $line){
        	$data[] = preg_split("/[,]+/",$line);
        }
        // Check if the record is okay
        if (!empty($data[22]) && trim($data[22]) != "OK") {
        	HookHandler::getInstance()->fire("onError", "Unexpected response from Piko.py:\r\n".print_r($inputLine,true));
            return null;
        }
        
        $live = new Live();
        $live->type = 'production';
        if (!empty ($data[1][1])) {
        	$line = explode('.',$data[1][1]);
        	preg_match('/(\d{4})\-(\d{2})\-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})/i', $line[0], $matches,0);
        	$result =  Util::getTimestampOfDate($matches[4],$matches[5],$matches[6],$matches[3],$matches[2],$matches[1]);
            $live->time = $result;
        }
        if (count($data[6] == 6)) {
            $live->I1V = $data[6][1];
            $live->I1A = $data[6][2];
            $live->I1P = $data[6][3];
        }
        if (count($data[7] == 6)) {
            $live->I2V = $data[7][1];
            $live->I2A = $data[7][2];
            $live->I2P = $data[7][3];
        }
        /**
         *** Third string fix
         */
        if (count($data[8] == 6)) {
        	$live->I3V = $data[8][1];
        	$live->I3A = $data[8][2];
        	$live->I3P = $data[8][3];
        }
        
        
        if (count($data[9] == 6)) {
            $live->GV = $data[9][1];
            $live->GA = $data[9][2];
            $live->GP = $data[9][3];
        }
        /**
         *** 3phase fix
         */
        if (count($data[10] == 6)) {
        	$live->GV2 = $data[10][1];
        	$live->GA2 = $data[10][2];
        	$live->GP2 = $data[10][3];
        }
        if (count($data[11] == 6)) {
        	$live->GV3 = $data[11][1];
        	$live->GA3 = $data[11][2];
        	$live->GP3 = $data[11][3];
        }
        
        $live->BOOT = 0;
        $live->INVT = 0;
        /**
         *** /3phase fix
         */
         
        // not available
        $live->FRQ = 0;
        
        $live->IP = $live->I1P + $live->I2P + $live->I3P;
  		$live->ACP = $live->GP + $live->GP2 + $live->GP3;

        if($live->IP > 0 AND $live->ACP > 0){
        	$live->EFF = (float)round(($live->ACP/$live->IP)*100,4);
        }

        if (!empty ($data[4][1])) {
            $live->KWHT = $data[4][1]/1000;
        }
        
        // This line is only valid if GP and KWHT are filled with data
        if (empty($live->KWHT) || empty($live->ACP)) {
        	HookHandler::getInstance()->fire("onError", "Piko.py didn't return KWHT or GP is empty! We need these values for calculations");
        	return null;
        }
        
        return $live;
    }
    
    public static function toDeviceHistory($line) {
		// Split the line based on multiple spaces between two values
    	$parts = preg_split('/\s+/', $line);
    	if (count($parts) < 2) {
    		return null;
    	}

    	// Create the object
    	$deviceHistory = new DeviceHistory();
    	$deviceHistory->amount = (double) $parts[1];
    	$deviceHistory->time = ($parts[0] != "") ? strtotime($parts[0]) : 0;
    	
    	// Only return if we have an valid time
    	if ($deviceHistory->time > 0) {
    		return $deviceHistory;
    	}
    	return null;
    }
}