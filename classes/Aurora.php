<?php
Class Aurora implements DeviceApi {
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
    		return $this->execute('-A -Y 10');
    	}

    }

    public function getData() {
        if ($this->DEBUG) {
            //return $this->execute('-b -c -T ' . $this->COMOPTION . ' -d0 -e 2>'. Util::getErrorFile($this->INVTNUM));
            return date("Ymd")."-11:11:11 233.188904 6.021501 1404.147217 234.981598 5.776632 1357.402222 242.095657 10.767704 2585.816406 59.966419 93.636436 68.472496 41.846001 3.230 8441.378 0.000 8384.237 12519.938 14584.0 84 236.659 OK";
        } else {
            return trim($this->execute('-c -T ' . $this->COMOPTION . ' -d0 -e'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return AuroraConverter::toLive($data);
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
        return $this->execute('-k 366 -Y 60');
    }

    public function syncTime() {
        return $this->execute('-L');
    }

    private function execute($options) {
        $cmd = $this->PATH . ' -a' . $this->ADR . ' ' . $options . ' ' . $this->PORT;
        //return shell_exec($cmd);
        
        $proc=proc_open($cmd,
        		array(
        				array("pipe","r"),
        				array("pipe","w"),
        				array("pipe","w")
        		),
        		$pipes);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        
        /*
        hide errors
        if ($stderr != "") {
        	echo ("error found: " . $stderr . "\n");
        }
        */
        proc_close($proc);
        
        //print stream_get_contents($pipes[1]);
        return trim($stdout);
    }
}
?>