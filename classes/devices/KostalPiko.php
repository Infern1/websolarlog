<?php
Class KostalPiko implements DeviceApi {
    private $ADR;
    private $DEBUG;
    private $PATH;

    private $device;
    private $communication;
    private $useCommunication = false;
    
    function __construct($path, $address, $debug) {
        $this->ADR = $address;
        $this->DEBUG = $debug;
        $this->PATH = $path;
    }
    
    function setCommunication(Communication $communication, Device $device) {
    	$this->communication = $communication;
    	$this->device = $device;
    	$this->useCommunication = true;
    }
    
    /**
     * @see DeviceApi::getState()
     */
    public function getState() {
    	return 0; // Try to detect, as it will die when offline
    }

    public function getAlarms() {
    	if ($this->DEBUG) {
    		return "W2223424".rand(0,9);
    	} else {
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->DEBUG) {
            return "0PRO,Piko,1,1.3.0,20130730
1TIM,2013-07-30T13:22:35.801768,17419h59m39s,8411h39m19s
2INF,90xxxKBNxxxxx,Piko_name,192.168.1.10,81,1,PIKO 8.3,2,3
3STA,3,Running-MPP,28,---L123,0
4ENE,11629195,13803
5PWR,4760,4531,95.2
6DC1,596.2,3.97,2370,51.21,94e0,4009
7DC2,614.9,3.88,2390,51.21,94e0,c00a
8DC3,0.0,0.00,0,51.29,94c0,0003
9AC1,241.6,6.31,1528,60.14,8540
10AC2,236.4,6.16,1466,60.14,8540
11AC3,243.5,6.27,1537,60.07,8560
12PRT,PIKO-Portal,01h48m49s
13HST,00h07m36s,00h15m00s";
        } else {
            return trim($this->execute(' -csv -q'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	$live = KostalPikoConverter::toLive($data);
    	HookHandler::getInstance()->fire("onDebug", print_r($live,true));
    	return $live;
    }

    public function getInfo() {
        if ($this->DEBUG) {
            return "PowerOne XXXXXX.XXXXXXXX";
        } else {
           return $this->execute('-p -n -f -g -m -v -Y 10');
        }
    }

    public function getHistoryData() {
    	// Try to retrieve the data of the last 366 days
    	$result = $this->execute('-k366 -Y60'); // -K10 is not yet supported by aurora
        
        if ($result) {
        	HookHandler::getInstance()->fire("onDebug", "getHistoryData :: start processing the result");
        	$deviceHistoryList = array();
        	$lines = explode("\n", $result);
        	foreach ($lines as $line) {
        		$deviceHistory = AuroraConverter::toDeviceHistory($line);
        		if ($deviceHistory != null) {
        			$deviceHistory->amount = $deviceHistory->amount * 10; // Remove this line when -K10 is supported
        			$deviceHistoryList[] = $deviceHistory;
        		}
        	}
        	return $deviceHistoryList;
        } else {
        	if (Session::getConfig()->debugmode) {
        		HookHandler::getInstance()->fire("onDebug", "getHistoryData :: nothing returned by inverter result=" + $result);
        	}
        }
        
        return null;
    }

    public function syncTime() {
        return $this->execute('-L');
    }
    
    
    public function doCommunicationTest() {
    	$result = false;
    	$data = $result = $this->execute(' -csv -q');
    	if ($data) {
    		$result = true;
    	}
    	 
    	return array("result"=>$result, "testData"=>$data);
    }
    

    private function execute($options) {
    	$exec = shell_exec($this->PATH . ' ' . $options);
    	HookHandler::getInstance()->fire("onDebug", print_r($exec,true));
		return $exec;
    }
}
?>