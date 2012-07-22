<?php

class LiveDataResult extends BaseResult {

    // Inverter one
    public $valueI1V; // Spanning in Volt
    public $valueI1A; // Ampere in Ampere
    public $valueI1P; // Vermogen in Watt

    // Inverter two
    public $valueI2V; // Spanning in Volt
    public $valueI2A; // Ampere in Ampere
    public $valueI2P; // Vermogen in Watt

    // Global
    public $valueGV; // Spanning in Volt
    public $valueGA; // Ampere in Ampere
    public $valueGP; // Vermogen in Watt


    public $valueSDTE;
    public $valueFRQ; // Frequency
    public $valueEFF; // Efficiency
    public $valueINVT;
    public $valueBOOT;
    public $valueKHWT;
    public $valuePMAXOTD;
    public $valuePMAXOTDTIME;

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->resulttype = "LiveDataResult";
    }

    public function setInverterOne($v, $a, $p) {
        $this->valueI1V = $v;
        $this->valueI1A = $a;
        $this->valueI1P = $p;
    }

    public function setInverterTwo($v, $a, $p) {
        $this->valueI2V = $v;
        $this->valueI2A = $a;
        $this->valueI2P = $p;
    }

    public function setGlobal($v, $a, $p) {
        $this->valueGV = $v;
        $this->valueGA = $a;
        $this->valueGP = $p;
    }
}
?>