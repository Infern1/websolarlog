<?php
Class Diehl implements DeviceApi {
    private $ADR;
    private $PORT;
    private $COMOPTION;
    private $DEBUG;
    private $PATH;

    /**
		***************
		Page:
			Statistics
		Form Data:
			{"jsonrpc":"2.0","method":"GetPowerLog","params":[1,17,"2013-02-14 00:00:00","kWh","1h",168],"id":0}:
		response:
			{
			"jsonrpc":	"2.0",
			"result":	{
				"TimeDate":	"2013-02-14 00:00:00",
				"Unit":	"kWh",
				"Resolution":	"1h",
				"Values":	[0, 0, 0, 0, 0, 0, 0, 0, 0, 0.001000, 0.039000, 0.042000, 0.045000, 0.046000, 0.041000, 0.044000, 0.015000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.029000, 0.035000, 0.087000, 0.111000, 0.076000, 0.062000, 0.062000, 0.058000, 0.031000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.020000, 0.110000, 0.137000, 0.142000, 0.334000, 0.345000, 0.287000, 0.179000, 0.069000, 0.026000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.047000, 0.272000, 0.405000, 0.667000, 1.027000, 1.181000, 0.546000, 0.427000, 0.214000, 0.039000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.026000, 0.189000, 0.424000, 0.825000, 1.295000, 0.959000, 0.379000, 0.390000, 0.091000, 0.020000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.023000, 0.087000, 0.263000, 0.271000, 0.216000, 0.238000, 0.191000, 0.071000, 0.098000, 0.024000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.036000, 0.074000, 0.322000, 0.526000, 1.285000, 1.694000, 0.505000, 0.400000, 0.192000, 0.067000, 0.022000, 0, 0, 0, 0, 0],
				"Index":	2182,
				"Total":	17.056999,
				"Saving":	166,
				"CO2Cut":	11
				},
				"Id":	0
			}
		
		***************
		Page:
			Statistics
		Form Data:
			{"jsonrpc":"2.0","method":"GetPowerLog","params":[1,17,"2012-03-01 00:00:00","kWh","1mnd",12],"id":0}:
		response:
			{
			"jsonrpc":	"2.0",
			"result":	{
				"TimeDate":	"2012-03-01 00:00:00",
				"Unit":	"kWh",
				"Resolution":	"1mnd",
				"Values":	[0, 0, 0, 0, 0, 0, 0, 0, 0, 13.898000, 17.695999, 34.641998],
				"Index":	5,
				"Total":	66.236000,
				"Saving":	660,
				"CO2Cut":	46
				},
				"Id":	0
			}
			
		
		***************
		Page:
			Statistics; auto updater?!
		Form Data:
			{"jsonrpc":"2.0","method":"GetPowerLog","params":[1,17,"2013-02-21 00:00:00","kWh","15min",34.6],"id":0}:
		response:	
			{
				"jsonrpc":	"2.0",
				"result":	{
					"TimeDate":	"2013-02-21 00:00:00",
					"Unit":	"kWh",
					"Resolution":	"15min",
					"Values":	[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.001000, 0.029000, 0.046000],
					"Index":	2183,
					"Total":	0,
					"Saving":	0,
					"CO2Cut":	0
				},
				"Id":	0
			}
		
		***************
		Page:
			Eventlog
		Form Data:
			{"jsonrpc":"2.0","method":"GetEventPage","params":[1,"2013-02-21 23:59:59",1,14],"id":0}:
			***Other date
			{"jsonrpc":"2.0","method":"GetEventPage","params":[1,"2013-02-01 23:59:59",1,14],"id":0}:
			***Other page 3 selected in page selector:
			{"jsonrpc":"2.0","method":"GetEventPage","params":[1,"2013-02-01 23:59:59",3,14],"id":0}:
		response:	
			{
				"jsonrpc":	"2.0",
				"result":	{
					"Items":	[{
							"EventNo":	1,
							"TimeStamp":	"2013-02-20 09:23:39",
							"Desc":	"System Reset",
							"Event":	"Info:On "
						},
						********** CUT **********
						 {
							"EventNo":	14,
							"TimeStamp":	"2012-12-11 11:28:49",
							"Desc":	"System Reset",
							"Event":	"Info:On "
						}]
				},
				"Id":	0
			}
		
		***************
		Page:
			Status->Plant
		Form Data:
			{"jsonrpc":"2.0","method":"GeteNexusData","params":[{"path":"eNEXUS_0056","datatype":"INT16U"}],"id":0}:
		response:	
			{
				"jsonrpc":	"2.0",
				"result":	[{
						"path":	"eNEXUS_0056",
						"value":	"1"
					}],
				"Id":	0
			}
			
			
		***************
		Page:
			Status->Plant (this is a second request on the same page)
		Form Data:
			{"jsonrpc":"2.0","method":"GeteNexusData","params":[{"path":"eNEXUS_0061[s:1,t:17]","datatype":"INT8U"},{"path":"eNEXUS_0035[s:1,t:17]","datatype":"SERIALNO"},{"path":"eNEXUS_0060[s:1]","datatype":"STRING32"},{"path":"eNEXUS_0002[s:1,t:17]","datatype":"INT8U"},{"path":"eNEXUS_0001[s:1,t:17]","datatype":"INT8U"},{"path":"eNEXUS_0010[s:1,t:17]","datatype":"INT16U"},{"path":"eNEXUS_0040[s:1,t:17,u:4]","datatype":"INT32U"},{"path":"eNEXUS_0041[s:1,t:17,u:4]","datatype":"INT32U"},{"path":"eNEXUS_0043[s:1,t:17,u:4]","datatype":"INT32U"}],"id":0}:
		response:	
			{
				"jsonrpc":	"2.0",
				"result":	[{
						"path":	"eNEXUS_0061[s:1,t:17]",
						"value":	"1"
					}, {
						"path":	"eNEXUS_0035[s:1,t:17]",
						"value":	"122671110228"
					}, {
						"path":	"eNEXUS_0060[s:1]",
						"value":	"Home"
					}, {
						"path":	"eNEXUS_0002[s:1,t:17]",
						"value":	"3"
					}, {
						"path":	"eNEXUS_0001[s:1,t:17]",
						"value":	"1"
					}, {
						"path":	"eNEXUS_0010[s:1,t:17]",
						"value":	"167"
					}, {
						"path":	"eNEXUS_0040[s:1,t:17,u:4]",
						"value":	"123"
					}, {
						"path":	"eNEXUS_0041[s:1,t:17,u:4]",
						"value":	"34765"
					}, {
						"path":	"eNEXUS_0043[s:1,t:17,u:4]",
						"value":	"66359"
					}],
				"Id":	0
			}
		
		***************
		Page:
			Home
		Form Data:
			{"jsonrpc":"2.0","method":"GetPowerLog","params":[1,17,"2013-02-21 00:00:00","kWh","15min",37.93333333333333],"id":0}:
		response:
			{
				"jsonrpc":	"2.0",
				"result":	{
					"TimeDate":	"2013-02-21 00:00:00",
					"Unit":	"kWh",
					"Resolution":	"15min",
					"Values":	[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.001000, 0.029000, 0.046000, 0.078000, 0.122000, 0.161000],
					"Index":	2186,
					"Total":	0,
					"Saving":	0,
					"CO2Cut":	0
				},
				"Id":	0
			}
			
			
		***************
		Page:
			Home
		Form Data:
			{"jsonrpc":"2.0","method":"GetPowerLog","params":[1,17,"2013-02-01 00:00:00","kWh","1day",21],"id":0}:
		response:
			{
				"jsonrpc":	"2.0",
				"result":	{
					"TimeDate":	"2013-02-01 00:00:00",
					"Unit":	"kWh",
					"Resolution":	"1day",
					"Values":	[0.908000, 2.482000, 1.427000, 3.093000, 2.238000, 1.013000, 2.309000, 2.953000, 0.717000, 0.028000, 0.075000, 0.137000, 0.205000, 0.049000, 0.580000, 1.755000, 4.278000, 4.483000, 1.338000, 4.574000, 0.096000],
					"Index":	76,
					"Total":	34.737999,
					"Saving":	337,
					"CO2Cut":	24
				},
				"Id":	0
			}
		
		
		Page:
			Home
		Form Data:
			{"jsonrpc":"2.0","method":"GetPowerLog","params":[1,17,"2013-01-01 00:00:00","kWh","1mnd",2],"id":0}:
		response:
			{
				"jsonrpc":	"2.0",
				"result":	{
					"TimeDate":	"2013-01-01 00:00:00",
					"Unit":	"kWh",
					"Resolution":	"1mnd",
					"Values":	[17.695999, 34.737999],
					"Index":	5,
					"Total":	52.433998,
					"Saving":	523,
					"CO2Cut":	36
				},
				"Id":	0
			}
		
			
		Page:
			Home
		Form Data:
			{"jsonrpc":"2.0","method":"GeteNexusData","params":[{"path":"eNEXUS_0001[s:17,t:1]","datatype":"INT8U"},{"path":"eNEXUS_0002[s:17,t:1]","datatype":"INT8U"},{"path":"eNEXUS_0003[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0004[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0064[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0013[s:17,t:1]","datatype":"INT32S"},{"path":"eNEXUS_0014[s:17,t:1]","datatype":"INT32S"},{"path":"eNEXUS_0015[s:17,t:1]","datatype":"INT32S"},{"path":"eNEXUS_0015[s:17,t:1]","datatype":"INT32S"},{"path":"eNEXUS_0016","datatype":"Date"},{"path":"eNEXUS_0017","datatype":"Time"},{"path":"eNEXUS_0018","datatype":"STRING32"}],"id":0}:
		response:
			{
				"jsonrpc":	"2.0",
				"result":	[{
						"path":	"eNEXUS_0001[s:17,t:1]",
						"value":	"1"
					}, {
						"path":	"eNEXUS_0002[s:17,t:1]",
						"value":	"3"
					}, {
						"path":	"eNEXUS_0003[s:17,t:1]",
						"value":	"0"
					}, {
						"path":	"eNEXUS_0004[s:17,t:1]",
						"value":	"0"
					}, {
						"path":	"eNEXUS_0064[s:17,t:1]",
						"value":	"182"
					}, {
						"path":	"eNEXUS_0013[s:17,t:1]",
						"value":	"129"
					}, {
						"path":	"eNEXUS_0014[s:17,t:1]",
						"value":	"34771"
					}, {
						"path":	"eNEXUS_0015[s:17,t:1]",
						"value":	"52467"
					}, {
						"path":	"eNEXUS_0015[s:17,t:1]",
						"value":	"52467"
					}, {
						"path":	"eNEXUS_0016",
						"value":	{
							"day":	21,
							"month":	2,
							"year":	2013
						}
					}, {
						"path":	"eNEXUS_0017",
						"value":	{
							"hour":	9,
							"minute":	14
						}
					}, {
						"path":	"eNEXUS_0018",
						"value":	"Almere"
					}],
				"Id":	0
			}
		
		
		
Page:
	Home
Form Data:
	{"jsonrpc":"2.0","method":"GeteNexusData","params":[{"path":"eNEXUS_0063[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0006[s:17,t:1]","datatype":"INT16U"},{"path":"eNEXUS_0064[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0065[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0011[s:17,t:1]","datatype":"INT32U"}],"id":0}:
response:
	{
		"jsonrpc":	"2.0",
		"result":	[{
				"path":	"eNEXUS_0063[s:17,t:1]",
				"value":	"509"
			}, {
				"path":	"eNEXUS_0006[s:17,t:1]",
				"value":	"3373"
			}, {
				"path":	"eNEXUS_0064[s:17,t:1]",
				"value":	"182"
			}, {
				"path":	"eNEXUS_0065[s:17,t:1]",
				"value":	"172"
			}, {
				"path":	"eNEXUS_0011[s:17,t:1]",
				"value":	"na",
				"error":	"No responce"
			}],
		"Id":	0
	}
	
			
			
			
			
			
     */
    
    function __construct($path, $address, $port, $comoption, $debug) {
        $this->ADR = $address;
        $this->PORT = $port;
        $this->COMOPTION = $comoption;
        $this->DEBUG = $debug;
        $this->PATH = $path;
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