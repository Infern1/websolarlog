<?php
Class SmartMeter implements DeviceApi {

	///////////////////////////////////////////////////
	//
	//  Name:	no additional software required
	//  url:	--
	//  author: --
	//
	///////////////////////////////////////////////////
	
    private $debug;
    private $device;
    private $communication;

    function __construct(Communication $communication, Device $device, $debug = false) {
        $this->communication = $communication;
        $this->device = $device;
        $this->debug = $debug;
    }
    
    /**
     * @see DeviceApi::getState()
     */
    public function getState() {
    	return 0; // A smartmeter should never be offline iff the cable is connected
    }
    
    public function getAlarms() {
    	// not supported
    }

    public function getData() {
        if ($this->debug) {
            return '/XMX5XMXABCE000024595

0-0:96.1.1(22222222222222222222222222222222)
1-0:1.8.1(01192.830*kWh)
1-0:1.8.2(00789.784*kWh)
1-0:2.8.1(00234.992*kWh)
1-0:2.8.2(00572.925*kWh)
0-0:96.14.0(0002)
1-0:1.7.0(0000.47*kW)
1-0:2.7.0(0000.00*kW)
0-0:17.0.0(999*A)
0-0:96.3.10(1)
0-0:96.13.1()
0-0:96.13.0()
0-1:96.1.0(1111111111111111111111111111111111)
0-1:24.1.0(03)
0-1:24.3.0(130116180000)(00)(60)(1)(0-1:24.2.0)(m3)
(00348.414)
0-1:24.4.0(1)
!';
        } else {
            return trim($this->execute());
        }
    }
    
    public function getLiveData() {
    	//echo "\r\ngetLiveData\r\n";
    	$data = explode("\n",$this->getData());
    	//echo "\r\ndata:".var_dump($data)."\r\n";
    	return SmartMeterConverter::toLiveSmartMeter($data);
    }

    public function getInfo() {
        // not supported
    }

    public function getHistoryData() {
        // not supported
    }

    public function syncTime() {
        // not supported
    }
    
    public function doCommunicationTest() {
        $result = false;
        
        $data['RawResponse'] = $this->getData();        
        $data['LiveObject'] = SmartMeterConverter::toLiveSmartMeter(explode("\n", $data['RawResponse']));

        if ($data) {
            $result = true;
        }
        return array("result" => $result, "testData" => print_r($data,true));
    }

    private function execute() {
        $uri = $this->communication->uri . " " . $this->communication->port;

        // Check for dangerous programs
  		$badAppString = "rm,cat,tail,reboot,halt,shutdown,fdisk,mkfs,sh,cp,mv,dd";      
    	$badApp = explode(",",$badAppString);
    	foreach ($badApp as $app) {
    		if (strpos($uri, $app . " ") !== false) {
    			HookHandler::getInstance()->fire("onError", "SmartMeter path contains blacklisted program: " . $app);
    			return null;
    		}
    	}
    	
    	return shell_exec($uri);
    }
}
?>