<?php
class Event {
    public $INV;
    public $SDTE;
    public $time;
    public $type;
    public $event;
    public $alarmSend;

    function __construct($inverterId, $date, $type, $event) {
        $this->INV = $inverterId;
        $this->SDTE = date("Ymd H:i:s", $date);
        $this->time = $date;
        $this->type = $type;
        $this->event = $event;
        $this->alarmSend = false;
    }
}


?>

