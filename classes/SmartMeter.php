<?php
Class SmartMeter implements DeviceApi {
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
    	// not supported
    }

    public function getData() {
        if ($this->DEBUG) {
            //return $this->execute('-b -c -T ' . $this->COMOPTION . ' -d0 -e 2>'. Util::getErrorFile($this->INVTNUM));
            return '/XMX5XMXABCE000024595

0-0:96.1.1(22222222222222222222222222222222)
1-0:1.8.1(01192.830*kWh)
1-0:1.8.2(00789.784*kWh)
1-0:2.8.1(00234.992*kWh)
1-0:2.8.2(00572.925*kWh)
0-0:96.14.0(0002)
1-0:1.7.0(0000.47*kW)
1-0:2.7.0(0000.00*kW)
0-0:17.0.0(999*A)
0-0:96.3.10(1)
0-0:96.13.1()
0-0:96.13.0()
0-1:96.1.0(1111111111111111111111111111111111)
0-1:24.1.0(03)
0-1:24.3.0(130116180000)(00)(60)(1)(0-1:24.2.0)(m3)
(00348.414)
0-1:24.4.0(1)
!';
        } else {
            return trim($this->execute());
        }
    }
    
    public function getLiveData() {
    	//echo "\r\ngetLiveData\r\n";
    	$data = explode("\n",$this->getData());
    	//echo "\r\ndata:".var_dump($data)."\r\n";
    	return SmartMeterConverter::toLiveSmartMeter($data);
    }

    public function getInfo() {
        // not supported
    }

    public function getHistoryData() {
        //return $this->execute('-k');
        // not supported
    }

    public function syncTime() {
        //return $this->execute('-L');
        // not supported
    }

    private function execute() {    	
        return shell_exec('sudo python3 '.$this->PATH.' /dev/serial/by-id/usb-Prolific_Technology_Inc._USB-Serial_Controller_D-if00-port0');
    }
}
?>