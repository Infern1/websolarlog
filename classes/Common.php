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

    /**
     * Recursively remove a directory
     * @param $dir
     */
    public static function rrmdir($dir) {
        if (!is_dir($dir)) return; // Only handle dirs that exist
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file)) {
                self::rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    /**
     * Recursively copy a directory
     * @param $src
     * @param $dest
     */
    public static function xcopy($src,$dest)
    {
        foreach  (scandir($src) as $file) {
            if (!is_readable($src.'/'.$file) || $file == '.' || $file == '..') continue;
            if (is_dir($src.'/'.$file)) {
                mkdir($dest . '/' . $file);
                self::xcopy($src.'/'.$file, $dest.'/'.$file);
            } else {
                copy($src.'/'.$file, $dest.'/'.$file);
            }
        }
    }


    /**
     * Make sure if the give path is available, else try to create
     *
     * @param string $path
     * @return boolean false if the path is invalid
     */
    public static function checkPath($path)
    {
        // Check if the path is available
        if (!is_dir($path)) {
            if (!mkdir($path)) {
                echo("Could not create: " . $path);
                return false;
            }
        }

        return true;
    }

    /**
     * Sends out an email with the given settings
     * @param string $subject
     * @param string $body
     * @return string|boolean
     */
    public static function sendMail($subject, $body, $config) {
        $mail = new PHPMailer();
        // $mail->SMTPDebug = true; Use this for testing only

        $mail->IsSMTP();  // telling the class to use SMTP
        $mail->Host = $config->smtpServer;
        $mail->Port = $config->smtpPort;
        $mail->FromName = $config->emailFromName;
        $mail->From = $config->emailFrom;
        $mail->AddAddress($config->emailTo);

        if ($config->smtpUser && $config->smtpPassword) {
            $mail->SMTPAuth = true;
            $mail->Username = $config->smtpUser;
            $mail->Password = $config->smtpPassword;
        }

        if (trim($config->smtpSecurity) != "" && trim($config->smtpSecurity != "none")) {
            $mail->SMTPSecure = $config->smtpSecurity;
        }

        $mail->Subject  = $subject;
        $mail->Body     = $body;
        $mail->WordWrap = 50;

        if(!$mail->Send()) {
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }
}
?>