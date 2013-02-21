<?php
class Updater {
    public static $url = "http://svn.code.sf.net/p/websolarlog/code/";
    public static $problems = array();
    public static $basepath = "../tmp";

    function __construct() {
    }

    function __destruct() {
    }

    /**
     * Checks if we can do auto update
     */
    public static function isUpdateable(){
        self::$problems = array(); // Reset the problems
        $result = true;
        if (!extension_loaded('svn')) {
            self::$problems[] = "SVN module not found. Try to install php5-svn package under debian.";
            $result = false;
        }
        return $result;
    }


    /**
     * Retrieves the available versions
     * @param boolean $experimental
     * @return array
     */
    public static function getVersions($experimental = false) {
        $tags = svn_ls(self::$url . "tags");
        $versions = array();
        foreach ($tags as $tag) {
            if ($experimental) {
                $versions[] = array('name'=>$tag['name'],'revision'=>$tag['created_rev'],'experimental'=>true);
            } else {
                if (Common::startsWith($tag['name'], "release") || Common::startsWith($tag['name'], "stable")) {
                    $versions[] = array('name'=>$tag['name'],'revision'=>$tag['created_rev'], 'experimental'=>false);
                }
            }
        }
        if ($experimental) {
            $trunk = svn_ls(self::$url);
            $versions[] = array('name'=>'trunk','experimental'=>true, 'revision'=>$trunk['trunk']['created_rev']);
        }

        return $versions;
    }

    /**
     * Checks/Creates the folders
     * return boolean
     */
    public static function prepareCheckout() {
        if (!Common::checkPath(self::$basepath)) {
            return false;
        }

        // We need to have an temp folder if it exist we need to remove it
        if (is_dir(self::$basepath . "/export")) {
            Common::rrmdir(self::$basepath . "/export");
        }

        // Try to create the temp folder
        return Common::checkPath(self::$basepath . "/export");
    }

    /**
     * Do a checkout off the given version
     * @param string $urlsvn
     * @return boolean
     */
    public static function doCheckout($urlsvn) {
        // Try to do an export
        return svn_export ($urlsvn, self::$basepath . "/export", false );
    }

    /**
     * Deploy the version checkedout
     */
    public static function copyToLive() {
        // We dont want to copy everything, so specify which dirs we dont want
        $skipDirs = Array( "data", "database", "scripts", "tmp" );
        $source = self::$basepath . "/export/";
        $target = "../";

        foreach (scandir($source) as $file) {
            // Skip files we can read and the dot(dot) folders
            if (!is_readable($source.$file) || $file == '.' || $file == '..') continue;
            if (is_dir($source.$file) && !in_array($file, $skipDirs) ) {
                // Remove the target dir before updating it
                Common::rrmdir($target . $file);

                // Make sure the target dir is available, always create it
                Common::checkPath($target . $file);

                // Copy all files over
                Common::xcopy($source . $file, $target . $file);
            }
            if (is_file($source.$file))  {
                copy($source . $file, $target . $file);
            }
        }

        // Update a few script files
        copy($source . "scripts/worker.php", $target . "scripts/worker.php");
        copy($source . "scripts/wsl.sh", $target . "scripts/wsl.sh");
        chmod($target . "scripts/wsl.sh", "750");
    }
}