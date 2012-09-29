<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../admin/classes/classloader.php';

// Initialize adapter and config
$adapter = new PDODataAdapter();
$config = $adapter->readConfig();

// Get values from url
$inverterId = Common::getValue("inverterId", 1);


$dataPath = "../data/invt" . $inverterId . "/";

// Read daily data





$dailyPath = $dataPath . "csv/";
foreach (scandir($dailyPath) as $file) {
    if (is_file($dailyPath.$file))  {
        importDailyFile($dailyPath . $file, $inverterId, $adapter);
    }
}

function importDailyFile($src, $inverterId, PDODataAdapter $adapter) {
    // Read all lines
    $lines = file($src);
    $result = array();
    R::begin();
    foreach ($lines as $line) {
        $adapter->addHistory($inverterId, parseCsvToLive($line));
    }
    R::commit();
}

function parseCsvToLive($csv) {
    // Convert comma to dot
    $csv = str_replace(",", ".", $csv);

    $fields = explode(";", $csv);
    $live = new Live();
    //$live->SDTE = Util::getUTCdate($fields[0]) * 1000;
    $live->SDTE = $fields[0];
    $live->I1V = $fields[1];
    $live->I1A = $fields[2];
    $live->I1P = $fields[3];
    $live->I2V = $fields[4];
    $live->I2A = $fields[5];
    $live->I2P = $fields[6];
    $live->GV = $fields[7];
    $live->GA = $fields[8];
    $live->GP = $fields[9];
    $live->FRQ = $fields[10];
    $live->EFF = $fields[11];
    $live->INVT = $fields[12];
    $live->BOOT = $fields[13];
    $live->KWHT = $fields[14];

    return $live;
}



?>