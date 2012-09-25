<?php
include "classes/classloader.php";

$config = new Config;
R::setup('sqlite:'.$config->dbHost );
R::debug(false);



// Test the isPeriodJob
while (true) {
    if (PeriodHelper::isPeriodJob('1min', 1)) {
        echo ('1minjob: ' . date("H:i:s", time()) ."\n");
    }
    if (PeriodHelper::isPeriodJob('5min', 5)) {
        echo ('5minjob: ' . date("H:i:s", time()) ."\n");
    }
    sleep(10);
}



?>