<?php
Class SoladinSolget implements DeviceApi {
	///////////////////////////////////////////////////
	//
	//  Name:	solget
	//  url:	http://www.solget.nl/download.htm
	//  author: Marcel Reinieren
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
            echo("GetAlarms SoladinSolget\n");
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->debug) {
            return "55,9 0,24 49,97 231 10 24 74,5 813,87 382,51 16481:9 3,87 1,81 16481:9 ERROR description";
        } else {
        	return trim($this->execute());
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return SoladinSolgetConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->debug) {
            return "PowerOne XXXXXX.XXXXXXXX";
        } else {
            return "SoladinSolget";
        }
    }

    public function getHistoryData() {
    	// Try to retrieve the data of the last 366 days
        echo("GetHistoryData SoladinSolget\n");
        return "No History Data"; 
    }

    public function syncTime() {
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