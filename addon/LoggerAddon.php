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
		if (Session::getConfig()->debugmode) {
			$message = $args[0] . " - " . $args[1];
			$this->write2file("debug", $message);
		}
	}

	public function onInverterStartup($args) {
		$message = $args[0] . " - " . $args[1]->name;
		$this->write2file("debug", $message);
	}

	public function onInverterShutdown($args) {
		$message = $args[0] . " - " . $args[1]->name;
		$this->write2file("debug", $message);
	}
	
	public function onInverterError($args) {
		$message = $args[0] . " - " . $args[1]->name . " - " . $args[2];
		$this->write2file("error", $message);
	}

	public function onInverterWarning($args) {
		$message = $args[0] . " - " . $args[1]->name . " - " . $args[2];
		$this->write2file("warning", $message);
	}
	
	private function write2file($level, $message) {
		$logPath = Common::getRootPath() . "/log";
		$fileName = $logPath . "/" . $level . ".log";
		
		// Check if the log directory is available
		if (!is_dir($logPath)) {
			if (!mkdir($logPath)) {
				throw new SetupException("Log directory is not available and we can't create it! " . $logPath);
			}
		}
		
		// Check if we can write to the file
		if (!is_writable($fileName)) {
			throw new SetupException("Log file is not writable" . $fileName);
		}
		
		$message = str_replace("\n", " ", $message);
		$logmsg = date("Ymd His") . "\t" . $message . "\n";
		
		$fh = fopen($fileName, "a+");
		if ($fh) {
			fwrite($fh, $logmsg);
			fclose($fh);
		}
	}
}
?>