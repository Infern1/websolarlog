<?php
class InactiveCheck {
    private $adapter;
    private $config;

    function __construct() {
        $this->adapter = new PDODataAdapter();
        $this->config = $this->adapter->readConfig();
    }

    function __destruct() {
        $config = null;
        $adapter = null;
    }

    public function check() {
        // Only continue when the sun is up!
        // -900, means start checking 15 min after the sun is up and before it is down
        if (Util::isSunDown($this->config,-900)) {
            return true;
        }

        // Only check every 15 minutes
        if (PeriodHelper::isPeriodJob("InactiveCheckJob", 15)) {
            foreach ($this->config->inverters as $inverter) {
                $live = $this->adapter->readLiveInfo($inverter->id);
                if ((time() - $live->time > 3600)) {
                    $event = new Event($inverter->id, time(), 'Check', "Live data for inverter '" . $inverter->name . "' not updated for more then one hour.");
                    $subject = "WSL :: Event message";
                    $body = "Hello, \n\n We have detected an problem on your inverter.\n\n";
                    $body .= $event->event . "\n\n";
                    $body .= "Please check if everything is alright.\n\n";
                    $body .= "WebSolarLog";

                    $event->alarmSend = Common::sendMail($subject, $body, $this->config);
                    $this->adapter->addEvent($inverter->id, $event);
                }
            }
        }
    }
}