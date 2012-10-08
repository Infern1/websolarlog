<?php
class Updater {
    private static $url = "svn://svn.code.sf.net/p/websolarlog/code/";
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
}