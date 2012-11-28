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
		$hookname = $args[0];
		$inverter = $args[1];
		if (Session::getConfig()->emailReports) {
			Common::sendMail("WSL :: Startup", "Startup test", Session::getConfig());
		}
	}
	
	public function onInverterShutdown($args) {
		$hookname = $args[0];
		$inverter = $args[1];
		if (Session::getConfig()->emailReports) {
			$title = "WSL :: Shutdown " . $inverter->name;
			$body = $this->createReport($inverter);
			
			Common::sendMail($title, $body, Session::getConfig());		
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
	
	private function createReport(Inverter $inverter) {
		$adapter = PDODataAdapter::getInstance();
		$maxPowerToday = $adapter->readMaxPowerToday($inverter->id);
		$arHistory = $adapter->readHistory($inverter->id, null);
		
		$first = reset($arHistory);
		$last = end($arHistory);
		
		$productionStart = $first['KWHT'];
		$productionEnd = $last['KWHT'];
		
		// Check if we passed 100.000kWh
		if ($productionEnd < $productionStart) {
			$productionEnd += 100000;
		}
		
		$production = round($productionEnd - $productionStart, 3);
		$maxwatt = round($maxPowerToday->GP, 0);
		
		$report = file_get_contents(Session::getBasePath() . "/reports/email/shutdown.inc");
		$report = str_replace("{{inverter.name}}", $inverter->name, $report);
		$report = str_replace("{{totalkwh}}", $production, $report);
		$report = str_replace("{{maxwatt}}", $maxwatt, $report);
		$report = str_replace("{{maxtime}}", date('h:n', $maxPowerToday->time), $report);
		
		return $report;
	}
}
?>