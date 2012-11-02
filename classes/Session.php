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
        if ($username === "admin" && $password === sha1("admin")) {
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
    
    private static $config;
    public static function getConfig() {
    	if (!isset(self::$config) || self::$config == null) {
    		self::$config = PDODataAdapter::getInstance()->readConfig();
    	}
    	return self::$config;
    }
    
    /**
     * Set the language to the give language code
     * @param language
     */
    public static function setLanguage($language) {
    	$locale = $language . ".UTF-8";
    	$domain = "default";
    	
    	setlocale(LC_ALL, $locale);
    	putenv("LC_ALL=".$locale);
    	bindtextdomain($domain, "./locale");
    	bind_textdomain_codeset($domain, 'UTF-8');
    	textdomain($domain);
    }
}
?>