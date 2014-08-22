<?php
set_time_limit ( 240 ); // 2 minutes
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../admin/classes/classloader.php';

// Initialize adapter and config
$adapter = PDODataAdapter::getInstance();
$config = Session::getConfig();

// Get values from url
$deviceId = Common::getValue("inverterId", 1);


$dataPath = "../data/invt" . $deviceId . "/";

// Read daily data
$dailyPath = $dataPath . "csv/";
foreach (scandir($dailyPath) as $file) {
    if (is_file($dailyPath.$file))  {
        importDailyFile($dailyPath . $file, $deviceId, $adapter);
    }
}

// Reay energy data
$energyPath = $dataPath . "production/";
foreach (scandir($energyPath) as $file) {
	echo $energyPath."".$file." ".$deviceId;
    if (is_file($energyPath.$file) && Common::startsWith($file, "energy"))  {
        importEnergyFile($energyPath . $file, $deviceId, $adapter);
    }
}

function importDailyFile($src, $deviceId, PDODataAdapter $adapter) {
    $historyService = new HistoryService();
	
    // Read all lines
    $lines = file($src);
    R::begin();
    foreach ($lines as $line) {
    	$live = parseCsvToLive(trim($line, "\n"));
    	$live->deviceId = $deviceId;
        $historyService->save($live->toHistory());
    }
    R::commit();
}

function importEnergyFile($src, $deviceId, PDODataAdapter $adapter) {
    $energyService = new EnergyService();
	
    // Read all lines
    $lines = file($src);
    echo ($src . ": " . count($lines));
    R::begin();
    $kwht = 0; // this only works if you have one file!
    foreach ($lines as $line) {
        $energy = parseCsvToEnergy($deviceId, trim($line, "\n"));
        $kwht += $energy->KWH;
        $energy->KWHT = $kwht;
        $energyService->save($energy);
    }
    R::commit();
}

function parseCsvToLive($csv) {
    // Convert comma to dot
    $csv = str_replace(",", ".", $csv);
    $fields = explode(";", $csv);
    $live = new Live();
    $live->SDTE = $fields[0];
    $live->time = Util::getUTCdate($fields[0]);
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
    
    // Calculate the ratio fields
    $IP = $live->I1P+$live->I2P;
    
    // Prevent division by zero error
    if (!empty($IP)) {
    	$live->I1Ratio = ($live->I1P/$IP)*100;
    	$live->I2Ratio = ($live->I2P/$IP)*100;
    }
    
    return $live;
}

function parseCsvToEnergy($deviceId, $csv) {
    // Convert comma to dot
    $csv = str_replace(",", ".", $csv);
    $fields = explode(";", $csv);

    $energy = new Energy();
    $energy->deviceId = $deviceId;
    $energy->SDTE = $fields[0] . "-05:00:00";
    $energy->time = Util::getUTCdate($fields[0] . "-05:00:00");
    $energy->KWH = $fields[1];
    $energy->KWHT = 0;
    $energy->co2 = Formulas::CO2kWh($energy->KWH, $config->co2kwh);

    return $energy;
}

?>