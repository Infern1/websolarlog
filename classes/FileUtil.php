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
}
?>