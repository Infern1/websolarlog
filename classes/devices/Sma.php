<?php
Class Sma implements DeviceApi {

	///////////////////////////////////////////////////
	//
	//  Name:	SMA-get
	//  url:	https://code.google.com/p/sma-get/
	//  author:	Roland Breedveld
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
    		return $this->execute('-a');
    	}

    }

    public function getData() {
        if ($this->debug) {
            return date("Ymd")."-11:11:11 233.188904 6.021501 1404.147217 234.981598 5.776632 1357.402222 242.095657 10.767704 2585.816406 59.966419 93.636436 68.472496 41.846001 3.230 8441.378 0.000 8384.237 12519.938 14584.0 84 236.659 OK";
        } else {
            return trim($this->execute('-d'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return AuroraConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->debug) {
            return "SMA XXXXXX.XXXXXXXX";
        } else {
           return $this->execute('-i');
        }
    }

    public function getHistoryData() {
        //return $this->execute('-k');
        // not supported
    }

    public function syncTime() {
        //return $this->execute('-L');
        // not supported
    }
    
    public function doCommunicationTest() {
    	$result = false;
    	$data = $this->execute('-i -v');
    	if ($data) {
    		$result = true;
    	}
    	
    	return array("result"=>$result, "testData"=>$data);
    }

    private function execute($options) {
        $cmd = $this->communication->uri . ' -n' . $this->device->comAddress . ' ' . $this->communication->optional . ' ' . $options . ' ' . $this->communication->port;
        return shell_exec($cmd);
    }

}

?>