<?php
class GrowattConverter
{

    /**
     * Converts the result of getData to an Live object
     * @param string $inputLine
     * @return Live or null
     */
    public static function toLive($inputLine)
    {
    	// Input Data from Growatt is as follows
    	/*
    	 * 
    	 * 
            [0, 57, 2280, 0, 0, 57, 0, 0, 0, 0, 0, 52, 4998, 2296, 0, 0, 52, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 654, 23, 19393, 248, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3612, 0, 0]
            pv_watts 4.8
            pv_volts 228.0
            pv_amps 0.0
            Onbekend 5.6
            out_watts 5.2
            ac_hz 49.98
            ac_volts 229.6
            Onbekend 0.0
            Onbekend 46.0
            Wh_today 200.0
            Wh_total 65400.0
            Time_total 230.0
            Onbekend 19421.0
            Temp_inverter 24.8
            Onbekend 3612.0
    	 */    	 
        // Check if the input line is valid
        if ($inputLine == null || trim($inputLine) == "") {
            echo("Input from Converter is null");
            return null;
        }

        // Split on a serie of spaces (not one)
        preg_match("/(?<=\[).*?(?=])/", $inputLine, $data);
        $data = explode(", ",$data[0]);
          
        
        for ($i = 0; $i < count($data); $i++) {
        	$data[$i] = str_replace(",",".",$data[$i]);
        }
        
        $live = new Live();
        $live->type = 'production';

        // getting timestamp from system time
        $timezone = date_default_timezone_get();
        //echo("The current server timezone is: " . $timezone."\n");
        $live->time =  Util::getTimestampOfDate(date('H', time()),date('i', time()),date('s', time()),date('d', time()),date('m', time()),date('Y', time()));
        //echo("The live Timestamp is: " . $live->time."\n");
        
        if (!empty ($data[0])) {
            $live->I1V = self::liveLineToValues($data[0],"float");
        }
        if (!empty ($data[1])) {
            $live->I1A = self::liveLineToValues($data[1],"float");
        }
        if (!empty ($live->I1V) && !empty($live->I1A)) {
            $live->I1P = self::liveLineToValues(($live->I1V * $live->I1A),"float");
        }else{
        	$live->I1P = 0;
        }
        //echo("The live Power is: " . $live->I1P."\n");
        
        // second string is 0
        $live->I2P = 0;
        $live->I2V = 0;
        $live->I2A = 0;
        if (!empty ($data[3])) {
            $live->GV = self::liveLineToValues($data[3],"float");
        }
        if (!empty ($data[4])) {
            $live->GA = self::liveLineToValues(round((($live->I1P /100) *$data[6])/$data[3],3),"float");
        }
        if ($live->I1P > 0 && !empty ($data[6])){
            $live->GP = self::liveLineToValues((($live->I1P /100) *$data[6]),"float");
        }
        if (!empty ($data[6])) {
            $live->FRQ = self::liveLineToValues($data[2],"float");
        }
        if (!empty ($data[7])) {
            $live->INVT = self::liveLineToValues($data[5],"float");
        }
        if (!empty ($data[8])) {
            $live->KWHT = self::liveLineToValues($data[10],"float");
        }
        if(!empty ($data[6])){
        	$live->EFF = self::liveLineToValues($data[6],"float");
        }
        //echo("Efficency:".$live->EFF." \n" );
        
        //data not given by converter is set to 0
       	$live->BOOT = 0;
        
        

        return $live;
    }
    
    public static function liveLineToValues($line,$type='float'){
    	$result = array();
    	switch ($type){
    		case 'float':
    			$float = str_replace(",",".",$line);
    			$result = (float)$float;
    			break;
    		case 'string':
    			$splitLine = preg_split("/:/",$line);
    			$result = trim($splitLine[1]);
    			break;
	   		case 'GrowattDateTime':
	   			//$splitLine= preg_split("/ /",$line);
	   	    	preg_match('/(\d{2})\/(\d{2})\/(\d{4}) (\d{2})\:(\d{2})\:(\d{2})/i', $line, $matches,0);
	   	    	$result =  Util::getTimestampOfDate($matches[4],$matches[5],$matches[6],$matches[1],$matches[2],$matches[3]);
	   			break;
    	}
    	return $result; 
    }
}
