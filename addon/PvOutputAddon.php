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
		HookHandler::getInstance()->fire("onDebug", "-Run PVoutputAddonJob with the following args:".print_r($args,true));
		foreach ($this->config->devices as $device){
			HookHandler::getInstance()->fire("onDebug", "--Foreach Device in config. Current device:".print_r($device,true));
			if ($device->pvoutputEnabled AND $device->active) {
				HookHandler::getInstance()->fire("onDebug", "--Foreach Device in config. Current device:".print_r($device,true));
				$live = $this->getUnsendHistory($device->id);

				HookHandler::getInstance()->fire("onDebug", "---Show getUnsendHistory:".print_r($live,true));
				// Set the defaults
				$date = date("Ymd", time());
				$time = date("H:i", time());
				$timestamp = time();
				
				// solar data
				$v1 = 0;//v1	Energy Generation	No1	number	watt hours	10000	r1
				$v2 = 0;//v2	Power Generation	No	number	watts	2000	r1
				
				//smartMeter data
				$v3 = 0;//v3	Energy Consumption	No	number	watt hours	10000	r1
				$v4 = 0;//v4	Power Consumption	No	number	watts	2000	r1
				
				// Grid voltage
				$v6 = 0;//v6	Voltage	No	decimal	volts	210.7	r2
				
				
				// Fill in the values for the found live values
				if(count($live)>=1){
					HookHandler::getInstance()->fire("onDebug", "----We have a Live record!");
					$date = date("Ymd", $live->time);
					$time = date("H:i", $live->time);
					$timestamp = $live->time;
					$v1 = $live->KWHT;//v1	Energy Generation	No1	number	watt hours	10000	r1
					$v2 = $live->GP;//v2	Power Generation	No	number	watts	2000	r1
					$v6 = $live->GV;//v6	Voltage	No	decimal	volts	210.7	r2
					$sendDataWholeDay = false;
				}
				
				$smartMeter = $this->metering->PVoutputSmartMeterData($timestamp);
				if($device->sendSmartMeterData == null){
					$device->sendSmartMeterData == true;
				}
				if($smartMeter['energy'] > 0 && $smartMeter['power'] > 0 && $device->sendSmartMeterData){
					HookHandler::getInstance()->fire("onDebug", "----We have smartMeter Data!");
					$v3 = $smartMeter['energy'];//v3	Energy Consumption	No	number	watt hours	10000	r1
					$v4 = $smartMeter['power'];//v4	Power Consumption	No	number	watts	2000	r1
					
					//we get SmartMeter data so we want to send this data day and night.
					$sendDataWholeDay = true;
				}else{
					HookHandler::getInstance()->fire("onDebug", "----We DO NOT smartMeter Data!");
					//we get NO SmartMeter data so we don't want to send this data day and night.
					$v3 = 0;//v3	Energy Consumption	No	number	watt hours	10000	r1
					$v4 = 0;//v4	Power Consumption	No	number	watts	2000	r1
					$sendDataWholeDay = false;
				}

				$v5 = $this->weather->PVoutputWeatherData($timestamp);//v5	Temperature	No	decimal	celsius	23.4	r2
				
				//When the sun is NOT down OR if the sun is down and we want to "sendDataWholeDay":
				if(Util::isSunDown(1800)==false || (Util::isSunDown(1800)==true && $sendDataWholeDay == true)){
					
					HookHandler::getInstance()->fire("onDebug", "-----Attemp to send data to PVoutput!");
					
					$result = $this->sendStatus($device, $date, $time, $v1, $v2, $v6, $v5, $v3, $v4);
				
					if(count($live)>=1){
						$object = new History();
						
						$object->id = $live->id;
						$object->INV = $live->INV;
						$object->deviceId = $live->deviceId;
						$object->SDTE = $live->SDTE;
						$object->time = $live->time;
						$object->dayNum = $live->dayNum;
							
						$object->I1V = $live->I1V;
						$object->I1A = $live->I1A;
						$object->I1P = $live->I1P;
						$object->I1Ratio = $live->I1Ratio;
							
						$object->I2V = $live->I2V;
						$object->I2A = $live->I2A;
						$object->I2P = $live->I2P;
						$object->I2Ratio = $live->I2Ratio;
							
						$object->I3V = $live->I3V;
						$object->I3A = $live->I3A;
						$object->I3P = $live->I3P;
						$object->I3Ratio = $live->I3Ratio;
							
						$object->GV = $live->GV;
						$object->GA = $live->GA;
						$object->GP = $live->GP;
							
						$object->GV2 = $live->GV2;
						$object->GA2 = $live->GA2;
						$object->GP2 = $live->GP2;
							
						$object->GV3 = $live->GV3;
						$object->GA3 = $live->GA3;
						$object->GP3 = $live->GP3;
							
						$object->IP = $live->IP;
						$object->ACP = $live->ACP;
							
						$object->FRQ = $live->FRQ;
						$object->EFF = $live->EFF;
						$object->INVT = $live->INVT;
						$object->BOOT = $live->BOOT;
						$object->KWHT = $live->KWHT;
						$object->pvoutput = $live->pvoutput;
						$object->pvoutputErrorMessage = $live->pvoutputErrorMessage;
						$object->pvoutputSend = $live->pvoutputSend;
						$object->pvoutputSendTime = time();
						
						if ($result['info']['http_code'] == "200") {
								$object->pvoutput = 1;
								$this->history->save($object);
						}elseif ($result['info']['http_code'] == "400") {
								$object->pvoutput = 2;
								$object->pvoutputErrorMessage = $result['response'];
								$this->history->save($object);
						}else{
							$object->pvoutput = 0;
							$object->pvoutputErrorMessage = $result['response'];
							$this->history->save($object);
						}
					}else{
						$object->pvoutput = 0;
						$object->pvoutputErrorMessage = 'unknown error....';
						$this->history->save($object);
						HookHandler::getInstance()->fire("onDebug", "http_code:unknown....");
					}
				}
			}
		}
	}
	
	//
	public function onShutdown($args){
		// get args
		$hookname = $args[0];
		$device = $args[1];
		
		// get timestamps for today
		$date = Util::getBeginEndDate('day', 1);
		// define parameters
		$parameters = array(":time"=>$date['beginDate'],":deviceId"=>$device->id);
		
		// get the last record of the history table for today and this device
		$bean = R::getAll("SELECT * FROM history WHERE time >= :time AND deviceId = :deviceId ORDER BY id DESC limit 1;",$parameters);
		// move from multidimension to none.
		$bean = $bean[0];
		
		//create history object
		$object = new History();
		// set the ID of the bean for the object
		$object->id = $bean['id'];
		// set the object to be sendable
		$object->pvoutputSend = 1;
		// save the object to the table.
		$this->history->save($object);
		
		// now run the job and sent the record we just set as sendable
		$this->onJob(null);
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

			// header info
			$headerInfo['hAPI'] = "X-Pvoutput-Apikey: " . $device->pvoutputApikey;
			$headerInfo['hSYSTEM'] = "X-Pvoutput-SystemId: " . $device->pvoutputSystemId;
			
			//$pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');
			$url = "http://pvoutput.org/service/r2/addstatus.jsp";
			$result = $this->PVoutputCurl($url,$vars,$headerInfo,true);
			return $result;
		} catch (Exception $e) {
			HookHandler::getInstance()->fire("onError","PVoutput::SendStatus".$e->getMessage());
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
			HookHandler::getInstance()->fire("onError", "PVoutput::saveTeamStateFromPVoutputToDB".$e->getMessage());
		}
	}
	
	public function getPvOutputDayData($date,$deviceId){
		$device = $this->config->getDeviceConfig($deviceId);
		// get timestamps for the dates begin and end.
		$timestamps = Util::getBeginEndDate('day', 1,$date);
		
		$parameters = array( ':beginDate' => $timestamps['beginDate'],':endDate' => $timestamps['endDate'],':deviceId'=>$deviceId);
		
		
		$sql = 'SELECT * FROM history WHERE deviceId = :deviceId AND time > :beginDate AND time < :endDate ORDER BY id DESC';
		
		$beans =  R::getAll($sql,
				$parameters
		);
		return array("beans"=>$beans,"date"=>$date,"deviceName"=>$device->name,"recordCount"=>count($beans));
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