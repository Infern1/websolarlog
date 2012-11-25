<?php
class MailAddon {
	public function onError($args) {
		$subject = "WSL :: Error message";
		$body = "Hello, \n\n WebSolarLog experienced an error.\n\n";
		$body .= $args[1] . "\n\n";
		$body .= "Please check if everything is alright.\n\n";
		$body .= "WebSolarLog";
		
		Common::sendMail($subject, $body, Session::getConfig());
	}
	
	public function onWarning($args) {
		$subject = "WSL :: Warning message";
		$body = "Hello, \n\n We have detected an warning on your inverter.\n\n";
		$body .= $args[1] . "\n\n";
		$body .= "Please check if everything is alright.\n\n";
		$body .= "WebSolarLog";
	
		Common::sendMail($subject, $body, Session::getConfig());
	}
	
	public function onInverterStartup($args) {
		if (Session::getConfig()->emailReports) {
			Common::sendMail("WSL :: Startup", "Startup test", Session::getConfig());
		}
	}
	
	public function onInverterShutdown($args) {
		if (Session::getConfig()->emailReports) {
			Common::sendMail("WSL :: Shutdown", "Shutdown test", Session::getConfig());		
		}
	}
	
	public function onInverterError($args) {
		if (Session::getConfig()->emailAlarms) {
			$subject = "WSL :: Error message";
			$body = "Hello, \n\n We have detected an error on your inverter.\n\n";
			$body .= $args[1] . "\n\n";
			$body .= "Please check if everything is alright.\n\n";
			$body .= "WebSolarLog";
			Common::sendMail($subject, $body, Session::getConfig());
		}	
	}
	
	public function onInverterWarning($args) {
		if (Session::getConfig()->emailEvents) {
			$subject = "WSL :: Warning message";
			$body = "Hello, \n\n We have detected an error on your inverter.\n\n";
			$body .= $args[1] . "\n\n";
			$body .= "Please check if everything is alright.\n\n";
			$body .= "WebSolarLog";
			Common::sendMail($subject, $body, Session::getConfig());
		}	
		
	}
}
?>