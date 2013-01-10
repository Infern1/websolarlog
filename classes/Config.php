<?php
class Config
{
    public $version_title;
    public $version_revision;

    public $title;
    public $subtitle;
    public $url;
    public $location;
    public $latitude;
    public $longitude;
    public $timezone;

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

    public $template;
    public $basePath;
    public $inverters; // Contains an array of inverters

    public $co2kwh;
    public $debugmode;

    public $aurorapath; // The path to aurora
    public $smagetpath; // The path to sma-get
    public $smartmeterpath;
    
    public $adminpasswd;

    public $urlDir;
    public $urlComplete;
    
    public $dropboxKey;
    public $dropboxSecret;
    
    public $googleAnalytics;
    public $piwikServerUrl;
    public $piwikSiteId;
    
    public $hybridAuth;
    
	/**
	 * Constructor
	 */
	function __construct()
	{
		// Disable for production switch to true to get debug logging
		$this->debugmode = false;
		
		
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
		$this->url=Common::getDomain();
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

		// ### EMAIL
		$this->emailFrom = "test-from@test.localhost";
		$this->emailTo = "test-to@test.localhost";
		$this->emailAlarms = true;
		$this->emailEvents = true;
		$this->emailReports = false;
		
		// ## DROPBOX
		$this->dropboxKey = 'phrjcc77h8am0ch';
		$this->dropboxSecret = 'uxaakmr5iz5x4m4';
		$this->dropboxCallback = '#backup';
		
		$this->template = 'green'; // Default selected template

		$this->aurorapath = 'aurora'; // If in system path this is enough
		$this->smagetpath = 'sma_get'; // If in system path this is enough
		
		$this->co2kwh = 440; // 440g/kWh is conform europa average
		
		$this->adminpasswd = sha1('admin');
		

	}

	function getInverterConfig($inverterId) {
		foreach ($this->inverters as $inverter) {
			if ($inverter->id == $inverterId) {
				return $inverter;
			}
		}
	}
}
?>