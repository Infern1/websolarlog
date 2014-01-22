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
    public $state;
    public $refreshTime;
    public $historyRate;
    public $active;

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
    function __construct()
    {
        $this->id = -1;
        $this->name = "";
        $this->description = "";
        $this->initialkwh = 0;
        $this->expectedkwh = 0;
        $this->plantpower = 0;
        $this->comAddress = '2';
        $this->comLog = false;
        $this->syncTime = false;
        $this->pvoutputWSLTeamMember=false;
        $this->pvoutputEnabled = false;
        $this->panels = array();
        $this->state = 0;
        $this->historyRate = 300;
        $this->refreshTime = 2;
    }

    function getApi(Config $config) {
    	$api = null;
        if ($this->deviceApi == "AURORA") {
            $api = new Aurora($config->aurorapath, $this->comAddress, $config->comDebug);
        }
    	if ($this->deviceApi == "SMA-RS485") {
	    	$api = new Sma($config->smagetpath, $this->comAddress, $config->comDebug);
	    }
    	if ($this->deviceApi == "SMA-BT") {
	    	$api = new SMABlueTooth($config->smaspotpath, $this->comAddress, $config->comDebug);
	    }
    	if ($this->deviceApi == "SMA-BT-WSL") {
	    	$api = new SMASpotWSL($config->smaspotWSLpath, $this->comAddress, $config->comDebug);
	    }
	    if ($this->deviceApi == "Diehl-ethernet") {
	    	$api = new Diehl($config->smagetpath, $this->comAddress, $config->comDebug);
	    }
	    if ($this->deviceApi == "DutchSmartMeter") {
	    	$api = new SmartMeter($config->smartmeterpath, $this->comAddress, $config->comDebug);
	    }
    	if ($this->deviceApi == "DutchSmartMeterRemote") {
	    	$api = new SmartMeterRemote($config->smartmeterpath, $this->comAddress, $config->comDebug);
	    }
    	if ($this->deviceApi == "Mastervolt") {
	    	$api = new MasterVolt($config->mastervoltpath, $this->comAddress, $config->comDebug);
	    }
	    if ($this->deviceApi == "SoladinSolget") {
	    	$api = new SoladinSolget($config->soladinSolgetpath, $this->comAddress, $config->comDebug);
	    }
    	if ($this->deviceApi == "Open-Weather-Map") {
	    	$api = new WeatherOWM($config->latitude, $config->longitude);
	    }
	    if ($this->deviceApi == "KostalPiko") {
	    	$api = new KostalPiko($config->kostalpikopath, $this->comAddress, $config->comDebug);
	    }

	    // Do we want to use the new communication?
	    if ($config->useNewCommunication){
		    $communicationService = new CommunicationService();
		    $communication = $communicationService->load($this->communicationId);
		    $api->setCommunication($communication, $this);
	    }
	    
	    return $api;
    }
}
?>