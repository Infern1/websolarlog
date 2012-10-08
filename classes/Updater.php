<?php
class Updater {
    public static $url = "svn://svn.code.sf.net/p/websolarlog/code/";
    public static $problems = array();

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
        $tags = array();
        if ($step == 0) {
            $tags = svn_ls(self::$url . "tags");
        }

        $versions = array();
        foreach ($tags as $tag) {
            if ($experimental) {
                $versions[] = array('name'=>$tag['name'],'experimental'=>true);
            } else {
                if (Common::startsWith($tag['name'], "release")) {
                    $versions[] = array('name'=>$tag['name'],'experimental'=>false);
                } else if(Common::startsWith($tag['name'], "stable")) {
                    $versions[] = array('name'=>$tag['name'],'experimental'=>false);
                }
            }
        }
        if ($experimental) {
            $versions[] = array('name'=>'trunk','experimental'=>true);
        }

        return $versions;
    }

    /**
     * Checks/Creates the folders
     * return boolean
     */
    public static function prepareCheckout() {
        if (!Common::checkPath("temp")) {
            return false;
        }

        // We need to have an temp folder if it exist we need to remove it
        if (is_dir("temp/export")) {
            Common::rrmdir("temp/export");
        }

        // Try to create the temp folder
        return Common::checkPath("temp/export");
    }

    /**
     * Do a checkout off the given version
     * @param string $urlsvn
     * @return boolean
     */
    public static function doCheckout($urlsvn) {
        // Try to do an export
        return svn_export (self::$url , "temp/export", false );
    }

    /**
     * Deploy the version checkedout
     */
    public static function copyToLive() {
        // We dont want to copy everything, so specify which dirs we dont want
        $skipDirs = Array( "data", "database", "updater", "scripts" );
        $source = "temp/export/";
        $target = "../";

        foreach (scandir($source) as $file) {
            // Skip files we can read and the dot(dot) folders
            if (!is_readable($source.'/'.$file) || $file == '.' || $file == '..') continue;
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

        // We skipped the update folder, but we want to update the update script
        copy($source . "updater/update.php", $target . "updater/update.php");
    }
}