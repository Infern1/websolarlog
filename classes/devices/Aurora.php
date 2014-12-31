<?php
Class Aurora implements DeviceApi {

	///////////////////////////////////////////////////
	//
	//  Name:	Aurora
	//  url:	http://www.curtronics.com/Solar/AuroraData.html
	//  author: Curt Blanke
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
    	return 0; // Try to detect, as it will die when offline
    }

    public function getAlarms() {
    	if ($this->debug) {
            return "W2223424".rand(0,9);
    	} else {
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->debug) {
            return date("Ymd")."-11:11:11 233.188904 6.021501 1404.147217 234.981598 5.776632 1357.402222 242.095657 10.767704 2585.816406 59.966419 93.636436 68.472496 41.846001 3.230 8441.378 0.000 8384.237 12519.938 14584.0 84 236.659 OK";
        } else {
            return trim($this->execute('-c -T -d0 -e'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return AuroraConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->debug) {
            return "PowerOne XXXXXX.XXXXXXXX";
        } else {
           return $this->execute('-p -n -f -g -m -v -Y 10');
        }
    }

    public function getHistoryData() {
    	// Try to retrieve the data of the last 366 days
    	$result = $this->execute('-k366 -Y100'); // -K10 is not yet supported by aurora
        
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
        
        $data['RawResponse'] = $this->getData();        
        $data['LiveObject'] = AuroraConverter::toLive($data['RawResponse']);

        if ($data) {
            $result = true;
        }

        return array("result" => $result, "testData" => print_r($data,true));
    }

    private function execute($options) {
        $cmd = $this->communication->uri . ' -a' . $this->device->comAddress . ' ' . $this->communication->optional . ' ' . $options . ' ' . $this->communication->port;	
        
        $proc=proc_open($cmd,
        		array(
        				array("pipe","r"),
        				array("pipe","w"),
        				array("pipe","w")
        		),
        		$pipes);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        
        proc_close($proc);
        return trim($stdout);
    }
}
?>