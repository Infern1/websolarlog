<?php
Class MasterVolt implements DeviceApi {
    private $ADR;
    private $PORT;
    private $COMOPTION;
    private $DEBUG;
    private $PATH;

    function __construct($path, $address, $port, $comoption, $debug) {
        $this->ADR = $address;
        $this->PORT = $port;
        $this->COMOPTION = $comoption;
        $this->DEBUG = $debug;
        $this->PATH = $path;
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
            echo("GetAlarms Mastervolt\n");
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->DEBUG) {
            //return $this->execute('-b -c -T ' . $this->COMOPTION . ' -d0 -e 2>'. Util::getErrorFile($this->INVTNUM));
            return "451 1,38 622 233 2,55 580 50,00 42 13,33 748 NoError";
        } else {
            //echo("GetData Mastervolt\n");
        	return trim($this->execute( $this->COMOPTION ));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
        //echo("GetLiveData Mastervolt\n");
    	return MasterVoltConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->DEBUG) {
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
        //$result = $this->execute('-k366 -Y60'); // -K10 is not yet supported by aurora
        
        //if ($result) {
        //	HookHandler::getInstance()->fire("onDebug", "getHistoryData :: start processing the result");
        //	$deviceHistoryList = array();
        //	$lines = explode("\n", $result);
        //	foreach ($lines as $line) {
        //		$deviceHistory = AuroraConverter::toDeviceHistory($line);
        //		if ($deviceHistory != null) {
        //			$deviceHistory->amount = $deviceHistory->amount * 10; // Remove this line when -K10 is supported
        //			$deviceHistoryList[] = $deviceHistory;
        //		}
        //	}
        //	return $deviceHistoryList;
        //} else {
        //	if (Session::getConfig()->debugmode) {
        //		HookHandler::getInstance()->fire("onDebug", "getHistoryData :: nothing returned by inverter result=" + $result);
        //	}
        //}
        //
        //return null;
    }

    public function syncTime() {
        //echo("syncTime Mastervolt\n");
    	//return $this->execute('-L');
        return null;
    }
    
    public function doCommunicationTest() {
    	return "Not yet implemented";
    }

    private function execute($options) {
    	//echo("Path ".$this->PATH." ADR: ".$this->ADR." Options: ".$options." Port: ".$this->PORT." \n");
        $cmd = $this->PATH." ".$this->ADR ;
      //  echo("MasterVolt support is not yet ready.\n");
        //echo("This should be the command executed cmd=".$cmd." \n");
        
        
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
