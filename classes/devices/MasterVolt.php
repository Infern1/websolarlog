<?php
Class MasterVolt implements DeviceApi {
	
	///////////////////////////////////////////////////
	//
	//  Name:	solget2MV
	//  url:	https://sourceforge.net/projects/solgetmv2wsl/
	//  author: Timo Busch
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
            echo("GetAlarms Mastervolt\n");
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->debug) {
            return "451 1,38 622 233 2,55 580 50,00 42 13,33 748 NoError";
        } else {
            //echo("GetData Mastervolt\n");
        	return trim($this->execute(""));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
        //echo("GetLiveData Mastervolt\n");
    	return MasterVoltConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->debug) {
            return "PowerOne XXXXXX.XXXXXXXX";
        } else {
            return "Mastervolt";
            //echo("GetInfo Mastervolt\n");
        //	return $this->execute('-p -n -f -g -m -v -Y 10');
        }
    }

    public function getHistoryData() {
    	// Try to retrieve the data of the last 366 days
        echo("GetHistoryData Mastervolt\n");
        return "No History Data"; 
    }

    public function syncTime() {
        //echo("syncTime Mastervolt\n");
    	//return $this->execute('-L');
        return null;
    }
    
    public function doCommunicationTest() {
    	$result = false;
    	$data = $this->getData();
    	if ($data) {
    		$result = true;
    	}
    	return array("result"=>$result, "testData"=>$data);
    }

    private function execute($options) {
        $cmd = $this->communication->uri . ' ' . $this->device->comAddress . ' ' . $this->communication->optional . ' ' . $options . ' ' . $this->communication->port;

        $proc=proc_open($cmd,
        		array(
        				array("pipe","r"),
        				array("pipe","w"),
        				array("pipe","w")
        		),
        		$pipes);
        $stdout = stream_get_contents($pipes[1]);
        //$stderr = stream_get_contents($pipes[2]);

        proc_close($proc);
        
        return trim($stdout);
    }
}
?>