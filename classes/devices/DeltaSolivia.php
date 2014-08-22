<?php
Class DeltaSolivia implements DeviceApi {
	
	///////////////////////////////////////////////////
	//
	//  Name:	DeltaPVOutput
	//  url:	https://github.com/stik79/DeltaPVOutput
	//  author: stik79
	//
	///////////////////////////////////////////////////
	
    private $debug;
    private $device;
    private $communication;
    private $SCRIPT_PATH;

    function __construct(Communication $communication, Device $device, $debug = false) {
        $this->communication = $communication;
        $this->device = $device;
        $this->debug = $debug;

        // TODO :: Below line should be set in uri
        $this->SCRIPT_PATH = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DeltaSoliviaPy' . DIRECTORY_SEPARATOR . 'DeltaPVOutput.py';
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
    		 return $this->execute('getalarms');
    	}

    }

    public function getData() {
        if ($this->debug) {
            return date("Ymd")."-11:11:11 233.188904 6.021501 1404.147217 234.981598 5.776632 1357.402222 242.095657 10.767704 2585.816406 59.966419 93.636436 68.472496 41.846001 3.230 8441.378 0.000 8384.237 12519.938 14584.0 84 236.659 OK";
        } else {
            return trim($this->execute('getdata'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return DeltaSoliviaConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->debug) {
            return "PowerOne XXXXXX.XXXXXXXX";
        } else {
           return $this->execute('getinfo');
        }
    }

    public function getHistoryData() {
    	// Delta does not retain history
    	$result = $this->execute('gethistory');
        if (Session::getConfig()->debugmode) {
        	HookHandler::getInstance()->fire("onDebug", "getHistoryData :: nothing returned by inverter result=" + $result);
        }
        
        //return $result;
        return null;
    }

    public function syncTime() {
        return $this->execute('synctime');

    }
    
    public function doCommunicationTest() {
        $result = false;
        $data = $this->execute('gethistory');
    	if (strcasecmp($data,"No response from inverter - shutdown?")!=0) {
    		$result = true;
    	}
    	 
    	return array("result"=>$result, "testData"=>$data);
    }

    private function execute($options) {
        $cmd = 'python '.$this->SCRIPT_PATH.' '.$options.' '.$this->communication->port.' '.$this->device->comAddress;
        
        //echo $cmd;
        $proc=proc_open($cmd,
                        array(
                                        array("pipe","r"),
                                        array("pipe","w"),
                                        array("pipe","w")
                        ),
                        $pipes);
        $stdout = stream_get_contents($pipes[1]);
        //$stderr = stream_get_contents($pipes[2]);
        //echo $stdout;
        proc_close($proc);
        return trim($stdout);
    }
}
?>
