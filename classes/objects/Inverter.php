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

    public $panels;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->id = 1;
        $this->name = "test";
        $this->description = "blabla ruimte";
        $this->initialkwh = 1200;
        $this->expectedkwh = 3750;
        $this->plantpower = 4800;
        $this->heading = "EAST";
        $this->correctionFactor = 0.987;
        $this->comAddress = 2;
        $this->comLog = false;

        $this->panels = array();
        $this->panels[] = new Panel();

        $panel = new Panel();
        $panel->id = 2;
        $panel->description='10 Aleo S_18 230W';
        $panel->roofOrientation='100';
        $panel->roofPitch='45';

        $this->panels[] = $panel;
    }
}
?>