<?php
error_reporting(E_ALL);

define('checkaccess', TRUE);

$docRoot = dirname(dirname(__FILE__));
require_once $docRoot . '/admin/classes/classloader.php';
Session::initialize();

HookHandler::getInstance()->fire("onInverterShutdown", Session::getConfig()->getInverterConfig(1));

$test = new WeatherAddon();
$config = Session::getConfig();
$woeid = $test->getCityCode($config->latitude, $config->longitude);
?>
<pre>
<?php echo (htmlentities($test->getWeather($woeid))); ?>
</pre>
<pre>
<?php 
echo(file_get_contents(dirname(dirname(__FILE__)) . "/log/debug.log"));
?>
</pre>