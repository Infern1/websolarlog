<?php
class Live {
	public $id;
	public $INV;
	public $deviceId;
	public $I1V;
	public $I1A;
	public $I1P;
	public $I1Ratio;
	public $I2V;
	public $I2A;
	public $I2P;
	public $I2Ratio;
	public $GV;
	public $GA;
	public $GP;
	public $SDTE;
	public $time;
	public $FRQ;
	public $EFF;
	public $INVT;
	public $BOOT;
	public $KWHT;
	public $IP;

	/**
	 * below are transient variables
	 */
	public $status;
	public $name;
	public $trend;
	public $avgPower;
	public $type;
	
	/**
	 * Convert the live object to an history object
	 * @return History
	 */
	public function toHistory() {
		$history = new History();
		$history->INV = $this->INV;
		$history->deviceId = $this->deviceId;
		$history->SDTE = $this->SDTE;
		$history->time = $this->time;
		$history->I1V = $this->I1V;
		$history->I1A = $this->I1A;
		$history->I1P = $this->I1P;
		$history->I1Ratio = $this->I1Ratio;
		$history->I2V = $this->I2V;
		$history->I2A = $this->I2A;
		$history->I2P = $this->I2P;
		$history->I2Ratio = $this->I2Ratio;
		$history->GV = $this->GV;
		$history->GA = $this->GA;
		$history->GP = $this->GP;
		$history->FRQ = $this->FRQ;
		$history->EFF = $this->EFF;
		$history->INVT = $this->INVT;
		$history->BOOT = $this->BOOT;
		$history->KWHT = $this->KWHT;
		$history->pvoutput = false;
		$history->pvoutputErrorMessage = 0;
		
		// Calculate the day number
		$history->dayNum = date("z", $history->time) + 1;
		
		return $history;
	}
}
?>