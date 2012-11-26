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
    public static function getConfig() {
    	if (!isset(self::$config) || self::$config == null) {
    		self::$config = PDODataAdapter::getInstance()->readConfig();
    	}
    	return self::$config;
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
    	$hookHandler->add("onInfo", "LoggerAddon.onInfo");
    	$hookHandler->add("onDebug", "LoggerAddon.onDebug");
    	$hookHandler->add("onInverterStartup", "LoggerAddon.onInverterStartup");
    	$hookHandler->add("onInverterShutdown", "LoggerAddon.onInverterShutdown");
    	$hookHandler->add("onInverterError", "LoggerAddon.onInverterError");
    	
    	// PvOutputAddon
    	$hookHandler->add("on1MinJob", "PvOutputAddon.onJob");
    	
    	// BasicChecksAddon
    	$hookHandler->add("on10MinJob", "BasicChecksAddon.on10MinJob");
    	
    	
    	//$hookHandler->fire("onDebug", "Hooks loaded");
    }
}
?>