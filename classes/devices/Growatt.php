<?php
Class Growatt implements DeviceApi {
	///////////////////////////////////////////////////
	//
	//  Name:	growatt
	//  url:	unknown
	//  author:     unknown
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
            echo("GetAlarms Growatt\n");
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->debug) {
            return "[0, 57, 2280, 0, 0, 57, 0, 0, 0, 0, 0, 52, 4998, 2296, 0, 0, 52, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 654, 23, 19393, 248, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3612, 0, 0]
pv_watts 4.8
pv_volts 228.0
pv_amps 0.0
Onbekend 5.6
out_watts 5.2
ac_hz 49.98
ac_volts 229.6
Onbekend 0.0
Onbekend 46.0
Wh_today 200.0
Wh_total 65400.0
Time_total 230.0
Onbekend 19421.0
Temp_inverter 24.8
Onbekend 3612.0";
        } else {
        	return trim($this->execute());
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return GrowattConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->debug) {
            return "Growatt XXXXXX.XXXXXXXX";
        } else {
            return "Growatt";
        }
    }

    public function getHistoryData() {
    	// Try to retrieve the data of the last 366 days
        return "No History Data"; 
    }

    public function syncTime() {
        return null;
    }
    
    public function doCommunicationTest() {
        $result = false;
        
        $data['RawResponse'] = $this->execute();        
        $data['LiveObject'] = GrowattConverter::toLive($data['RawResponse']);

        if ($data) {
            $result = true;
        }
        return array("result" => $result, "testData" => print_r($data,true));
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