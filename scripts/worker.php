<?php
error_reporting(E_ALL);

define('checkaccess', TRUE);

require_once '../admin/classes/classloader.php';

$worker = new Worker();
$worker->start();
?>