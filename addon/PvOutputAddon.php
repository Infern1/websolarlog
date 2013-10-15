<?php
class PvOutputAddon {
	
	function __construct(){
		$this->config = Session::getConfig(); 
		$this->weather = new WeatherService();
		$this->metering = new HistorySmartMeterService();
	}
	/**
	 * Start the job
	 * @param mixed $args
	 */
	public function onJob($args) {
		foreach ($this->config->devices as $device){
			if ($device->pvoutputEnabled AND $device->active) {
				$live = $this->getUnsendHistory($device);
				$date = date("Ymd", $live->time);
				$time = date("H:i", $live->time);
				$smartMeter = $this->metering->PVoutputSmartMeterData($live->time);
				
				$v1 = $live->KWHT;//v1	Energy Generation	No1	number	watt hours	10000	r1
				$v2 = $live->GP;//v2	Power Generation	No	number	watts	2000	r1
				$v3 = $smartMeter['energy'];//v3	Energy Consumption	No	number	watt hours	10000	r1
				$v4 = $smartMeter['power'];//v4	Power Consumption	No	number	watts	2000	r1
				$v5 = $this->weather->PVoutputWeatherData($live->time);//v5	Temperature	No	decimal	celsius	23.4	r2
				$v6 = $live->GV;//v6	Voltage	No	decimal	volts	210.7	r2
				
				
				
				$result = $this->sendStatus($device, $date, $time, $v1, $v2, $v6, $v5, $v3, $v4);
				if ($result['info']['http_code'] == "200") {
					$live->pvoutput = 1;
					R::store($live);
				}elseif ($result['info']['http_code'] == "400") {
					$live->pvoutput = 2;
					$live->pvoutputErrorMessage = $result['response'];
					R::store($live);
				}else{
					$live->pvoutput = 3;
					$live->pvoutputErrorMessage = $result['response'];
					R::store($live);
				}
			}
		}
	}
	
	

	private function getUnsendHistory($device) {
		$date = mktime(0, 0, 0, date('m'), date('d')-13, date('Y'));
		$beans =  R::find(
				'history', 
				'deviceId = :deviceId AND time > :time AND (pvoutput is null or pvoutput = "" or pvoutput = 0) AND pvoutputSend = 1 order by time ASC LIMIT 1', 
				array( ':time' => $date,':deviceId'=>$device['id'])
				);
		return $beans;
	}

	
	private function sendStatus(Device $device, $date, $time, $KWHDtot, $GPtot, $GV, $temp, $smartMeterEnergy, $smartMeterPower) {
		$headerInfo = array();
		try {
			$vars = array(
					'd' => $date, // Date
	                't' => $time, // Time
	                'v1' => ($KWHDtot * 1000), // Energy Generation (Watt hours)
	                'v2' => $GPtot, // Power Generation (Watts)
	                'v3' => $smartMeterEnergy, // Energy Consumption (Watt hours)
	                'v4' => $smartMeterPower, // Power Consumption (Watts)
	                'v5' => number_format($temp, 2), // Temperature (Celsius)
	                'v6' => $GV, // Voltage (volts)
					'c1' => '1', // Cumulative
					
			);
		
			// header info
			$headerInfo['hAPI'] = "X-Pvoutput-Apikey: " . $device->pvoutputApikey;
			$headerInfo['hSYSTEM'] = "X-Pvoutput-SystemId: " . $device->pvoutputSystemId;
			
			//$pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');
			$url = "http://pvoutput.org/service/r2/addstatus.jsp";
			$result = $this->PVoutputCurl($url,$vars,$headerInfo,true);
			HookHandler::getInstance()->fire("onInfo", print_r($result,true));
			return $result;
		} catch (Exception $e) {
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
		return false;	
	}

	//http://www.pvoutput.org/help.html#api-getteam
	//http://pvoutput.org/service/r2/getsystem.jsp?sid=7856&key=b26114a4b573381947b837c12b5d4f3155d1d08a&teams=1&donations=1&tariffs=1
	/**
	 * get System With Teams From PVoutput
	 * @param unknown $device
	 * @return NULL
	 */
	public function saveTeamStateFromPVoutputToDB($device){
		$headerInfo = array();
		try {
			$vars = array(
				'teams' => 1,
				'donations'=>1,
				'tariffs'=>1,
			);
			// header info
			$headerInfo['hAPI'] = "X-Pvoutput-Apikey: " . $device->pvoutputApikey;
			$headerInfo['hSYSTEM'] = "X-Pvoutput-SystemId: ".$device->pvoutputSystemId;
			$url = "http://pvoutput.org/service/r2/getsystem.jsp";
			$result = $this->PVoutputCurl($url,$vars,$headerInfo,true);
			//var_dump($result);
			if($result['info']['http_code']==200){
				$team = explode(';',$result['response']);
				$pos = strpos($team[count($team)-2], '602');
				if($pos!==false){
					$pvoutputWSLTeamMember =true;
				}else{
					$pvoutputWSLTeamMember=false;
				}
				
				$bean = R::load('inverter',$device->id);
				if (!$bean){
					$bean = R::dispense('inverter');
				}
				$bean->pvoutputWSLTeamMember = $pvoutputWSLTeamMember;
				//Store the bean
				$id = R::store($bean);
			}else{
				return null;
			}
		}catch (Exception $e){
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
	}
	
	
	
	public function getTeamStatusFromDB($device) {
		$bean = R::load('inverter',$device->id);
	
		$device = new Device();
		$device->id = $bean->id;
		$device->name = $bean->name;
		($device->pvoutputApikey) ? $device->pvoutputApikey = true : $device->pvoutputApikey = false;
		($device->pvoutputSystemId) ? $device->pvoutputSystemId = true : $device->pvoutputSystemId = false;
		$device->pvoutputWSLTeamMember = $bean->pvoutputWSLTeamMember;
		return $device;
	}
	
	
	
	public function getTeamFiguresFromPVoutput(){
		$headerInfo = array();
		try {
			$vars = array(
					'tid' => 602, // TeamID WebSolarLog
			);
			// header info
			$headerInfo['hAPI'] = "X-Pvoutput-Apikey: MyReadOnlyKey";//ReadOnlyKey
			$headerInfo['hSYSTEM'] = "X-Pvoutput-SystemId: 7856";//
			$url = "http://pvoutput.org/service/r2/getteam.jsp";
			$result = $this->PVoutputCurl($url,$vars,$headerInfo,true);
			if($result['info']['http_code']==200){
				$team = explode(';',$result['response']);
				if($team[4]>1000000){
					$team[4] = round($team[4]/1000000,3);
					$energyNotation = 'MWh';
					
				}else{
					if($team[4]>10000){
						$team[4] = round($team[4]/1000,2);
						$energyNotation = 'kWh';
					}
				}
				return array('TeamName'=>$team[0],
						'TeamSize'=>$team[1],
						'AverageSize'=>$team[2],
						'NumberOfSystems'=>$team[3],
						'EnergyGenerated'=>$team[4],
						'EnergyGeneratedNotation'=>$energyNotation,
						'Outputs'=>$team[5],
						'EnergyAverage'=>$team[6],
						'Type'=>$team[7],
						'Description'=>$team[8]);
			}else{
				return null;
			}
		}catch (Exception $e){
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
	}


	public function joinTeam($device){
		$headerInfo = array();
		try {
			$vars = array(
					'tid' => 602, // TeamID WebSolarLog
			);
			// header info
			$headerInfo['hAPI'] = "X-Pvoutput-Apikey: " . $device->pvoutputApikey;
			$headerInfo['hSYSTEM'] = "X-Pvoutput-SystemId: ".$device->pvoutputSystemId;
			$url = "http://pvoutput.org/service/r2/jointeam.jsp";
			$result = $this->PVoutputCurl($url,$vars,$headerInfo,true);
			$this->saveTeamStateFromPVoutputToDB($device);
			return $result;
		}catch (Exception $e){
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
	}
	


	public function leaveTeam($device){
		$headerInfo = array();
		try {
			$vars = array(
					'tid' => 602, // TeamID WebSolarLog
			);
			// header info
			$headerInfo['hAPI'] = "X-Pvoutput-Apikey: " . $device->pvoutputApikey;
			$headerInfo['hSYSTEM'] = "X-Pvoutput-SystemId: ".$device->pvoutputSystemId;
			$url = "http://pvoutput.org/service/r2/leaveteam.jsp";
			return $this->PVoutputCurl($url,$vars,$headerInfo,true);
		}catch (Exception $e){
			HookHandler::getInstance()->fire("onError", $e->getMessage());
		}
	}
	
	private function PVoutputCurl($url,$vars,$headerInfo,$returnOutput=false){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, count($vars));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headerInfo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4); // Connection timeout in seconds
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Transmission timeout in seconds
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		$httpResponse = curl_getinfo($ch,CURLINFO_HEADER_OUT);
		curl_close($ch);
		HookHandler::getInstance()->fire("onDebug", "send to pvoutput: " . print_r($vars, true) . " result: " .  $response);
		if ($info['http_code'] == "200") {
			if($returnOutput){
				return array('result'=>true,'response'=> $response,'info'=>$info);
			}else{
				return true;
			}
		}else{
			if($returnOutput){
				return array('result'=>false,'response'=> $response,'info'=>$info);
			}else{
				return false;
			}
			
		}
	}

}
?>