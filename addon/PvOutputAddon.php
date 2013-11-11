<?php
class PvOutputAddon {
	
	function __construct(){
		$this->config = Session::getConfig(); 
		$this->weather = new WeatherService();
		$this->metering = new HistorySmartMeterService();
		$this->history = new HistoryService();
	}
	/**
	 * Start the job
	 * @param mixed $args
	 */
	public function onJob($args) {
		foreach ($this->config->devices as $device){
			if ($device->pvoutputEnabled AND $device->active) {

				$live = $this->getUnsendHistory($device->id);

				if(count($live)>=1){
					$date = date("Ymd", $live->time);
					$time = date("H:i", $live->time);
					$timestamp = $live->time;
				}else{
					$date = date("Ymd", time());
					$time = date("H:i", time());
					$timestamp = time();
				}
				$smartMeter = $this->metering->PVoutputSmartMeterData($timestamp);

				$v1 = $live->KWHT;//v1	Energy Generation	No1	number	watt hours	10000	r1
				$v2 = $live->GP;//v2	Power Generation	No	number	watts	2000	r1
				$v3 = $smartMeter['energy'];//v3	Energy Consumption	No	number	watt hours	10000	r1
				$v4 = $smartMeter['power'];//v4	Power Consumption	No	number	watts	2000	r1
				$v5 = $this->weather->PVoutputWeatherData($timestamp);//v5	Temperature	No	decimal	celsius	23.4	r2
				$v6 = $live->GV;//v6	Voltage	No	decimal	volts	210.7	r2

				$result = $this->sendStatus($device, $date, $time, $v1, $v2, $v6, $v5, $v3, $v4);
				HookHandler::getInstance()->fire("onInfo", "before http_code:\r\n".print_r($result,true));
				if ($result['info']['http_code'] == "200" and $v1 > 0 AND $v2 > 0) {
					HookHandler::getInstance()->fire("onInfo", "http_code:200:\r\n".print_r($result,true));
					$live->pvoutput = 1;
					$this->history->save($live);
				}elseif ($result['info']['http_code'] == "400" and $v1 > 0 AND $v2 > 0) {
					HookHandler::getInstance()->fire("onInfo", "http_code:400:\r\n".print_r($result,true));
					$live->pvoutput = 2;
					$live->pvoutputErrorMessage = $result['response'];
					$this->history->save($live);
				}else{
					HookHandler::getInstance()->fire("onDebug", "http_code:unknown:\r\n".print_r($result,true));
					//$live->pvoutput = 3;
					//$live->pvoutputErrorMessage = $result['response'];
					//$this->history->save($live);
				}
			}
		}
	}
	
	private function getUnsendHistory($deviceId) {
		$date = mktime(0, 0, 0, date('m'), date('d')-13, date('Y'));
		$parameters = array( ':time' => $date,':deviceId'=>$deviceId);
		$beans =  R::findOne(
				'history',
				' deviceId = :deviceId AND time > :time AND (pvoutput is null or pvoutput = "" or pvoutput = 0) AND pvoutputSend = 1 order by time ASC limit 1',
				$parameters
		);	
		return $beans;
	}

	
	private function sendStatus(Device $device, $date, $time, $KWHDtot, $GPtot, $GV, $temp, $smartMeterEnergy=0, $smartMeterPower=0) {
		$headerInfo = array();
		$vars = array();
		try {

			$vars['d'] = $date;
			$vars['t'] = $time;
			
			// Production
			if($KWHDtot > 0){
				$vars['v1'] = ($KWHDtot * 1000);
			}
			// Production
			if($GPtot > 0){
				$vars['v2'] = $GPtot;
			}
			// Production
			if($GV>0){
				$vars['v6'] = $GV;
			}
			
			// SmartMeter
			if($smartMeterEnergy>0){
				$vars['v3'] = $smartMeterEnergy;
			}
			// SmartMeter
			if($smartMeterPower>0){
				$vars['v4'] = $smartMeterPower;
			}
			// Weather
			if($temp){
				$vars['v5'] = number_format($temp, 2);
			}
			

			$vars['c1'] = '1';
				
			HookHandler::getInstance()->fire("onInfo", "vars::".print_r($vars,true));
			
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