<?php
class MailAddon {
	public function onError($args) {
		$subject = "WSL :: Error occured";
		$body = "Error: " . $args[1];
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