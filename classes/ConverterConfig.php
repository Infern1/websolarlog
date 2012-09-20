<?php

class ConverterConfig
{
    public $id;
    public $name;
    public $description;
    public $initialkwh;
    public $power;
    public $heading;
    public $correctionFactor;
    public $comAddress;
    public $comLog;

    public $pnl1Description;
    public $pnl1RoofOrientation;
    public $pnl1RoofPitch;
    public $pnl2Description;
    public $pnl2RoofOrientation;
    public $pnl2RoofPitch;


    /**
     * Constructor
     */
    function __construct()
    {
        $this->id = 1;
        $this->name = "test";
        $this->description = "blabla ruimte";
        $this->initialkwh = 1200;
        $this->power = 4800;
        $this->heading = "EAST";
        $this->correctionFactor = 0.987;
        $this->comAddress = 2;
        $this->comLog = false;

        $this->pnl1Description='10 Aleo S_18 230W';
        $this->pnl1RoofOrientation='100';
        $this->pnl1RoofPitch='45';

        $this->pnl2Description='10 Aleo S_18 230W';
        $this->pnl2RoofOrientation='100';
        $this->pnl2RoofPitch='45';
    }
}
?>