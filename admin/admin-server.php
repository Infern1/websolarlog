<?php
session_start();

require_once("classes/classloader.php");
Session::initialize();


$config = Session::getConfig();
$adapter = PDODataAdapter::getInstance();
// Retrieve action params
$settingstype = Common::getValue("s", null);

$data = array();
switch ($settingstype) {
    case 'advanced':
        $data['co2kwh'] = $config->co2kwh;
        $data['aurorapath'] = $config->aurorapath;
        $data['smagetpath'] = $config->smagetpath;
        break;
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
        $data['smtpPort'] = $config->smtpPort;
        $data['smtpSecurity'] = $config->smtpSecurity;
        $data['smtpUser'] = $config->smtpUser;
        $data['smtpPassword'] = $config->smtpPassword;
        break;
    case 'general':
        $data['title'] = $config->title;
        $data['subtitle'] = $config->subtitle;
        $data['location'] = $config->location;
        $data['latitude'] = $config->latitude;
        $data['longitude'] = $config->longitude;
        $data['template'] = $config->template;
        $data['timezone'] = $config->timezone;
        $data['timezones'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
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
    case 'templates':
        $path = "../template";
        $templates = array();
        foreach (scandir($path) as $file) {
            if (is_dir($path . "/" . $file) && $file != '.' && $file !== '..') {
                $templates[] = $file;
            }
        }
        $data['templates'] = $templates;
        break;
    case 'login':
        $data['result'] = Session::login();
        break;
    case 'logout':
        Session::logout();
        break;
    case 'updater-getversions':
        $experimental = (Common::getValue("experimental", 'false') === 'true') ? true : false;

        if (Updater::isUpdateable()) {
            $data['result'] = true;
            $data['versions'] = Updater::getVersions($experimental);
        } else {
            $data['result'] = false;
            $data['problems'] = Updater::$problems;
        }
        break;
    case 'updater-go':
    	updaterJsonFile("busy", "preparing", 1);
    	FileUtil::writeObjectToJsonFile($jsonFilePath, $status);
    	 
    	HookHandler::getInstance()->fire("onInfo", "Starting update");
        $versioninfo = explode("*",Common::getValue("version", "none"));
        $version = $versioninfo[0];
        $revision = $versioninfo[1];

        if ($version === "none") {
	    	HookHandler::getInstance()->fire("onInfo", "Update error: No version selected");
	    	updaterJsonFile("error", "No Version selected", 1);
            $data['error'] = "No version selected.";
            $data['result'] = false;
            break;
        }

        // Prepare folders
    	updaterJsonFile("busy", "preparing", 5);
        if (!Updater::prepareCheckout()) {
	    	HookHandler::getInstance()->fire("onInfo", "Update error: Error preparing download folder");
	    	updaterJsonFile("error", "Error preparing download folder", 5);
            $data['error'] = "Error preparing download folder.";
            $data['result'] = false;
            break;
        }

        // Do the checkout
        $url = Updater::$url . "tag/" . $version;
        $url = ($version == "trunk") ? Updater::$url . "trunk" : $url;
        $data['svnurl'] = $url;


    	updaterJsonFile("busy", "retrieving update", 10);
        HookHandler::getInstance()->fire("onInfo", "Update info: Starting checkout of " . $url);
        if(!Updater::doCheckout($url)) {
	    	updaterJsonFile("error", "retrieving update failed", 10);
        	HookHandler::getInstance()->fire("onInfo", "Update error: Checkout failed.");
            $data['error'] = "Error while doing the checkout.";
            $data['result'] = false;
            break;
        }

        // Copy the files
    	updaterJsonFile("busy", "applying update", 60);
        HookHandler::getInstance()->fire("onInfo", "Update info: Removing old files and copy new files.");
        Updater::copyToLive();

        // Everything ok, save version info to db
        HookHandler::getInstance()->fire("onInfo", "Update ready: " . $version . " (" . $revision . ")");
        $config->version_title = $version;
        $config->version_revision = $revision;
        $adapter->writeConfig($config);
        
        $status['state'] = "ready";
        $status['info'] = "Update ready";
        $status['percentage'] = 100;
        FileUtil::writeObjectToJsonFile($jsonFilePath, $status);

        $data['result'] = true;
        break;
    case 'isLogin':
        $data['result'] = Session::isLogin();
        break;
    case 'save-adminpasswd':
    	$oldPassword = sha1(Common::getValue("oldPasswd"));
    	
    	if ($config->adminpasswd !== $oldPassword) {
    		$data['success'] = false;
    		$data['title'] = _("Validation");
    		$data['text'] = _("Old password is invalid:");
    		break;    		
    	}
    	
    	if (strlen(Common::getValue("newPasswd1", "")) < 6) {
    		$data['success'] = false;
    		$data['title'] = _("Validation");
    		$data['text'] = _("New password should be at least 6 characters");
    		break;
    	}
    	
    	$newPassword1 = sha1(Common::getValue("newPasswd1", ""));
    	$newPassword2 = sha1(Common::getValue("newPasswd2"));
    	if ($newPassword1 !== $newPassword2) {
    		$data['success'] = false;
    		$data['title'] = _("Validation");
    		$data['text'] = _("New passwords are not the same");
    		break;
    	}
    	
		$config->adminpasswd = $newPassword1;
    	$adapter->writeConfig($config);
    	$data['success'] = true;
   		$data['title'] = _("Success");
   		$data['text'] = _("Password successfully changed");
    	break;
    case 'save-advanced':
        $config->co2kwh = Common::getValue("co2kwh");
        $config->aurorapath =Common::getValue("aurorapath");
        $config->smagetpath =Common::getValue("smagetpath");
        $adapter->writeConfig($config);
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
        $config->timezone = Common::getValue("timezone");
        $config->template = Common::getValue("template");
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
        $inverter->deviceApi = Common::getValue("deviceApi");
        $inverter->comAddress = Common::getValue("comAddress");
        $inverter->comLog = Common::getValue("comLog");
        $inverter->syncTime = Common::getValue("syncTime");

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
        $panel->amount = Common::getValue("amount");
        $panel->wp = Common::getValue("wp");

        $adapter->writePanel($panel);
        break;
    case 'save_expectation':
        $id = Common::getValue("id");
        $inverter = new Inverter();
        if ($id > 0) {
            // get the current data
            $inverter = $adapter->readInverter($id);
        }
        $inverter->expectedkwh = Common::getValue("totalProdKWH");
        $inverter->expectedJAN = Common::getValue("janPER");
        $inverter->expectedFEB = Common::getValue("febPER");
        $inverter->expectedMAR = Common::getValue("marPER");
        $inverter->expectedAPR = Common::getValue("aprPER");
        $inverter->expectedMAY = Common::getValue("mayPER");
        $inverter->expectedJUN = Common::getValue("junPER");
        $inverter->expectedJUL = Common::getValue("julPER");
        $inverter->expectedAUG = Common::getValue("augPER");
        $inverter->expectedSEP = Common::getValue("sepPER");
        $inverter->expectedOCT = Common::getValue("octPER");
        $inverter->expectedNOV = Common::getValue("novPER");
        $inverter->expectedDEC = Common::getValue("decPER");

        $adapter->writeInverter($inverter);
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
        $config->smtpPort = Common::getValue("smtpPort");
        $config->smtpSecurity = Common::getValue("smtpSecurity");
        $config->smtpUser = Common::getValue("smtpUser");
        $config->smtpPassword = Common::getValue("smtpPassword");
        $adapter->writeConfig($config);
        break;
    case 'send-testemail':
        $subject = "WSL :: Test message";
        $body = "Hello, \n\n This is an test email from your WebSolarLog site.";

        $result = Common::sendMail($subject, $body, $config);
        if ( $result === true) {
            $data['result'] = true;
        } else {
            $data['result'] = false;
            $data['message'] = $result;
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

function updaterJsonFile($state, $info, $percentage) {
	$jsonFilePath = dirname(__FILE__) . "/update.json";
	$status = array();
	$status['state'] = $state;
	$status['info'] = $info;
	$status['percentage'] = $percentage;
	FileUtil::writeObjectToJsonFile($jsonFilePath, $status);
}

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
    $checkExtensions = array('curl','sqlite','sqlite3','json','calendar','mcrypt');
    foreach ($checkExtensions as $extension) {
    	$extensions[] = Util::checkIfModuleLoaded($extension);
    }

    $result['extensions'] = $extensions;

    // Encryption/Decryption test
    $testphrase ="This is an test sentence";
    $encrypted = Encryption::encrypt($testphrase);
    $decrypted = Encryption::decrypt($encrypted);
    $result['mcrypt_module_open'] = function_exists("mcrypt_module_open");
    $result['encrypting'] = ($testphrase === $decrypted);

    return $result;
}
?>