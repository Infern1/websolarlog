<?php
Class KostalPiko implements DeviceApi {
    private $ADR;
    private $PORT;
    private $COMOPTION;
    private $DEBUG;
    private $PATH;

    private $device;
    private $communication;
    private $useCommunication = false;
    
    function __construct($path, $address, $port, $comoption, $debug) {
        $this->ADR = $address;
        $this->PORT = $port;
        $this->COMOPTION = $comoption;
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
            return "PRO,Piko,1,1.3.0-Beta,20130626
INF,12345ABC67890,DeviceName
STA,3,Running,28,---L123
ENE,6975574,26366
PWR,1011,936,92.6
DC1,380.8,1.63,620,90.21,50a0,4009
DC2,274.6,1.42,391,90.07,50e0,c00a
DC3,0.0,0.00,0,88.50,53a0,0003
AC1,227.8,1.42,310,83.36,5ca0
AC2,228.8,1.43,313,90.00,5100
AC3,228.0,1.45,313,89.93,5120";
        } else {
            return trim($this->execute(' -csv -q'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return KostalPikoConverter::toLive($data);
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
    	return array("result"=>false, "testData"=>"Not yet implemented");
    }

    private function execute($options) {
    	$exec = shell_exec($this->PATH . ' ' . $options);
    	HookHandler::getInstance()->fire("onDebug", print_r($exec,true));
		return $exec;
    }
}
?>