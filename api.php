<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require 'classes/classloader.php';
Session::initialize();

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode(ApiController::getInstance()->route());
?>