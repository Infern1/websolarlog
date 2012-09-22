<?php
require_once("classes/classloader.php");

$adapter = new PDODataAdapter();
$config = $adapter->readConfig();

// Retrieve action params
$settingstype = Common::getValue("s", null);

$data = array();
switch ($settingstype) {
    case 'communication';
        $data['comPort'] = $config->comPort;
        $data['comOptions'] = $config->comOptions;
        $data['comDebug'] = $config->comDebug;
        break;
    case 'email':
        $data['emailFromName'] = $config->emailFromName;
        $data['emailFrom'] = $config->emailFrom;
        $data['emailTo'] = $config->emailTo;
        $data['emailAlarms'] = $config->emailAlarms;
        $data['emailEvents'] = $config->emailEvents;
        $data['emailReports'] = $config->emailReports;
        $data['smtpServer'] = $config->smtpServer;
        $data['smtpUser'] = $config->smtpUser;
        $data['smtpPassword'] = $config->smtpPassword;
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
        if ($inverterId == -1) {
            $data['inverter'] = new Inverter();
        }
        break;
    case 'panel':
        $id = $_GET['id'];
        $inverterId = $_GET['inverterId'];
        $panel = new Panel();
        if ($id == -1) {
            $panel->inverterId = $inverterId;
        } else {
            $panel = $adapter->readPanel($id);
        }
        $data['panel'] = $panel;
        break;
    case 'save-communication':
        $config->comPort = Common::getValue("comPort");
        $config->comOptions = Common::getValue("comOptions");
        $config->comDebug = Common::getValue("comDebug");
        $adapter->writeConfig($config);
        break;
    case 'save-general':
        $config->title = Common::getValue("title");
        $config->subtitle = Common::getValue("subtitle");
        $config->location = Common::getValue("location");
        $config->latitude = Common::getValue("latitude");
        $config->longitude = Common::getValue("longitude");
        $adapter->writeConfig($config);
        break;
    case 'save-inverter':
        $id = Common::getValue("id");
        $inverter = new Inverter();
        if ($id > 0) {
            // get the current data
            $inverter = $adapter->readInverter($id);
        }
        $inverter->name = Common::getValue("name");
        $inverter->description = Common::getValue("description");
        $inverter->initialkwh = Common::getValue("initialkwh");
        $inverter->expectedkwh = Common::getValue("expectedkwh");
        $inverter->plantpower = Common::getValue("plantpower");
        $inverter->heading = Common::getValue("heading");
        $inverter->correctionFactor = Common::getValue("correctionFactor");
        $inverter->comAddress = Common::getValue("comAddress");
        $inverter->comLog = Common::getValue("comLog");

        $adapter->writeInverter($inverter);
        break;
    case 'save-panel':
        $id = Common::getValue("id");
        $panel = new Panel();
        if ($id > 0) {
            // get the current data
            $panel = $adapter->readPanel($id);
        }
        $panel->inverterId = Common::getValue("inverterId");
        $panel->description = Common::getValue("description");
        $panel->roofOrientation = Common::getValue("roofOrientation");
        $panel->roofPitch = Common::getValue("roofPitch");

        $adapter->writePanel($panel);
        break;
    case 'save-email':
        $config->emailFromName = Common::getValue("emailFromName");
        $config->emailFrom = Common::getValue("emailFrom");
        $config->emailTo = Common::getValue("emailTo");
        $config->emailAlarms = Common::getValue("emailAlarms");
        $config->emailEvents = Common::getValue("emailEvents");
        $config->emailReports = Common::getValue("emailReports");
        $adapter->writeConfig($config);
        break;
    case 'save-smtp':
        $config->smtpServer = Common::getValue("smtpServer");
        $config->smtpUser = Common::getValue("smtpUser");
        $config->smtpPassword = Common::getValue("smtpPassword");
        $adapter->writeConfig($config);
        break;
    case 'send-testemail':
        $mail = new PHPMailer();

        $mail->IsSMTP();  // telling the class to use SMTP
        $mail->Host = $config->smtpServer; // SMTP server
        $mail->FromName = $config->emailFromName;
        $mail->From = $config->emailFrom;
        $mail->AddAddress($config->emailTo);

        if ($config->smtpUser && $config->smtpPassword) {
            $mail->SMTPAuth = true;
            $mail->Username = $config->smtpUser;
            $mail->Password = $config->smtpPassword;
        }

        $mail->Subject  = "WSL :: Test message";
        $mail->Body     = "Hello, \n\n This is an test email from your WebSolarLog site.";
        $mail->WordWrap = 50;

        if(!$mail->Send()) {
            $data['result'] = false;
            $data['message'] = $mail->ErrorInfo;
        } else {
            $data['result'] = true;
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