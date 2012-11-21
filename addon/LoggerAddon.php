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
		$message = $args[0] . " - " . $args[1];
		$this->write2file("error", $message);
	}
	
	private function write2file($level, $message) {
		$logPath = dirname(dirname(__FILE__)) . "/log";
		
		// Check if the log directory is available
		if (!is_dir($logPath)) {
			if (!mkdir($logPath)) {
				exit("Log directory is not available and we can't create it! " . $logPath);
			}
		}
		
		$message = str_replace("\n", " ", $message);
		$logmsg = date("Ymd His") . "\t" . $message . "\n";
		
		$fh = fopen($logPath . "/" . $level . ".log", "a+");
		if ($fh) {
			fwrite($fh, $logmsg);
			fclose($fh);
		}
		
	}
}
?>