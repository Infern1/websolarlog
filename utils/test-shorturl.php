<?php
error_reporting(E_ALL);

define('checkaccess', TRUE);

$docRoot = dirname(dirname(__FILE__));
require_once $docRoot . '/admin/classes/classloader.php';
Session::initialize();
?>
<pre>
<?php echo (Common::getShortUrl("solar.diphoorn.eu")); ?>
</pre>
