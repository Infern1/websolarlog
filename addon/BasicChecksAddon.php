<?php
class BasicChecksAddon {
	private $adapter;
	private $config;
	
	function __construct() {
		$this->adapter = PDODataAdapter::getInstance();
		$this->config = Session::getConfig();
	}
	
	function __destruct() {
		$this->config = null;
		$this->adapter = null;
	}
	
	public function onNewLive($args) {
		$inverter = $args[1];
		$sessionKey = 'noLiveCounter-' . $inverter->id;
		
		// Check if the sun is up and the inverter is down
		if(!Util::isSunDown($this->config) && $inverter->state != 1) {
			if ($this->adapter->changeInverterStatus($inverter, 1)) {
				HookHandler::getInstance()->fire("onInverterStartup", $inverter);
				$inverter->state = 1;
			}			
		}
		
		if (isset($_SESSION[$sessionKey])) {
			unset($_SESSION[$sessionKey]);
		}
	}
	
	public function onNoLiveData($args) {
		$inverter = $args[1];
		$deviceApi = getApi(Session::getConfig());
		
		// detect if the server is down
		$offline = false;
		if ($deviceApi->getState() == 1  ) {
			// we are offline
			$offline = true;
		}		
		if ($deviceApi->getState() == 0  ) {
			$sessionKey = 'noLiveCounter-' . $inverter->id;
			
			$liveCounter = (integer) (isset($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : 0);
			$liveCounter++;
			if ($liveCounter == 30) {
				// Are we seriously down?
				if(Util::isSunDown($this->config)) {
					$offline = true;
				} else {
					// Probably temporarely down, check again
					if (PeriodHelper::isPeriodJob("ShutDownJobErrorINV-" . $inverter->id, 60)) {
						HookHandler::getInstance()->fire("onInverterError", $inverter, "Inverter seems to be down");
					}
					$liveCounter = 0;
				}		
			}
			// Reset the counter if we are still live and the counter > 30
			if ($inverter->state = 1 && $liveCounter > 30) {
					$liveCounter = 0;			
			}
			
			$_SESSION[$sessionKey] = $liveCounter;
		}
		
		if ($offline) {
			if ($this->adapter->changeInverterStatus($inverter, 0)) {
				if (PeriodHelper::isPeriodJob("ShutDownJobINV-" . $inverter->id, (2 * 60))) {
					HookHandler::getInstance()->fire("onInverterShutdown", $inverter);
					$inverter->state = 0;
				}
			}
		}
	}
	
	public function on10MinJob($args) {
		$this->InactiveCheck();	
	}
	
	private function InactiveCheck() {
		// Only continue when the sun is up!
		// -1800, means start checking 30 min after the sun is up and before it is down
		if (Util::isSunDown($this->config,-1800)) {
			return true;
		}
	
		// Only check every 30 minutes
		if (PeriodHelper::isPeriodJob("InactiveCheckJob", 30)) {
			foreach ($this->config->inverters as $inverter) {
				$live = $this->adapter->readLiveInfo($inverter->id);
				if (!empty($live->time) && (time() - $live->time > 3600)) {
					$event = new Event($inverter->id, time(), 'Check', "Live data for inverter '" . $inverter->name . "' not updated for more then one hour.");
					$subject = "WSL :: Event message";
					$body = "Hello, \n\n We have detected an problem on your inverter.\n\n";
					$body .= $event->event . "\n\n";
					$body .= "Please check if everything is alright.\n\n";
					$body .= "Time of live data : " . date("d-m-Y H:i:s", $live->time) . "\n\n";
					$body .= "Please check if everything is alright.\n\n";
					$body .= "WebSolarLog";
	
					$event->alarmSend = Common::sendMail($subject, $body, $this->config);
					$this->adapter->addEvent($inverter->id, $event);
				}
			}
		}
	}
	
}
?>