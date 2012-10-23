<?php
class Encryption {
    const CYPHER = MCRYPT_RIJNDAEL_256;
    const MODE   = MCRYPT_MODE_CBC;

    public static function encrypt($plaintext) {
        if (function_exists("mcrypt_module_open")) {
            $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            mcrypt_generic_init($td, self::getKey(), $iv);
            $crypttext = mcrypt_generic($td, $plaintext);
            mcrypt_generic_deinit($td);
            return base64_encode($iv.$crypttext);
        } else {
            return $plaintext;
        }
    }

    public static function decrypt($crypttext) {
        if (function_exists("mcrypt_module_open")) {
            $crypttext = base64_decode($crypttext);
            $plaintext = '';
            $td        = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
            $ivsize    = mcrypt_enc_get_iv_size($td);
            $iv        = substr($crypttext, 0, $ivsize);
            $crypttext = substr($crypttext, $ivsize);
            if ($iv) {
                mcrypt_generic_init($td, self::getKey(), $iv);
                $plaintext = mdecrypt_generic($td, $crypttext);
            }
            return trim($plaintext);
        } else {

        }
    }

    public static function getKey() {
        return md5("@This_Is_An_Very_Secret_Key_For_WSL@");
    }
}
?>