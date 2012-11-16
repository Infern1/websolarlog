<?php
class LoggerAddon {
	public function onError($args) {
		$message = $args[0] . " - " . $args[1];
		$this->write2file("error", $message);
	}
	
	public function onWarning($args) {
		$message = $args[0] . " - " . $args[1];
		$this->write2file("warning", $message);
	}
	
	public function onInfo($args) {
		$message = $args[0] . " - " . $args[1];
		$this->write2file("info", $message);
	}

	public function onDebug($args) {
		if (Session::getConfig()->debugging) {
			$message = $args[0] . " - " . $args[1];
			$this->write2file("debug", $message);
		}
	}

	public function onInverterStartup($args) {

	}

	public function onInverterShutdown($args) {

	}
	
	public function onInverterError($args) {
		
	}
	
	private function write2file($level, $message) {
		// Check if the log directory is available
		if (!is_dir("log")) {
			if (!mkdir("log")) {
				exit("Log directory is not available and we can't create it!");
			}
		}
		
		$logmsg = date("Ymd His") . "\t" . $message . "\n";
		
		$fh = fopen("log/" . $level . ".log", "a+");
		if ($fh) {
			fwrite($fh, $logmsg);
			fclose($fh);
		}
		
	}
}
?>