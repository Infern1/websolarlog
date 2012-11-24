<?php
class PvOutputAddon {
	
	// This job will be called every 10 minutes
	public function onJob($args) {
		$beans = $this->getUnsendHistory();
		foreach ($beans as $live) {
			$inverter= Session::getConfig()->getInverterConfig($live->INV);
			if ($inverter->pvoutputEnabled) {
				$date = date("Ymd", $live->time);
				$time = date("H:i", $live->time);
				
				$result = $this->sendStatus($inverter, $date, $time, $live->KWHT, $live->GP, $live->GV);
				if ($result) {
					$live->pvoutput = true;
					R::store($live);
				}
				return;
			}
		}
	}
	
	private function getUnsendHistory() {
		$date = mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
		$beans =  R::find( 'history', 'time > :time and (pvoutput is null or pvoutput = "" or pvoutput = 0)', array( 'time' => $date));
		return $beans;
	}
	
	private function sendStatus($inverter, $date, $time, $KWHDtot, $GPtot, $GV) {
		try {
			$vars = array(
					'd' => $date, // Date
	                't' =>  $time, // Time
	                'v1' => ($KWHDtot * 1000), // Energy Generation (Watt hours)
	                'v2' => $GPtot, // Power Generation (Watts)
	                //'v3' => '10000', // Power Consumption (Watt hours)
	                //'v4' => '2000', // Energy Consumption (Watts)
	                //'v5' => '23.4', // Temperature (Celsius)
	                'v6' => $GV, // Voltage (volts)
					'c1' => '1', // Cumulative
					
			);
		
			// header info
			$hAPI = "X-Pvoutput-Apikey: " . $inverter->pvoutputApikey;
			$hSYSTEM = "X-Pvoutput-SystemId: " . $inverter->pvoutputSystemId;
			
			//$pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');
			
			$ch = curl_init("http://pvoutput.org/service/r2/addstatus.jsp");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( $hAPI, $hSYSTEM));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$result = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			HookHandler::getInstance()->fire("onDebug", "send to pvoutput: " . print_r($vars, true) . " result: " .  $result);
			if ($httpCode == "200") {
				return true;
			}
		} catch (Exception $e) {
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
		return false;	
	}
}