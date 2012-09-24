<?php
class Config
{
    public $title;
    public $subtitle;
    public $location;
    public $latitude;
    public $longitude;

    public $comPort;
    public $comOptions;
    public $comDebug;

    public $emailFromName;
    public $emailFrom;
    public $emailTo;
    public $emailAlarms;
    public $emailEvents;
    public $emailReports;

    public $smtpServer;
    public $smtpPort; // 25 (default), 587 (alternative), 465 (ssl)
    public $smtpSecurity; // none, ssl, tls
    public $smtpUser;
    public $smtpPassword;

    public $inverters; // Contains an array of inverters

	/**
	 * Constructor
	 */
	function __construct()
	{
		// ### DATABASE CONFIG
		$this->basepath = dirname(dirname(__FILE__));
		$this->dbType='sqlite';
		$this->dbHost= $this->basepath.'/database/wsl.sdb';
		$this->dbDatabase='wsl';
		$this->dbUser='wsl';
		$this->dbPassword='wsl';
		$this->dbPort='0';

		// ### GENERAL
		$this->title='WebSolarLog';
		$this->subtitle='--== Sun <-> Energy ==--';
		$this->location='Home Sweet Home';
		$this->latitude = '52.061152';
		$this->longitude = '4.493330';

		// ### Communication
		$this->comPort = "/dev/ttyUSB0";
		$this->comOptions = "-Y3 -l3 -M3";
		$this->comDebug = false;

		// ### Create 2 inverter config for testing multi inverter config
		$this->inverters = array();

		// ### FRONT PAGE
		$this->YMAX='4600';
		$this->YINTERVAL='1000';
		$this->PRODXDAYS='20';

		// ### EXPECTED PRODUCTION
		$this->EXPECTEDPROD='3400';
		$this->EXPECTJAN='2.4';
		$this->EXPECTFEB='3.6';
		$this->EXPECTMAR='8.1';
		$this->EXPECTAPR='12.3';
		$this->EXPECTMAY='13.9';
		$this->EXPECTJUN='14.35';
		$this->EXPECTJUI='13.65';
		$this->EXPECTAUG='11.8';
		$this->EXPECTSEP='9.1';
		$this->EXPECTOCT='5.9';
		$this->EXPECTNOV='3';
		$this->EXPECTDEC='1.9';

		// ### EMAIL
		$this->emailFrom = "test-from@test.localhost";
		$this->emailTo = "test-to@test.localhost";
		$this->emailAlarms = true;
		$this->emailEvents = true;
		$this->emailReports = false;
	}

	function getInverterConfig($inverterId) {

	}
}
?>