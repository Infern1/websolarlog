<?php

class Device {
    public $id;
    public $deviceApi;
    public $type;
    public $name;
    public $liveOnFrontend;
    public $graphOnFrontend;
    public $graphShowACDC;
    public $description;
    public $initialkwh;
    public $producesSince;
    public $expectedkwh;
    public $plantpower;
    public $communicationId;
    public $comAddress;
    public $comLog;
    public $syncTime;
    public $pvoutputEnabled;
    public $pvoutputApikey;
    public $pvoutputSystemId;
    public $pvoutputWSLTeamMember;
    public $pvoutputAutoJoinTeam;
    public $sendSmartMeterData;
    public $state;
    public $refreshTime;
    public $historyRate;
    public $active;
    public $testMode;

    public $expectedJAN;
    public $expectedFEB;
    public $expectedMAR;
    public $expectedAPR;
    public $expectedMAY;
    public $expectedJUN;
    public $expectedJUL;
    public $expectedAUG;
    public $expectedSEP;
    public $expectedOCT;
    public $expectedNOV;
    public $expectedDEC;

    public $panels;

    /**
     * Constructor
     */
    function __construct() {
        $this->id = -1;
        $this->name = "";
        $this->description = "";
        $this->initialkwh = 0;
        $this->expectedkwh = 0;
        $this->plantpower = 0;
        $this->comAddress = '2';
        $this->comLog = false;
        $this->syncTime = false;
        $this->pvoutputWSLTeamMember = false;
        $this->pvoutputEnabled = false;
        $this->panels = array();
        $this->state = 0;
        $this->historyRate = 300;
        $this->refreshTime = 2;
        $this->sendSmartMeterData = true;
        $this->pvoutputAutoJoinTeam = true;
    }

    function getApi(Config $config) {
        $communicationService = new CommunicationService();
        $communication = $communicationService->load($this->communicationId);

        switch (strtoupper($this->deviceApi)) {
            case "AURORA":
                return new Aurora($communication, $this, $config->comDebug);
            case "SMA-RS485":
                return new Sma($communication, $this, $config->comDebug);
            case "SMA-BT":
                return new SMABlueTooth($communication, $this, $config->comDebug);
            case "SMA-BT-WSL":
                return new SMASpotWSL($communication, $this, $config->comDebug);
            case "DIEHL-ETHERNET":
                return new Diehl($communication, $this, $config->comDebug);
            case "DUTCHSMARTMETER":
                return new SmartMeter($communication, $this, $config->comDebug);
            case "DUTCHSMARTMETERREMOTE":
                return new SmartMeterRemote($communication, $this, $config->comDebug);
            case "SMARTMETERAMPYREMOTE":
                return new SmartMeterAmpyRemote($communication, $this, $config->comDebug);
            case "OMNIK":
                return new Omnik($communication, $this, $config->comDebug);
            case "GROWATT":
                return new Omnik($communication, $this, $config->comDebug);
            case "MASTERVOLT":
                return new MasterVolt($communication, $this, $config->comDebug);
            case "SOLADINSOLGET":
                return new SoladinSolget($communication, $this, $config->comDebug);
            case "DELTASOLIVIA":
                return new DeltaSolivia($communication, $this, $config->comDebug);
            case "OPEN-WEATHER-MAP":
                $api = new WeatherOWM($communication, $this, $config->comDebug);
                $api->setLatLon($config->latitude, $config->longitude); // TODO :: set as communication options???
                return $api;
            case "KOSTALPIKO":
                return new KostalPiko($communication, $this, $config->comDebug);
        }

        HookHandler::getInstance()->fire("onError", "We should never got here, could not load class for API: " . $this->deviceApi);
        return null;
    }
}
?>