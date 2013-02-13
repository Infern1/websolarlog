<?php
class Session
{
    /**
     * Try to login
     * @return boolean
     */
    public static function login() {
        $username = Common::getValue('username', 'none');
        $password = sha1(Common::getValue('password', 'none'));
        if ($username === "admin" && $password === self::getConfig()->adminpasswd) {
            $_SESSION['userid'] = 1;
            $_SESSION['username'] = $username;
            return true;
        }

        return false;
    }

    /**
     * Remove the current session
     */
    public static function logout() {
        unset($_SESSION['userid']);
        unset($_SESSION['username']);
    }

    /**
     * Check if there is an valid login
     * @return boolean
     */

    public static function isLogin() {
        $result = false;
        if (isset($_SESSION['userid']) && isset($_SESSION['username'])) {
            if ($_SESSION['userid'] > 0 && $_SESSION['username'] === 'admin') {
                $result = true;
            }
        }
        return $result;
    }
    
    public static function initialize() {
    	self::setTimezone();
    	self::setLanguage(); 
    	self::registerHooks();
    }
    
    private static $config;
    public static function getConfig($reload=false) {
    	if (!isset(self::$config) || self::$config == null || $reload == true) {
    		self::$config = PDODataAdapter::getInstance()->readConfig();
    	}
    	return self::$config;
    }
    
    /**
     * Retrieves the base path
     * @return string
     */
    public static function getBasePath() {
    	return dirname(dirname(__FILE__));
    }
    
    /**
     * Set the language to the given language code
     */
    public static function setLanguage() {
    	// TODO :: Replace language from dbase
    	$language = "nl_NL"; 
    	
    	$locale = $language . ".UTF-8";
    	$domain = "default";
    	
    	setlocale(LC_ALL, $locale);
    	putenv("LC_ALL=".$locale);
    	bindtextdomain($domain, "./locale");
    	bind_textdomain_codeset($domain, 'UTF-8');
    	textdomain($domain);
    }
    
    /**
     * Sets the time zone
     */
    public static function setTimezone() {
    	ini_set('date.timezone', self::getConfig()->timezone);
    }
    
    /**
     * Register all the addon hooks
     */
    public static function registerHooks() {
    	// TODO :: Get from dbase
    	$hookHandler = HookHandler::getInstance();
    	// MailAddon
    	$hookHandler->add("onError", "MailAddon.onError");
    	$hookHandler->add("onWarning", "MailAddon.onWarning");
    	$hookHandler->add("onInverterStartup", "MailAddon.onInverterStartup");
    	$hookHandler->add("onInverterShutdown", "MailAddon.onInverterShutdown");
    	$hookHandler->add("onInverterError", "MailAddon.onInverterError");
    	$hookHandler->add("onInverterWarning", "MailAddon.onInverterWarning");
    	
    	// LoggerAddon
    	$hookHandler->add("onError", "LoggerAddon.onError");
    	$hookHandler->add("onWarning", "LoggerAddon.onWarning");
    	//$hookHandler->add("onInfo", "LoggerAddon.onInfo");
    	$hookHandler->add("onDebug", "LoggerAddon.onDebug");
    	$hookHandler->add("onInverterStartup", "LoggerAddon.onInverterStartup");
    	$hookHandler->add("onInverterShutdown", "LoggerAddon.onInverterShutdown");
    	$hookHandler->add("onInverterError", "LoggerAddon.onInverterError");
    	
    	// PvOutputAddon
    	$hookHandler->add("onFastJob", "PvOutputAddon.onJob");
    	
    	// TwitterAddon
    	$hookHandler->add("onInverterShutdown", "TwitterAddon.sendTweet");
    	
    	// BasicChecksAddon
    	$hookHandler->add("on10MinJob", "BasicChecksAddon.on10MinJob");
    	
    	// NEW Work Handler hooks
    	$hookHandler->add("onLiveData", "CoreAddon.onLiveData");
    	$hookHandler->add("onLiveSmartMeterData", "SmartMeterAddon.onLiveSmartMeterData");
    	
    	$hookHandler->add("onHistory", "CoreAddon.onHistory");
    	$hookHandler->add("onSmartMeterHistory", "SmartMeterAddon.onSmartMeterHistory");
    	
    	$hookHandler->add("onEnergy", "CoreAddon.onEnergy"); // Will run also if inverter is down
    	$hookHandler->add("onSmartMeterEnergy", "SmartMeterAddon.onSmartMeterEnergy"); // Will run at 00:00
    	
    	
    	$hookHandler->add("GraphDayPoints", "SmartMeterAddon.GraphDayPoints"); // Will run at 00:00
    	
    	
    	$hookHandler->add("newHistory", "CoreAddon.onEnergy"); // Update on every new History
    	$hookHandler->add("onInverterInfo", "CoreAddon.onInverterInfo");
    	$hookHandler->add("onInverterAlarm", "CoreAddon.onInverterAlarm");
    	
    	$hookHandler->add("newLiveData", "BasicChecksAddon.onNewLive");
    	$hookHandler->add("onNoLiveData", "BasicChecksAddon.onNoLiveData");
    	$hookHandler->add("onRegularJob", "BasicChecksAddon.on10MinJob");
    	
    	// fire from frontend
    	$hookHandler->add("checkEnergy", "EnergyCheckAddon.checkEnergy");
    	
    }
}
?>