<?php

class Inverter
{
    public $id;
    public $name;
    public $description;
    public $initialkwh;
    public $expectedkwh;
    public $plantpower;
    public $heading;
    public $correctionFactor;
    public $comAddress;
    public $comLog;
    public $syncTime;

    public $panels;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->id = -1;
        $this->name = "";
        $this->description = "";
        $this->initialkwh = 0;
        $this->expectedkwh = 0;
        $this->plantpower = 0;
        $this->heading = "";
        $this->correctionFactor = 0.987;
        $this->comAddress = 2;
        $this->comLog = false;
        $this->syncTime = false;
        $this->panels = array();
    }
}
?>