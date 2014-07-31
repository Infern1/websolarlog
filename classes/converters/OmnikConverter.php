<?php
class OmnikConverter {

    /**
     * Converts the result of getData to an Live object
     * @param string $inputLine
     * @return Live or null
     */
    public static function toLive($inputLine)
    {
        // Check if the input line is valid
        if ($inputLine == null || trim($inputLine) == "") {
        	return null;
        }


        // Comma seperated
        $data = explode(",", $inputLine);

        // Id,Temp,VPV1,VPV2,VPV3,IPV1,IPV2,IPV3,IAC1,IAC2,IAC3,VAC1,VAC2,VAC3,FAC1,PAC1,FAC2,PAC2,FAC3,PAC3,ETODAY,ETOTAL,HTOTAL
        // NLDN3020137X5092,35.9,170.9,161.7,-1,0.6,0.6,-1,0.8,-1,-1,238.1,-1,-1,50.03,206,-1,-1,-1,-1,15.25,2065.0,2964

        $live = new Live();
        $live->type = 'production';
        $live->time = time();

        // VPV1, VPV2, VPV3 => Voltages
        if (!empty($data[2]) && $data[4] != "-1") {
            $live->I1V = $data[2];
        }
        if (!empty($data[3]) && $data[4] != "-1") {
            $live->I2V = $data[3];
        }
        if (!empty($data[4]) && $data[4] != "-1") {
            $live->I3V = $data[4];
        }

        // IPV1, IPV2, IPV3 => Power
        if (!empty($data[5]) && $data[5] != "-1") {
            $live->I1P = $data[5];
        }
        if (!empty($data[6]) && $data[6] != "-1") {
            $live->I2P = $data[6];
        }
        if (!empty($data[7]) && $data[7] != "-1") {
            $live->I3P = $data[7];
        }


        // TODO
        if (!empty($data[2])) {
            $live->I1A = $data[2];
        }
        if (!empty($data[5])) {
            $live->I2A = $data[5];
        }
        if (!empty ($data[7])) {
            $live->GV = $data[7];
        }
        if (!empty ($data[8])) {
            $live->GA = $data[8];
        }
        if (!empty ($data[9])) {
            $live->GP = $data[9];
        }
        if (!empty ($data[10])) {
            $live->FRQ = $data[10];
        }
        if (!empty ($data[11])) {
            $live->EFF = $data[11];
        }
        if (!empty ($data[12])) {
            $live->INVT = $data[12];
        }
        if (!empty ($data[13])) {
            $live->BOOT = $data[13];
        }
        if (!empty ($data[19])) {
            $live->KWHT = $data[19];
        }
        
        // This line is only valid if GP and KWHT are filled with data
        if (empty($live->KWHT) || empty($live->GP)) {
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