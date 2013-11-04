<?php
session_start();

require_once("../classes/classloader.php");
Session::initialize();

$config = Session::getConfig();
$adapter = PDODataAdapter::getInstance();
// Retrieve action params
$settingstype = Common::getValue("s", null);

// Security check
if (!Session::isLogin() && $settingstype != 'login' && $settingstype != 'isLogin') {
	exit("not allowed");
}

$data = array();
switch ($settingstype) {
	case 'advanced':
		$data['co2kwh'] = $config->co2kwh;
		$data['co2gas'] = $config->co2gas;
		$data['co2CompensationTree'] = $config->co2CompensationTree;
		$data['costkwh'] = $config->costkwh;
		$data['costGas'] = $config->costGas;
		$data['costWater'] = $config->costWater;
		
		$data['aurorapath'] = $config->aurorapath;
		$data['mastervoltpath'] = $config->mastervoltpath;
		$data['smagetpath'] = $config->smagetpath;
		$data['smaspotpath'] = $config->smaspotpath;
		$data['smaspotWSLpath'] = $config->smaspotWSLpath;
		$data['smartmeterpath'] = $config->smartmeterpath;
		$data['kostalpikopath'] = $config->kostalpikopath;
		$data['plugwiseStrech20IP'] = $config->plugwiseStrech20IP;
		$data['plugwiseStrech20ID'] = $config->plugwiseStrech20ID;
		$data['debugmode'] = $config->debugmode;
		$data['googleAnalytics'] = $config->googleAnalytics;
		$data['piwikServerUrl'] = $config->piwikServerUrl;
		$data['piwikSiteId'] = $config->piwikSiteId;
		
		// social
		$user_id=1;
		$type='Twitter';
		$twitter = $adapter->get_hybridauth_session($user_id,$type);

		if($twitter){
			$social = new Social();
			$social->TwitterAttached = (strlen($twitter['displayName'])>0) ? 1 : 0;
			$social->TwitterDisplayName = $twitter['displayName'];
			$data['social'] = $social;
		}else{
			$social = new Social();
			$social->TwitterAttached = 0;
			$data['social'] = $social;
		}
		
		
		break;
	case 'social':
		
		// social
		$user_id=1;
		$type='Twitter';
		$twitter = $adapter->get_hybridauth_session($user_id,$type);
	
		if($twitter){
			$social = new Social();
			$social->TwitterAttached = (strlen($twitter['displayName'])>0) ? 1 : 0;
			$social->TwitterDisplayName = $twitter['displayName'];
			$data['social'] = $social;
		}else{
			$social = new Social();
			$social->TwitterAttached = 0;
			$data['twitter'] = $social;
		}
		break;
	case 'getTeamFiguresFromPVoutput':
		$lang = array();
		$lang['teamName'] 			= ucfirst(_("team"))." "._("name");
		$lang['energyGenerated']	= ucfirst(_("energy"))." "._("generated");
		
		$lang['energyAverage'] 		= ucfirst(_("energy"))." "._("average");
		$lang['description']	 	= ucfirst(_("description"));
		$lang['numberOfSystems'] 	= ucfirst(_("number"))." "._("of")." "._("Systems");
		$lang['teamSize'] 			= ucfirst(_("team"))." "._("size");
		$lang['averageSize'] 		= ucfirst(_("average"))." "._("size");
		$lang['outputs'] 			= ucfirst(_("outputs"));
		$lang['inverter'] 			= ucfirst(_("inverter"));
		$data['lang'] = $lang;
		
		$pvOutputAddon = new PvOutputAddon();
		$team = $pvOutputAddon->getTeamFiguresFromPVoutput();
		$data['devices'][] =array('pvOutputTeams' => $team);

		
		break;
	case "joinPVoTeam":
		$pvOutputAddon = new PvOutputAddon();
		$team = array();
		$deviceId = Common::getValue("id", null);
		if($deviceId){
			$device = $config->getDeviceConfig($deviceId);
			if($device->pvoutputApikey){
				$result = $pvOutputAddon->joinTeam($device);
				//var_dump($result);
				if($result['info']['http_code']==200){
					//var_dump($result['response']);
					$pvOutputAddon->saveTeamStateFromPVoutputToDB($device);
					$team = $result;
				}else{
					$pvOutputAddon->saveTeamStateFromPVoutputToDB($device);
					$team = $result;
				}
			}
		}else{
			$team['response'] = 'no team supplied';
		}
		$data['id'] = $deviceId;
		$data['team']= $team;
		break;
	case "leavePVoTeam":
		$pvOutputAddon = new PvOutputAddon();
		$team = array();
		$deviceId = Common::getValue("id", null);
		if($deviceId){
			$device = $config->getDeviceConfig($deviceId);
			if($device->pvoutputApikey){
				$result = $pvOutputAddon->leaveTeam($device);
				if($result['info']['http_code']==200){
					//var_dump($result['response']);
					$pvOutputAddon->saveTeamStateFromPVoutputToDB($device);
					$team = $result;
				}else{
					$pvOutputAddon->saveTeamStateFromPVoutputToDB($device);
					$team = $result;
				}
			}
		}else{
			$team['response'] = 'no team supplied';
		}
		$data['id'] = $deviceId;
		$data['team']= $team;
		break;
	case "saveTeamStatus":
		$pvOutputAddon = new PvOutputAddon();
		$team = array();
		$deviceId = Common::getValue("id", null);
		if($deviceId){
			$device = $config->getDeviceConfig($deviceId);
			if($device->pvoutputApikey){
				//request PVoutput for teamstatus and save it in the DB
				$pvOutputAddon->saveTeamStateFromPVoutputToDB($device);
			}
		}else{
			foreach ($config->devices as $device){
				if($device->pvoutputApikey){
					//request PVoutput for teamstatus and save it in the DB
					$pvOutputAddon->saveTeamStateFromPVoutputToDB($device);
				}
				pause(1);
			}
		}
		break;
	case "getTeamStatus":
		$pvOutputAddon = new PvOutputAddon();
		$result = array();
		$deviceId = Common::getValue("id", null);
		if($deviceId){
			$device = $config->getDeviceConfig($deviceId);
			if($device->pvoutputApikey){
				$result[] = $pvOutputAddon->getTeamStatusFromDB($device);
			}
		}else{
			foreach ($config->devices as $device){
				if($device->pvoutputApikey){
					$result[] = $pvOutputAddon->getTeamStatusFromDB($device);
				}
			}
		}
		$data['devices'] = $result;
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
		$data['url'] = $config->url;
		$data['gaugeMaxType'] = $config->gaugeMaxType;
		$data['graphShowACDC'] = $config->graphShowACDC;
		$data['location'] = $config->location;
		$data['latitude'] = $config->latitude;
		$data['longitude'] = $config->longitude;
		$data['template'] = $config->template;
		$data['timezone'] = $config->timezone;
		$data['timezones'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
		$data['moneySign'] = $config->moneySign;
		break;
	case 'inverters': // backwords compatibility
		$deviceService = new DeviceService();
		$data['supportedDevices'] = $deviceService->getSupportedDevices();
		$data['inverters'] = $config->allDevices;
		break;
	case 'devices':
		$deviceService = new DeviceService();
		$data['supportedDevices'] = $deviceService->getSupportedDevices();
		$data['devices'] = $config->allDevices;
		break;
	case 'inverter': // backwords compatibility
	case 'device':
		$deviceId = $_GET['id'];
		//var_dump($deviceId);
		$data['SMAspotDevices'] = 0;
		$deviceService = new DeviceService();
		$data['supportedDevices'] = $deviceService->getSupportedDevices();
		

		$data['inverter'] = $deviceService->load($deviceId);
		//var_dump($data['inverter']);
		// if we don't have a SMA-BT-WSL device, we don't want to use "special" config file and reset comAdress.

		if ($deviceId == -1) {
			$data['inverter'] = new Device();
		}

		// check if we have already a SMA-BT-WSL
		foreach($config->allDevices as $device){
			if($device->deviceApi == "SMA-BT-WSL"){
				$data['SMAspotDevices']++;
			}
		}
		if($data['SMAspotDevices']==0 && $device->deviceApi == "SMA-BT-WSL"){
			$data['inverter']->comAddress = '';
		}


		break;
	case 'panel':
		$id = $_GET['id'];
		$panelService = new PanelService();
		if ($id == -1) {
			$panel = new Panel();
			$panel->inverterId = $_GET['inverterId'];
		} else {
			$panel = $panelService->load($id);
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
	case 'save-checkNewTrunk':
		$config->checkNewTrunk = Common::getValue('chkNewTrunk');
		$adapter->writeConfig($config);
		$data['result']= true;
		
		break;
	case 'updater-getversions':
		$experimental = (Common::getValue("experimental", 'false') === 'true') ? true : false;
		$beta = (Common::getValue("beta", 'false') === 'true') ? true : false;
		$data['chkNewTrunk'] = ($config->checkNewTrunk=='true') ? true : false;
		$data['currentVersionTitle'] = $config->version_title;
		$data['currentVersionRevision'] = $config->version_revision;
		$data['currentVersionReleaseTime'] = date("Y-m-d H:i:s",$config->version_release_time);
		$data['currentVersionReleaseDescription'] = $config->version_release_description;
		$data['currentVersionUpdateTime'] = date("Y-m-d H:i:s",$config->version_update_time);
		if (Updater::isUpdateable()) {
			$data['result'] = true;
			$data['versions'] = Updater::getVersions($experimental,$beta);
		} else {
			$data['result'] = false;
			$data['problems'] = Updater::$problems;
		}
		break;
	case 'updater-go':
		updaterJsonFile("busy", "preparing", 1);

		HookHandler::getInstance()->fire("onInfo", "Starting update");
		$versioninfo = explode("*",Common::getValue("version", "none"));
		$version = $versioninfo[0];
		$revision = $versioninfo[1];
		$releaseTime = $versioninfo[2];
		$releaseDescription = $versioninfo[3];
		
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
		$url = Updater::$url . "tags/" . $version;
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
		updaterJsonFile("busy", "applying update", 50);
		HookHandler::getInstance()->fire("onInfo", "Update info: Removing old files and copy new files.");
		sleep(1); // Sleep 1 second to give system some air
		Updater::copyToLive();

		// Everything ok, save version info to db
		$config->version_title = $version;
		$config->version_revision = $revision;
		$config->version_release_time = $releaseTime;
		$config->version_update_time = time();
		$config->version_release_description = $releaseDescription;
		
		$adapter->writeConfig($config);
		
		HookHandler::getInstance()->fire("onInfo", "Update ready: " . $version . " (" . $revision . ")");
		updaterJsonFile("ready", "Update ready", 100);
		$data['result'] = true;
		
		// We want an restart off the queue server
		Common::createRestartQueueItem();
		
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
		$config->co2gas = Common::getValue("co2gas");
		$config->co2CompensationTree = Common::getValue("co2CompensationTree");
		$config->costkwh = Common::getValue("costkwh");
		$config->costGas = Common::getValue("costGas");
		$config->costWater = Common::getValue("costWater");
		
		$config->aurorapath =Common::getValue("aurorapath");
		$config->mastervoltpath =Common::getValue("mastervoltpath");
		$config->smagetpath =Common::getValue("smagetpath");
		$config->smaspotpath =Common::getValue("smaspotpath");
		$config->smaspotWSLpath =Common::getValue("smaspotWSLpath");
		$config->kostalpikopath =Common::getValue("kostalpikopath");
		$config->plugwiseStrech20IP =Common::getValue("plugwiseStrech20IP");
		$config->plugwiseStrech20ID =Common::getValue("plugwiseStrech20ID");
		$config->smartmeterpath =Common::getValue("smartmeterpath");
		$config->debugmode =Common::getValue("debugmode");
		$config->googleAnalytics = Common::getValue("googleAnalytics");
		$config->piwikServerUrl = Common::getValue("piwikServerUrl");
		$config->piwikSiteId = Common::getValue("piwikSiteId");
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
		$config->url = Common::getValue("url");
		$config->gaugeMaxType = Common::getValue("gaugeMaxType");
		$config->graphShowACDC = Common::getValue("graphShowACDC");
		$config->location = Common::getValue("location");
		$config->latitude = Common::getValue("latitude");
		$config->longitude = Common::getValue("longitude");
		$config->timezone = Common::getValue("timezone");
		$config->template = Common::getValue("template");
		$config->moneySign = Common::getValue("moneySign");
		$adapter->writeConfig($config);
		break;
	case 'save-inverter': // backwords compatibility
	case 'save-device':
		$deviceService = new DeviceService();
		$device = new Device();
		
		$id = Common::getValue("id", -1);

		if ($id > 0) {
			// get the current data
			$device = $deviceService->load($id);
		}
		$device->name = Common::getValue("name");
		$device->description = Common::getValue("description");
		$device->liveOnFrontend = Common::getValue("liveOnFrontend");
		$device->graphOnFrontend = Common::getValue("graphOnFrontend");
		$device->initialkwh = Common::getValue("initialkwh");
		$device->producesSince = Common::getValue("producesSince");
		$device->expectedkwh = Common::getValue("expectedkwh");
		$device->plantpower = Common::getValue("plantpower");
		$device->deviceApi = Common::getValue("deviceApi");
		$device->type = Common::getValue("deviceType");
		$device->communicationId = Common::getValue("communicationId"); 
		$device->comAddress = Common::getValue("comAddress");
		$device->comLog = Common::getValue("comLog");
		$device->syncTime = Common::getValue("syncTime");
		$device->pvoutputEnabled = Common::getValue("pvoutputEnabled",'null');
		$device->pvoutputApikey = Common::getValue("pvoutputApikey");
		$device->pvoutputSystemId = Common::getValue("pvoutputSystemId");
		$device->pvoutputWSLTeamMember = Common::getValue("pvoutputWSLTeamMember");
		$device->refreshTime = (Common::getValue("refreshTime")< 2) ? 2 : Common::getValue("refreshTime");
		$device->historyRate = (Common::getValue("historyRate")<60) ? 60 : Common::getValue("historyRate");
		$device->active = Common::getValue("deviceActive");
		$data['id'] = $deviceService->save($device)->id;
		break;
	case 'save-panel':
		$panelService = new PanelService();
		
		$id = Common::getValue("id");
		$panel = new Panel();
		if ($id > 0) {
			// get the current data
			$panel = $panelService->load($id);
		}
		$panel->inverterId = Common::getValue("inverterId");
		$panel->description = Common::getValue("description");
		$panel->roofOrientation = Common::getValue("roofOrientation");
		$panel->roofPitch = Common::getValue("roofPitch");
		$panel->amount = Common::getValue("amount");
		$panel->wp = Common::getValue("wp");

		$panelService->save($panel);
		break;
	case 'save_expectation':
		$deviceService = new DeviceService();
		$id = Common::getValue("id");
		if ($id > 0) {
			// get the current data
			$device = $deviceService->load($id);
			$device->expectedkwh = Common::getValue("totalProdKWH");
			$device->expectedJAN = Common::getValue("janPER");
			$device->expectedFEB = Common::getValue("febPER");
			$device->expectedMAR = Common::getValue("marPER");
			$device->expectedAPR = Common::getValue("aprPER");
			$device->expectedMAY = Common::getValue("mayPER");
			$device->expectedJUN = Common::getValue("junPER");
			$device->expectedJUL = Common::getValue("julPER");
			$device->expectedAUG = Common::getValue("augPER");
			$device->expectedSEP = Common::getValue("sepPER");
			$device->expectedOCT = Common::getValue("octPER");
			$device->expectedNOV = Common::getValue("novPER");
			$device->expectedDEC = Common::getValue("decPER");
			
			$deviceService->save($device);
		}
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
	case 'attachTwitter':
		$_SESSION['refURL'] = Common::getValue('refURL');
		if($_SESSION["HA::STORE"]['hauth_session.twitter.is_logged_in']!='i:1;'){
			$_SESSION["HA::STORE"] = null;
			$_SESSION["HA::CONFIG"] = null;
			$hybridTwitter = new TwitterAddon();
			$hybridTwitter->attachTwitter();
		}else{
			$hybridTwitter = new TwitterAddon();
			$hybridTwitter->attachTwitter();
			$refURL = $_SESSION['refURL'];
			$_SESSION['refURL'] = null;
			header("location:".$refURL."#social");
		}
		break;
	case 'detachTwitter':
		$hybridTwitter = new TwitterAddon();
		$hybridTwitter->detachTwitter();
		break;
	case 'sendTweet':
		$hybridTwitter = new TwitterAddon();
		$hybridTwitter->sendTweet();
		
		break;
	case 'attachFacebook':
		$hybridFacebook = new FacebookAddon();
		$hybridFacebook->attachFacebook();
	
		break;
	
	case 'detachFacebook':
		$hybridFacebook = new FacebookAddon();
		$hybridFacebook->detachFacebook();
	
		break;
	case 'sendFacebook':
		$hybridFacebook = new FacebookAddon();
		$hybridFacebook->sendFacebook();
		break;

	case 'attachDropbox':
		// getting back from Dropbox with the UID and oauth_token as $_get values
		if((Common::getValue('uid')) && (Common::getValue('oauth_token'))){
			$dropbox = new Dropbox();
			$url =explode("/",Util::getCurrentUrl());
			array_pop($url);
			$url = implode("/",$url);
			header('location:'.$url."#backup");
		}else{
			//we are not getting from Dropbox and want to go to Dropbox to account
			unset($_SESSION['dropbox_api']);
			$dropbox = new Dropbox();
		}
		break;
	case 'dropboxMakeBackup':
		$data = array();
		$data['files'] = array();
		$dropbox = new Dropbox;

		/* not needed till we can switch DB-engines
		$config = Session::getConfig();
		$dbName = explode('/',$config->dbHost);
		$dbName = explode('.',$dbName[count($dbName)-1]);
		$backupFileName = $dbName[0].'_'.date('Ymd').''.date('His').'.backup';
		*/
		
		$dbName = 'wsl';
		$backupFileName = $dbName.'_'.date('Ymd').''.date('His').'.backup';
		
		$dbPath = '../database/'.$dbName.'.sdb';
		
		$data['dropboxResponse']= $dropbox->dropbox->putFile($dbPath, $backupFileName);

		$path = $dropbox->dropbox->media($data['dropboxResponse']['body']->path);
		//$data['files'][0]->fullPath = $path['body']->url;
			
		//$file->client_mtime = "Tue, 04 Dec 2012 13:00:59 +0000";
		// explode client_mtime
		$exDateTime = explode(' ',$data['dropboxResponse']['body']->client_mtime);
		// re-org timeformat
		$client_mtime = $exDateTime[2].' '.$exDateTime[1].' '.$exDateTime[3].' '.$exDateTime[4];
		//make timestamp and generate new timesting
		$dateTime = strtotime($client_mtime);
		// replace client_mtime with new timestring
		$data['files'] = array('client_mtime' => $dateTime,'fullPath' => $path['body']->url,'path' => str_replace("/","",$data['dropboxResponse']['body']->path), 'bytes' => $data['dropboxResponse']['body']->bytes);

		$adapter->dropboxSaveFile($data['files']);
			
		break;
	case 'detachDropbox':
		$data = $adapter->dropboxDisconnect();
		break;
	case 'dropbox':
		if($adapter->sqlEngine=='sqlite'){
			$data['available'] = $adapter->dropboxTokenExists();
		}else{
			$data['available'] = false;
			$data['sqlEngine'] = $adapter->sqlEngine;
		}
		break;
	case 'dropboxSyncFiles':
		try {
	
		// get Dropbox things
		$dropbox = new Dropbox;
			
		//get all the dropbox files
		$meta = $dropbox->dropbox->metaData();

		$data['success'] = true;
		//we only need the file content
		$data['files'] = $meta['body']->contents;
		// reverse the order (last added file to 0-key of array)
		$data['files'] = array_reverse($data['files']);

		//init
		$totalBackupSize = 0;
		$i=0;

		// walkthrough the dropbox files
		foreach ($data['files'] as $file) {
				//echo str_replace("/","",$file->path);
				$fileMedia = $dropbox->dropbox->media($file->path);

			// set fullPath to the files array

			//$file->client_mtime = "Tue, 04 Dec 2012 13:00:59 +0000";
			// explode client_mtime
			$exDateTime = explode(' ',$file->client_mtime);
			// re-org timeformat
			$client_mtime = $exDateTime[2].' '.$exDateTime[1].' '.$exDateTime[3].' '.$exDateTime[4];
			//make timestamp and generate new timesting
			$dateTime = strtotime($client_mtime);
				
				$dropbboxFile = array(
						'client_mtime' => $dateTime,
						'path' => $file->path,
						'fullPath' => $fileMedia['body']->url,
						'id' => $i,
						'num' => $i+1,
						'bytes' => $file->bytes				
				);
				//var_dump($dropbboxFile);
			//lets see if we need to save the file to the database.
				$adapter->dropboxSaveFile($dropbboxFile);
			// sum the filesizes for some nice figures
			$totalBackupSize += $file->bytes;
				$data['files'][] = $dropbboxFile;
			$i++;
		}
		$data['totalBackups'] = $i+1;
		$data['totalBackupSize'] = number_format($totalBackupSize/1000000,2,'.',''); // Bytes -> MegaByte
		$data['avarageBackupSize'] = number_format((totalBackupSize/($i+1))/1000000,2,'.',''); // Bytes -> MegaByte
			
		// sync dropbox-files with database records (remove file from DB if the are not in dropbox-file-array)
		$adapter->dropboxCheckActive($data['files']);
		$data['files'] = null;
		$data =$adapter->dropboxGetFilesFromDB();
		$data['success'] = true;
		} catch (Exception $e) {
			//echo $e->getMessage();
			$data['message'] = $e->getMessage();
			$data['success'] = false;
		}
		break;
	case 'dropboxGetFiles':
		$data =$adapter->dropboxGetFilesFromDB();
			
		$data['success']= true;
		break;
			
	case 'dropboxDeleteFile':
		$dropbox = new Dropbox;
		$path = Common::getValue("path");
		//echo $path;
		// Output the result
		try {
			$delete = $dropbox->dropbox->delete($path);
			if ($delete){
				$adapter->dropboxDropFile($path);
				$data['message'] = "File '$path' deleted from your dropbox and WebSolarLog.";
				$data['success'] = true;
			}
		} catch (Exception $e) {
			$adapter->dropboxDropFile($path);
			$data['message'] = "File '$path' doesn't exist in your dropbox.<br>So we only deleted it from WebSolarLog";
			$data['success'] = false;
		}
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
	case "getAllPlugs":
		$plugwise = new PlugwiseStretchAddon();
		$data['plugs'] = $plugwise->getAllPlugwisePlugs();
		break;
	case "switchPowerState":
		$plugwise = new PlugwiseStretchAddon();
		
		$plug = new PlugwisePlug();
		$plug->powerState = Common::getValue('newPowerState',null);
		$plug->applianceID = Common::getValue('applianceID',null);
		
		$data['plugs'] = $plugwise->switchPowerState($plug);
		break;
	case "syncPlugs":
		$plugwise = new PlugwiseStretchAddon();
		$data['plugs'] = $plugwise->syncPlugsWithDB();
		break;	
	case "getPlugWatts":
		$plugwise = new PlugwiseStretchAddon();
		$data['plugs'] = $plugwise->getPlugsWatts();
		break;
	case 'plugwiseSavePlug':
		$plugwise = new PlugwiseStretchAddon();
		$plug = new PlugwisePlug();
		
		$plug->name = Common::getValue('name');
		$plug->applianceID = Common::getValue('id');
		$plugwise->SavePlugwisePlug($plug);
		$data['result'] = true;
		break;	
	case 'current-trunk-version':
		$config = Session::getConfig();
		$versions = Updater::getVersions(true);
		foreach ($versions as $key => $version){
			if($key=='trunk'){
				$data['trunkNotifier'] = ($version[0]['revision'] > $config->version_revision) ? true : false;
			}
		}
		break;
	case 'test':
		$data['test'] = diagnostics($adapter);
		break;
	case 'graphs':
		$graphService = new GraphService();
		$data['graphs'] = $graphService->loadDaily();
		break;
	/*case 'getGraphObject':
		$graph = new Graph();

		$getGraphAxes = HookHandler::getInstance()->fire("getGraphAxes","SmartMeterAddon.getAxes");
		//var_dump($getGraphAxes);
		$graph->axes = $graph->getGraphAxes();
		$graph->series = $graph->getGraphSeries();
		
		$data['graphObject'] = $graph;
		break;*/
	case 'dbm_getTables':
		$data['tables'] = R::$writer->getTables();
		break;
		
	case 'dbm_getTableData':
		$tableName = Common::getValue("tableName", null);
		if ($tableName) {
			$db_columns = array();
			$columns = R::$writer->getColumns($tableName);
			foreach ($columns as $columnname=>$columntype) {
				// Which editor do we need?				
				$editor = "Slick.Editors.Text";
				if ($columntype == "INTEGER") {
					$editor = "Slick.Editors.Integer"; // only full Integers (no decimals!)
				}
				
				$db_column = array();
				$db_column['id'] = $columnname;
				$db_column['name'] = $columnname;
				$db_column['field'] = $columnname;
				$db_column['editor'] = $editor;
				$db_column['toolTip'] = $columntype;
				$db_columns[] = $db_column;
}

			$data['dbname'] = $tableName;
			$data['columns'] = $db_columns;
			$data['data'] = R::getAll( 'select * from ' . $tableName);
		}
		break;
	case 'dbm_saveTableData':
		$tableName=Common::getValue("tableName");
		$id=Common::getValue('id');
		
		
		$oBean = R::load($tableName, $id);
		// TODO If not id quit
		
		// Use the values send and save
		foreach($_POST as $key => $val) {
			if ($key == "id") {
				continue; // skip the id
			}
			$oBean[$key] = $val;
		}
		R::store($oBean);
		$data['status'] = 'saved';
		break;
	case 'resetGraph':
		$graphService = new GraphService();
		$graphService->installGraph(true);
		break;
	case 'yield_getEnergyList':
		$deviceService = new DeviceService();
		$deviceHistoryService = new DeviceHistoryService();
		$energyService = new EnergyService();
		
		$device = $deviceService->load(Common::getValue('deviceId'));
		
		$result = array();
		
		// Set the data from the energyList
		$energyList = $energyService->getEnergyListByDevice($device);
		foreach ($energyList as $energy) {
			$year = (int) date("Y", $energy->time);
			$month = (int) date("m", $energy->time);
			$day = (int) date("d", $energy->time);
			
			if (!isset($result[$year])) {
				$result[$year] = array();
			}
			if (!isset($result[$year][$month])) {
				$result[$year][$month] = array();
			}
			if (!isset($result[$year][$month][$day])) {
				$result[$year][$month][$day] = array();
			}			
			
			$result[$year][$month][$day]["energy"] = $energy; 
		}
		
		$deviceHistoryList = $deviceHistoryService->getArrayByDevice($device);
		foreach ($deviceHistoryList as $deviceHistory) {
			$year = (int) date("Y", $deviceHistory->time);
			$month = (int) date("m", $deviceHistory->time);
			$day = (int) date("d", $deviceHistory->time);
			
			if (!array_key_exists($year, $result)) {
				$result[$year] = array();
			}
			if (!array_key_exists($month, $result[$year])) {
				$result[$year][$month] = array();
			}
			if (!array_key_exists($day, $result[$year][$month])) {
				$result[$year][$month][$day] = array();
			}
			$result[$year][$month][$day]["deviceHistory"] = $deviceHistory; 
		}
		
		$data['data'] = $result;
		break;
	case 'yield_addEnergy':
		$sdte = Common::getValue('time');
		$time = strtotime($sdte . " 01:00:00");
		$deviceId = Common::getValue('deviceId');
		$newKWH = Common::getValue('newKWH');
		
		// Update the energy if it is available
		$energyService = new EnergyService();
		$energy = new Energy();
		$energy->INV = $deviceId;
		$energy->deviceId = $deviceId;
		$energy->SDTE = $sdte;
		$energy->time = $time;
		$energy->KWH = $newKWH;
		$energyService->save($energy);
		
		$data['success'] = true;
		break;
	case 'yield_saveEnergy':
		$energyId = Common::getValue('energyId');
		$deviceHistoryId = Common::getValue('deviceHistoryId');
		$newKWH = Common::getValue('newKWH');
		
		// Update the deviceHistory if it is available
		$deviceHistoryService = new DeviceHistoryService();
		$deviceHistory = $deviceHistoryService->load($deviceHistoryId);
		if ($deviceHistory->id == $deviceHistoryId && $deviceHistory->id > 0) {
			$deviceHistory->processed = true;
			$deviceHistoryService->save($deviceHistory);
		}
		
		// Update the energy if it is available
		$energyService = new EnergyService();
		$energy = $energyService->load($energyId);
		if ($energy->id != $energyId || $energy->id < 1) {
			$energy->time = $deviceHistory->time;
			$energy->INV = $deviceHistory->deviceId;
			$energy->deviceId = $deviceHistory->deviceId;
		}
		$energy->KWH = $newKWH;
		$energyService->save($energy);
		
		$data['success'] = true;
		break;
}

if(Session::isLogin()){
	$pass = $adapter->checkDefaultPassword();
}

if(isset($pass) && $pass){
	$data['pass']=$pass;	
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
	$jsonFilePath = dirname(dirname(__FILE__)) . "/tmp/update.json";
	$status = array();
	$status['state'] = $state;
	$status['info'] = $info;
	$status['percentage'] = $percentage;
	FileUtil::writeObjectToJsonFile($jsonFilePath, $status);
}

/**
 * The diagnostics() function runs a number of test for us.
 * It also returns the available drivers/extensions/classes, version check, DB info, log paths etc... 
 * @return array
 */
function diagnostics($adapter) {
	$config = Session::getConfig();	
	$result = array();
	$result['sqlite'] = false;
	$result['available_drivers'] = PDO::getAvailableDrivers();
	$result['db']['sqlEngine'] = $adapter->sqlEngine;
	// Check if sql lite is installed
	foreach ($result['available_drivers'] as $driver) {
		if ($driver == 'sqlite') {
			$result['sqlite'] = true;
		}
	}
	// check path is ending with a slash(/)
	$configURL = 'http://'.$config->url.((substr($config->url, -1)=='/') ? '' : '/');
	$basePath = Session::getBasePath().((substr(Session::getBasePath(), -1)=='/') ? '' : '/');
	
	$result['logs'][] = array('url' => $configURL.'log/debug.log','location' => $basePath.'log/debug.log', 'name' => 'Debug');
	$result['logs'][] = array('url' => $configURL.'log/error.log','location' => $basePath.'log/error.log', 'name' => 'Error');
	$PidFilename = $basePath.'scripts/server.php.pid';
	
	$result['currentTime']=time();
	if (file_exists($PidFilename)) {
		$stat = stat($PidFilename);
		$result['pid']['exists']=true;
		$result['pid']['timeDiff'] = time()-$stat['atime'];
		if($result['pid']['timeDiff'] >= 65){
			$result['pid']['WSLRunningState']=false;

		}else{ 
			$result['pid']['WSLRunningState']=true;
		}
		
		$result['pid']['atime']=$stat['atime'];
		$result['pid']['mtime']=$stat['mtime'];
		$result['pid']['ctime']=$stat['ctime'];
	} else {
		$result['pid']['exists']=false;
	}
	$result['commands']['start'] = $basePath.'scripts/./wsl.sh start';
	$result['commands']['stop'] = $basePath.'scripts/./wsl.sh stop';
	$result['commands']['restart'] = $basePath.'scripts/./wsl.sh restart';
	$result['commands']['status'] = $basePath.'scripts/./wsl.sh status';
	
	
	$result['db']['dsn'] = $config->dbDSN;
	if ($result['db']['sqlEngine'] == 'sqlite') {
		$SDBFilename = Session::getBasePath().'/database/wsl.sdb';
		$result['dbRights'] = Util::file_perms($SDBFilename);
		if (file_exists($SDBFilename)) {
			$stat = stat($SDBFilename);
			$result['db']['exists']=true;
			$result['db']['timeDiff'] = time()-$stat['ctime'];
			($result['db']['timeDiff'] >= 30) ? $result['db']['dbChanged']=false : $result['db']['dbChanged']=true;
		
			$result['db']['atime']=$stat['atime'];
			$result['db']['mtime']=$stat['mtime'];
			$result['db']['ctime']=$stat['ctime'];
		} else {
			$result['db']['exists']=false;
		}
	}
	
	// TODO
	// needs some standard DB testing;
	// last live record, could we connect, etc...
	
	
	// Try to get the sqlite version if installed
	if ($result['sqlite'] === true) {
		$filename = tempnam(sys_get_temp_dir(), 'empty'); // use a temporary empty db file for version check
		$conn = new PDO('sqlite:' . $filename);
		$result['sqliteVersion'] = $conn->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
		$result['sqliteVersionCheck'] = (version_compare($result['sqliteVersion'], '3.7.11', '>=')) ? true : false ;
		$conn = null; // Close the connection and free resources
	}else{
		
	}

	// Check if the following extensions are installed/activated
	$checkExtensions = array('curl','sqlite3','sqlite','json','calendar','mcrypt');
	foreach ($checkExtensions as $extension) {
		if($extension == 'sqlite'){
			if(!$extensions['sqlite3']['status']){
				$extensions[$extension] = Util::checkIfModuleLoaded($extension,$type='extension');
			}
		}else{
				$extensions[$extension] = Util::checkIfModuleLoaded($extension,$type='extension');
		}
	}

	
	
	// check functions
	$name = "mcrypt_module_open";
	$extensions[$name] = array('name'=>$name,'status'=>function_exists($name),'type'=>'function');
	$result['extensions'] = $extensions;

	$result['phpVersion'] = phpversion(); 
	$result['phpVersionCheck'] = (version_compare($result['phpVersion'], '5.3.10', '>=')) ? true : false ;
	
	$result['sqliteVersionMixed'] = (isset($extensions['sqlite']) && $extensions['sqlite']['status'] && !$extensions['sqlite3']['status']);

	// Encryption/Decryption test
	$testphrase ="This is an test sentence";
	$encrypted = Encryption::encrypt($testphrase);
	$decrypted = Encryption::decrypt($encrypted);
	
	$result['encrypting'] = ($testphrase === $decrypted);

	
	$result['browserLanguages'] = Util::getBrowserDefaultLanguage();
	$result['supportedLanguages'] = Session::supportedLanguages($result['browserLanguages']);
	$result['setLanguage'] = Session::setLanguage();
	$result['sessionLanguage'] = $_SESSION['WSL_LANGUAGE'];

	$dateTimeZoneUTC = new DateTimeZone("utc");
	$dateTimeZoneConfig = new DateTimeZone($config->timezone);
	
	$dateTimeUTC = new DateTime("now", $dateTimeZoneUTC);
	$dateTimeConfig = new DateTime("now", $dateTimeZoneConfig);
	
	$offset = $dateTimeZoneConfig->getOffset($dateTimeUTC);
	
	$result['time']['offset'] =  ($offset>0) ? $offset/3600 : $offset;
	$result['time']['dateTimeUTC'] = $dateTimeUTC;
	$result['time']['dateTimeLocation'] = $dateTimeConfig;
	return $result;
}
?>