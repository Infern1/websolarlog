<?php

class Inverter
{
    public $id;
    public $deviceApi;
    public $type;
    public $name;
    public $liveOnFrontend;
    public $graphOnFrontend;
    public $description;
    public $initialkwh;
    public $expectedkwh;
    public $plantpower;
    public $heading;
    public $correctionFactor;
    public $comAddress;
    public $comLog;
    public $syncTime;
    public $pvoutputEnabled;
    public $pvoutputApikey;
    public $pvoutputSystemId;
    public $state;

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
        $this->heading = "";
        $this->correctionFactor = 0.987;
        $this->comAddress = 2;
        $this->comLog = false;
        $this->syncTime = false;
        $this->pvoutputEnabled = false;
        $this->panels = array();
        $this->state = 0;
    }

    function getApi($config) {
    	//echo "\r\n getApi\r\n";
    	//echo "\r\nApifound:".$this->deviceApi."\r\n";
        if ($this->deviceApi == "AURORA") {
            return new Aurora($config->aurorapath, $this->comAddress, $config->comPort, $config->comOptions, $config->comDebug);
        }
    	    if ($this->deviceApi == "SMA-RS485") {
	    	return new Sma($config->smagetpath, $this->comAddress, $config->comPort, $config->comOptions, $config->comDebug);
	    }
	    if ($this->deviceApi == "DutchSmartMeter") {
	    	//echo "\r\nMake DutchSmartMeter object\r\n";
	    	return new SmartMeter($config->smartmeterpath, $this->comAddress, $config->comPort, $config->comOptions, $config->comDebug);
	    }
	    if ($this->deviceApi == "DutchSmartMeterRemote") {
	    	//echo "\r\nMake DutchSmartMeterRemote object\r\n";
	    	return new SmartMeterRemote($config->smartmeterpath, $this->comAddress, $config->comPort, $config->comOptions, $config->comDebug);
	    }
	        
    }
}
?>