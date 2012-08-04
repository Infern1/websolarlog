<?php
/**
 * Common class file
 *
 * PHP Version 5.2
 *
 * @category  Utils
 * @package   Common
 * @author    Martin Diphoorn <martin@diphoorn.com>
 */

/**
 * Common used functions
 *
 * @category  Utils
 * @package   Common
 * @author    Martin Diphoorn <martin@diphoorn.com>
 * @access    public
 * @since     File available since Release 0.0.0
 */
class Common
{

    /**
     * Returns the value posted, requested or the default if not found.
     *
     * @param string  $name Name of the value
     * @param string  $default default return value when nothing found
     * @param integer $index the index of the value (think about table row lines)
     * @return string
     */
    public static function getValue($name, $default = "", $index = -1)
    {
        // First try to get the post value
        if (isset($_POST[$name]) && $_POST[$name] != "") {
            $value = $_POST[$name];
        } else {
            $value = (isset($_GET[$name]) && $_GET[$name] != "") ? $_GET[$name] : $default;
        }

        if (!is_array($value)) {
            $value = htmlspecialchars($value);
        } else {
            if ($index != -1) {
                $value = $value[$index];
                $value = ($value != "") ? $value : $default;
            }
        }

        return $value;
    }

    /**
     * Try to cast the given object to the class_type
     *
     * @param object &$obj       object we want to cast
     * @param string $class_type type we want to convert to
     * @return
     */
    public static function Cast(&$obj, $class_type)
    {
        if (class_exists($class_type, true)) {
            return unserialize(preg_replace("/^O:[0-9]+:\"[^\"]+\":/i", "O:" . strlen($class_type) . ":\"" . $class_type . "\":", serialize($obj)));
        }
    }

    /**
     * Resolve the domain
     *
     * return string url
     */
    public static function getDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Retrieves the defined constants from the environment
     *
     * @param String $prefix Only return keys with this prefix
     * @return array of key->value pairs
     */
    public static function getDefinedConstants($prefix)
    {
        $found = array();
        foreach (get_defined_constants() as $key => $value) {
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $found[$key] = $value;
            }
        }
        return $found;
    }

    /**
     * Retrieves the IP Address of the client
     *
     * @since 1.0.1
     * @return string
     */
    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Checks if needle is the beginning of the string
     * @param $haystack
     * @param $needle
     * @return boolean
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Checks if needle is the end of the string
     * @param $haystack
     * @param $needle
     * @return boolean
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}
?>