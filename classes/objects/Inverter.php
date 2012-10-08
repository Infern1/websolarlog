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

    public $expectedJAN;
    public $expectedFEB;
    public $expectedMRT;
    public $expectedAPR;
    public $expectedMAY;
    public $expectedJUN;
    public $expectedJUL;
    public $expectedAUG;
    public $expectedSEP;
    public $expectedOCT;
    public $expectedNOV;
    public $expectedDEC;

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