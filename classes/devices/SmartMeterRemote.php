<?php

Class SmartMeterRemote implements DeviceApi {

    ///////////////////////////////////////////////////
    //
	//  Name:	no additional software required
    //  url:	--
    //  author: --
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
        // not supported
    }

    public function getData() {
        if ($this->debug) {
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
        $data = explode("\n", $this->getData());
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

    public function doCommunicationTest() {
        $result = false;
        $data[0] = $this->getData();
        $testData  = explode("\n", $data[0]);
        
        $data[1] = SmartMeterConverter::toLiveSmartMeter($testData);
        if ($data) {
            $result = true;
        }
        return array("result" => $result, "testData" => $data);
    }

    private function execute() {
        $server = $this->communication->uri;
        $port = $this->communication->port;
        $timeout = $this->communication->timeout;

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket < 0) {
            HookHandler::getInstance()->fire("onError", "Could not create socket: " . socket_strerror(socket_last_error($socket)));
            return;
        }
        if (!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $timeout, "usec" => 0))) {
            HookHandler::getInstance()->fire("onWarning", "Could not set socket timeout: " . socket_strerror(socket_last_error($socket)));
        }
        if (!@socket_connect($socket, $server, $port)) {
            HookHandler::getInstance()->fire("onError", "Could not create connection: " . socket_strerror(socket_last_error($socket)));
            return;
        }
        $result = socket_read($socket, '1024');
        socket_close($socket);
        return $result;
    }

}

?>