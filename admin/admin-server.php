<?php
require_once("classes/classloader.php");

$config = new Config();

// Retrieve action params
$method = $_GET['method'];
$settingstype = $_GET['s'];

switch ($settingstype) {
    case 'communication';
        $data['comPort'] = $config->comPort;
        $data['comOptions'] = $config->comOptions;
        $data['comDebug'] = $config->comDebug;
        break;
    case 'email':
        $data['emailFrom'] = $config->emailFrom;
        $data['emailTo'] = $config->emailTo;
        $data['emailAlarms'] = $config->emailAlarms;
        $data['emailEvents'] = $config->emailEvents;
        $data['emailReports'] = $config->emailReports;
        break;
    case 'general':
        $data['title'] = $config->title;
        $data['subtitle'] = $config->subtitle;
        $data['location'] = $config->location;
        $data['latitude'] = $config->latitude;
        $data['longitude'] = $config->longitude;
        break;
    case 'inverters':
        $data['inverters'] = $config->inverters;
        break;
    case 'inverter':
        $inverterId = $_GET['id'];
        foreach ($config->inverters as $inverter) {
            if ($inverter->id == $inverterId) {
                $data['inverter'] = $inverter;
            }
        }
        break;
    case 'test':
        $data['test'] = checkSQLite();
        break;
}



// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// Output the result
try {
    echo json_encode($data);
} catch (Exception $e) {
    echo "error: <br/>" . $e->getMessage() ;
}

exit();

/**
 * This function will check if sqlite is installed an which version
 * It also returns other available drivers
 * @return array
 */
function checkSQLite() {
    $result = array();
    $result['sqlite'] = false;
    $result['available_drivers'] = PDO::getAvailableDrivers();
    // Check if sql lite is installed
    foreach ($result['available_drivers'] as $driver) {
        if ($driver == 'sqlite') {
            $result['sqlite'] = true;
        }
    }

    // Try to get the sqlite version if installed
    if ($result['sqlite'] === true) {
        $filename = tempnam(sys_get_temp_dir(), 'empty'); // use a temporary empty db file for version check
        $conn = new PDO('sqlite:' . $filename);
        $result['sqlite_version'] = $conn->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
        $conn = null; // Close the connection and free resources
    }
    
    // Check if the following extensions are installed/activated
    $checkExtensions = array('curl','sqlite','sqlite3','json','calendar');
    foreach ($checkExtensions as $extension) {
    	$extensions[] = Util::checkIfModuleLoaded($extension);
    }
    
    $result['extensions'] = $extensions;
    return $result;
}
?>