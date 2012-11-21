<?php
class MailAddon {
	public function onError($args) {
		$subject = "WSL :: Error message";
		$body = "Hello, \n\n We have detected an error on your inverter.\n\n";
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
		// Common::sendMail("WSL :: Startup", "Startup test", Session::getConfig());
	}
	
	public function onInverterShutdown($args) {
		Common::sendMail("WSL :: Shutdown", "Shutdown test", Session::getConfig());		
	}
	
	public function onInverterError($args) {
	
	}
}
?>