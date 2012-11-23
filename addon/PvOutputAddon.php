<?php
class PvOutputAddon {
	public function onHistory($args) {
		$inverter = $args[1]; // Inverter object
		$live = $args[2]; // Live object
		
		// We are only allowed for 5 till 15 minutes
		if (Session::getConfig()->pvoutputEnabled && PeriodHelper::isPeriodJob("PvOutputJob", 10)) {
			$date = date("Ymd", $live->time);
			$time = date("h:n", $live->time);
			
			// KLOPT DIT >>>> ???
			sendStatus($inverter, $date, $time, $live->GP, $live->KWHT, $live->GV);
		}
		
	}
	
	/*
	 * Ik heb hier bewust de oude termen gebruikt vanuit 123 aurora
	 */
	private function sendStatus($inverter, $date, $time, $KWHDtot, $GPtot, $GV) {
		$vars = array(
				'd' => $date, // Date
                't' =>  $time, // Time
                'v1' => $KWHDtot, // Energy Generation (Watt hours)
                'v2' => $GPtot, // Power Generation (Watts)
                //'v3' => '10000', // Power Consumption (Watt hours)
                //'v4' => '2000', // Energy Consumption (Watts)
                //'v5' => '23.4', // Temperature (Celsius)
                'v6' => $GV, // Voltage (volts)
				'c1' => '0', // Cumulative
				
		);
	
		// header info
		$hAPI = "X-Pvoutput-Apikey: " . Session::getConfig()->pvoutputApikey;
		$hSYSTEM = "X-Pvoutput-SystemId: " . $inverter->pvoutputSystemId;
		
		//$pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');
		
		$ch = curl_init("http://pvoutput.org/service/r2/addstatus.jsp");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( $hAPI, $hSYSTEM));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		curl_close($ch);
	}
}