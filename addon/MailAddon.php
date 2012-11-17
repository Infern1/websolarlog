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
		
	}
	
	public function onInverterShutdown($args) {
		
	}
	
	public function onInverterError($args) {
	
	}
}
?>