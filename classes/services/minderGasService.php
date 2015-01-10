<?php
class minderGasService {
	public static $tbl = "energySmartMeter";
	private $config;
	
	function __construct() {
		$this->config = Session::getConfig();
	}
        

        public function minderGasCurl(){
            echo "aaaa";
            foreach ($this->config->devices as $device) {
                if($device->minderGasAPIToken){
                    $yesterday = Util::getBeginEndDate('yesterday',1);

                    
                    $query = ' time > :beginDate AND time < :endDate and deviceId = :deviceId';
                    $parameters = array( ':beginDate' => $yesterday['beginDate'],':endDate'=>$yesterday['endDate'],':deviceId'=>$device->id);
                    $beans =  R::findOne(self::$tbl,$query,$parameters);

                    $jsonData = json_encode(array("date" => date("Y-m-d",$beans['time']), "reading" => ($beans['gasUsageT']/1000)));
  
                    $headerData = array('Content-Type:application/json','Accept:application/json','AUTH-TOKEN:'.$device->minderGasAPIToken );

                    $ch = curl_init("https://www.mindergas.nl/api/gas_meter_readings");
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4); // Connection timeout in seconds
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Transmission timeout in seconds
                    $response = curl_exec($ch);
                    $info = curl_getinfo($ch);
                    
                    $httpResponse = curl_getinfo($ch,CURLINFO_HEADER_OUT);
                    curl_close($ch);
                }
            }
        }
}
?>