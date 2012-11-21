<?php
class FileUtil {

    /**
     * Returns the path to last changed file in a directory
     * @param string $dir
     * @param string $suffix
     * @return string
     */
    public static function getLastChangedFileFromDir($dir, $suffix = "") {
        $timestamp = 0;
        $path = "";
        $di = new DirectoryIterator($dir);
        foreach ($di as $fileinfo) {
            if (!$fileinfo->isDot()) {
                if ($suffix == "" || Common::endsWith($fileinfo->getFilename, $suffix)) {
                    if ($fileinfo->getMTime() > $timestamp) {
                        // current file has been modified more recently
                        // than any other file we've checked until now
                        $timestamp = $fileinfo->getMTime();
                        $path = $dir . "/" . $fileinfo->getFilename();
                    }
                }
            }
        }
        return $path;
    }
    
    /**
     * Encodes an object to json and writes it to the give path
     * @param string $filepath
     * @param object $object
     * @param string $mode
     */
    public static function writeObjectToJsonFile($filepath, $object, $mode = 'w') {
    	try {
	    	$fp = fopen($filepath, 'w');
	    	fwrite($fp, json_encode($object));
	    	fclose($fp);
    	} catch (Exception $e) {
    		HookHandler::getInstance()->fire("onError", "writeObjectToJsonFile: " . $e->getMessage());
    	}
    }
}
?>