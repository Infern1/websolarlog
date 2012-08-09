<?php
/**
 * LiveDataResult class file
 *
 * PHP Version 5.2
 *
 * @category  Classes
 * @package   Results
 * @author    Martin Diphoorn <martin@diphoorn.com>
 */

/**
 * LiveDataResult
 *
 * @category  Classes
 * @package   Results
 * @author    Martin Diphoorn <martin@diphoorn.com>
 * @access    public
 * @since     File available since Release 0.0.0
 */
class LiveDataResult extends BaseResult {

    // MPP/string one
    public $valueI1V; // Spanning in Volt
    public $valueI1A; // Ampere in Ampere
    public $valueI1P; // Vermogen in Watt

    // MPP/string two
    public $valueI2V; // Spanning in Volt
    public $valueI2A; // Stroom in Ampere
    public $valueI2P; // Vermogen in Watt

    // Grid values
    public $valueGV; // Spanning in Volt
    public $valueGA; // Stroom in Ampere
    public $valueGP; // Vermogen in Watt
    public $valueFRQ; // Grid Frequency

    public $valueSDTE; // ?? datetime ??
    public $valueEFF; // Efficiency
    public $valueINVT; // Inverter Temp.
    public $valueBOOT; //  Booster Temp.
    public $valueKWHT; // kiloWattHourTotal
    public $valuePMAXOTD; // Power Max of today
    public $valuePMAXOTDTIME; // Power Max of today Time

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->resulttype = "LiveDataResult";
    }

    public function setMppOne($v, $a, $p) {
        $this->valueI1V = $v;
        $this->valueI1A = $a;
        $this->valueI1P = $p;
    }

    public function setMppTwo($v, $a, $p) {
        $this->valueI2V = $v;
        $this->valueI2A = $a;
        $this->valueI2P = $p;
    }

    public function setGrid($v, $a, $p) {
        $this->valueGV = $v;
        $this->valueGA = $a;
        $this->valueGP = $p;
    }
}
?>