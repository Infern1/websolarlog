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
}
?>