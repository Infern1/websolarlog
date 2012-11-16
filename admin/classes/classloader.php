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
function wsl_autoloader($classname)
{
    global $current_module;
    $docRoot = dirname(dirname(dirname(__FILE__)));
        
    if ($classname == "") {
        exit("Could not autoload empty classname!");
    }

	// Gather the dirs we need to check
	$classdirs = Array( "/classes", "/classes/objects", "/classes/converters", "/addon" );

    foreach ($classdirs as $classdir) {
        // Check the domain model
        $filename = $docRoot . $classdir . "/" . $classname . ".php";
        if (file_exists($filename)) {
            require_once($filename);
            return;
        }
    }

    // plugins
    if ($classname === "R" || substr($classname, 0, strlen("RedBean")) === "RedBean") {
        require_once $docRoot."/classes/redbean.php";
        return;
    }
    if (substr($classname, 0, strlen("Model")) === "Model") {
		// We don't handle Model classes, let RedBean do that
        return;
    }
    if ($classname === "PHPMailer") {
        require_once $docRoot."/classes/phpmailer/class.phpmailer.php";
        require_once $docRoot."/classes/phpmailer/class.smtp.php";
        return;
    }

    exit("ClassLoader::Admin::Could not autoload: " . $classname. ", " .$docRoot. " is used as documentRoot");
}

spl_autoload_register('wsl_autoloader');
?>