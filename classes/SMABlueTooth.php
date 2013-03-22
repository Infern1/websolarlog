<?php
Class SMABlueTooth implements DeviceApi {
    private $ADR;
    private $PORT;
    private $COMOPTION;
    private $DEBUG;
    private $PATH;

    function __construct($path, $address, $port, $comoption, $debug) {
        $this->ADR = $address;
        $this->PORT = $port;
        $this->COMOPTION = $comoption;
        $this->DEBUG = true;
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
    		return $this->execute('-a');
    	}

    }

    public function getData() {
        if ($this->DEBUG) {
            //return $this->execute('-b -c -T ' . $this->COMOPTION . ' -d0 -e 2>'. Util::getErrorFile($this->INVTNUM));
            return "SMAspot V2.0.4
Yet another tool to read power production of SMA solar inverters
(c) 2012-2013, SBF (http://code.google.com/p/sma-spot)

Commandline Args: -v
sunrise: 19/03/2013 06:47:00
sunset : 19/03/2013 18:50:00
Connecting to 00:80:25:2A:3C:DF (1/10)
Initializing...
SMA netID=01
Serial Nr: 7E5FCEF6 (2120208118)
BT Signal=66%
Logon OK
Local PC Time: 19/03/2013 14:09:39
Inverter Time: 19/03/2013 14:09:33
Time diff (s): 6
TZ offset (s): 3600
Device Name:      SN: 2120208118
Device Class:     Solar Inverters
Device Type:      SB2000HF-30
Software Version: 02.30.07.R
Serial number:    2120208118
Device Status:      OK
Energy Production:
        EToday: 1.839kWh
        ETotal: 100.310kWh
        Operation Time: 293.55h
        Feed-In Time  : 255.86h
DC Spot Data:
        String 1 Pdc: 102.237kW - Udc:  27.84V - Idc:121.891A
        String 2 Pdc:   0.000kW - Udc:   0.00V - Idc:  0.000A
AC Spot Data:
        Phase 1 Pac :   0.217kW - Uac: 233.79V - Iac:  0.929A
        Phase 2 Pac :   0.000kW - Uac:   0.00V - Iac:  0.000A
        Phase 3 Pac :   0.000kW - Uac:   0.00V - Iac:  0.000A
        Total Pac   :   0.217kW
Grid Freq. : 50.02Hz
Current Inverter Time: 19/03/2013 14:09:33
Inverter Wake-Up Time: 19/03/2013 06:54:23
Inverter Sleep Time  : 19/03/2013 14:09:33
ExportSpotDataToCSV()
********************
* ArchiveDayData() *
********************
startTime = 51479C70 -> 19/03/2013 00:00:00
ExportDayDataToCSV()
**********************
* ArchiveMonthData() *
**********************
startTime = 51308A30 -> 01/03/2013 12:00:00
ExportMonthDataToCSV()
Done.";
        } else {
            return trim($this->execute('-v'));
        }
    }
    
    public function getLiveData() {
    	$data = $this->getData();
    	return SMABlueToothConverter::toLive($data);
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