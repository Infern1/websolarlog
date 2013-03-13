<?php
class Config
{
	// Database connection string
	public $dbDSN;
	// These are private, else they can be exported to json, use getters to get the values
	private $dbUser;
	private $dbPassword;
	
    public $version_title;
    public $version_revision;

    public $title;
    public $subtitle;
    public $url;
    public $location;
    public $gaugeMaxType;
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
    public $co2gas;
    public $co2CompensationTrees;
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
    
    public $pauseWorker;
    public $restartWorker;
    
    
	/**
	 * Constructor
	 */
	function __construct()
	{
		// Disable for production switch to true to get debug logging
		$this->debugmode = false;
		
		// ### DATABASE CONFIG
		$this->dbDSN= 'sqlite:' . Session::getBasePath() .'/database/wsl.sdb';
		$this->dbUser='';
		$this->dbPassword='';

		// ### GENERAL
		$this->title='WebSolarLog';
		$this->subtitle='--== Sun <-> Energy ==--';
		$this->url=Common::getDomain();
		$this->gaugeMaxType = 'panels';
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
		$this->co2gas = 2200; // 2200g/m3 natural gas is conform europa average
		$this->co2CompensationTree = 27; // 27g Co2 is what a average tree consume a day
		$this->adminpasswd = sha1('admin');
		
		$this->pauseWorker = false;
		$this->restartWorker = false;
	}
	
	public function getDatabaseUser() {
		return $this->dbUser;
	}
	public function setDatabaseUser($user) {
		$this->dbUser = $user;
	}
	
	public function getDatabasePassword() {
		return $this->dbPassword;		
	}

	public function setDatabasePassword($password) {
		$this->dbPassword = $password;		
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