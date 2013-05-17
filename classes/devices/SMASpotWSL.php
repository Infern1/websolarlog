<?php
Class SMASpotWSL implements DeviceApi {
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
    		return $this->execute('');
    	}

    }

    public function getData() {
        if ($this->DEBUG) {
        	//var_dump(explode(";","WSL_START;datetime:01/05/2013 22:05:47;Pdc1:123,000;Pdc2:234,000;Idc1:1,2300;Idc2:1,2300;Udc1:345,000;Udc2:456,000;Pac1:375,000;Pac2:0,000;Pac3:0,000;Iac1:3,000;Iac2:0,000;Iac3:0,000;Uac1:233,000;Uac2:0,000;Uac3:0,000;PdcTot:357,000;PacTot:340,000;Efficiency:95,600;EToday:21,738;ETotal:9039,226;Frequency:49,990;OperatingTime:9904,874;FeedInTime:9590,518;BT_Signal:64,314;Condition:OK;GridRelay:?;WSL_END"));
            return "WSL_START;01/05/2013 22:05:47;123,000;234,576;1,2300;1,2300;345,032;456,000;375,000;0,000;0,000;3,000;0,000;0,000;233,000;0,000;0,000;357,000;340,000;95,600;21,738;9039,226;49,990;9904,874;9590,518;64,314;OK;?;WSL_END";
            
            //      WSL_START;DateTime;           Pdc1;   Pdc2;   Idc1;  Idc2;  Udc1;   Udc2;   Pac1;   Pac2; Pac3; Iac1; Iac2; Iac3; Uac1;   Uac2; Uac3; PdcTot; PacTot; Efficiency;EToday;ETotal;Frequency;OperatingTime;FeedInTime;BT_Signal;Condition;GridRelay;WSL_END
        } else {
			 /*
			 SMAspot [-scan] [-d#] [-v#] [-ad#] [-am#] [-cfgX.Y] [-u]
			 -scan   Scan for bluetooth enabled SMA inverters.
			 -d#     Set debug level: 0-5 (0=none, default=2)
			 -v#     Set verbose output level: 0-5 (0=none, default=2)
			 -ad#    Set #days for archived daydata: 0-90
			         0=disabled, 1=today (default), ...
			 -am#    Set #months for archived monthdata: 0-60
			         0=disabled, 1=current month (default), ...
			 -cfgX.Y Set alternative config file to X.Y (multiple inverters)
			 -u      Upload to online monitoring system (see config file)
			 -finq   Force Inquiry (Inquire inverter also during the night)
			 -wsl    Special output for WebSolarLog 
			  */
			  return trim($this->execute('-finq -wsl'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return SMASpotWSLConverter::toLive($data);
    }

    public function getInfo() {
        if ($this->DEBUG) {
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

    private function execute($options) {
        return shell_exec($this->PATH . ' ' . $options);
    }

}
?>