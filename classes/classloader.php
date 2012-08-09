<?php
/**
 * classloader file
 *
 * PHP Version 5.2
 *
 * @category  Kernel
 * @package   classloader
 * @author    Martin Diphoorn <martin@diphoorn.com>
 */

/**
 * Try to load the class
 *
 * @param $classname
 *
 * @return void
 */
function __autoload($classname)
{
    global $current_module;

    if ($classname == "") {
        exit("Could not autoload empty classname!");
    }

	// Gather the dirs we need to check
	$classdirs = Array( "classes", "classes/objects" );

    foreach ($classdirs as $classdir) {
        // Check the domain model
        $filename = $classdir . "/" . $classname . ".php";
        if (file_exists($filename)) {
            require_once($filename);
            return;
        }
    }

    // plugins example
    /*
    if ($classname == "TCPDF") {
        require_once "plugins/tcpdf/tcpdf.php";
        return;
    }
    */

    exit("Could not autoload: " . $classname);
}
?>