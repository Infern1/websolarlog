<?php
set_time_limit ( 120 ); // 2 minutes
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../admin/classes/classloader.php';

// Initialize adapter and config
$adapter = new PDODataAdapter();
$config = $adapter->readConfig();

echo ("If you get an memory exception, just restart it will continue<br/><br/>");

$tableString = "Energy,History,Event,Pmaxotd";
$tables = explode(",", $tableString);
foreach ($tables as $table) {
    echo ("Converting " . $table . "......... ");
    $beans =  R::find($table, "time = 0 OR time = '' OR time is null");
    $i = 0;
    R::begin();
    foreach ($beans as $bean) {
        if ($bean['time'] == null || $bean['time'] == 0 || trim($bean['time']) == "") {
            $time = Util::getUTCdate($bean['SDTE']);
            if ($time == 0) {
                $time = strtotime(trim($bean['SDTE']));
            }
            $bean['time'] = $time;
            R::store($bean);
            $i++;
            // Flush after 50 lines
            if ($i % 50 == 0) {
                R::commit();
                R::begin();
            }
        }
    }
    R::commit();
    echo (" updated " . $i . " records<br/>");
}

?>