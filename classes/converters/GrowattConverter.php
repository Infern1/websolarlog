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
            [
         * 0,       reg#01
         * 57,      reg#02 I1P/Ppv L Input power (low) 0.1W
         * 2280,    reg#03 I1V/Vpv1 PV1 voltage 0.1V
         * 0,       reg#04 I1A/PV1Curr PV1 input current 0.1A
         * 0,       reg#05
         * 57,      reg#06 PV1Watt L PV1 input watt (low) 0.1W
         * 0,       reg#07
         * 0,       reg#08
         * 0,       reg#09
         * 0,       reg#10
         * 0,       reg#11
         * 52,      reg#12 Pac L Output power (low) 0.1W
         * 4998,    reg#13 FRQ/Fac Grid frequency 0.01Hz
         * 2296,    reg#14 GV/Vac1 Three/single phase grid voltage 0.1V
         * 0,       reg#15 GA/Iac1 Three/single phase grid output current 0.1A
         * 0,       reg#16
         * 52,      reg#17 GP/Pac1 L Three/single phase grid output watt (low) 0.1VA
         * 0,       reg#18
         * 0,       reg#19
         * 0,       reg#20
         * 0,       reg#21
         * 0,       reg#22
         * 0,       reg#23
         * 0,       reg#24
         * 0,       reg#25
         * 0,       reg#26
         * 2,       reg#27 Energy today L Today generate energy today (low) 0.1KWH
         * 0,       reg#28
         * 654,     reg#29 Energy total L Total generate energy (low) 0.1KWH
         * 23,      reg#30 Time total H Work time total (high) 0.5S
         * 19393,   reg#31 Time total L Work time total (low) 0.5S
         * 248,     reg#32 Temperature Inverter temperature 0.1C
         * 0,       reg#33
         * 0,       reg#34
         * 0,       reg#35
         * 0,       reg#36
         * 0,       reg#37
         * 0,       reg#38
         * 0,       reg#39
         * 0,       reg#40
         * 0,       reg#41
         * 3612,    reg#42 Undocumented
         * 0,       reg#43
         * 0        reg#44
         * ]
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

        $live = new Live();
        $live->type = 'production';

        // getting timestamp from system time
        $timezone = date_default_timezone_get();
        //echo("The current server timezone is: " . $timezone."\n");
        $live->time =  Util::getTimestampOfDate(date('H', time()),date('i', time()),date('s', time()),date('d', time()),date('m', time()),date('Y', time()));
        //echo("The live Timestamp is: " . $live->time."\n");
        
        if (!empty ($data[2])) {
            $live->I1V = self::liveLineToValues($data[2]/10,"float");
        }
        if (!empty ($data[3])) {
            $live->I1A = self::liveLineToValues($data[3]/10,"float");
        }
        if (!empty ($data[1])) {
            $live->I1P = self::liveLineToValues($data[1]/10,"float");
        }
        
        //echo("The live Power is: " . $live->I1P."\n");
        
        // second string is 0
        $live->I2P = 0;
        $live->I2V = 0;
        $live->I2A = 0;
        if (!empty ($data[16])) {
            $live->GV = self::liveLineToValues($data[16]/10,"float");
        }
        if (!empty ($data[14])) {
            $live->GA = self::liveLineToValues($data[14]/10,"float");
        }
        if (!empty ($data[16])) {
            $live->GP = self::liveLineToValues($data[16]/10,"float");
        }
        if (!empty ($data[12])) {
            $live->FRQ = self::liveLineToValues($data[12]/100,"float");
        }
        if (!empty ($data[31])) {
            $live->INVT = self::liveLineToValues($data[31]/10,"float");
        }
        if (!empty ($data[28])) {
            $live->KWHT = self::liveLineToValues($data[28]/10,"float");
        }
        if(!empty ($data[6])){
            $live->EFF = self::liveLineToValues((($live->GP/$live->I1P)*100),"float");
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
