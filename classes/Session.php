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

        if ($username === "admin" && $password === "admin") {
            $_SESSION['userid'] = session_id();
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
            if ($_SESSION['userid'] != '' && $_SESSION['username'] != '') {
                $result = true;
            }
        }
        return $result;
    }
}
?>