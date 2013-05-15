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
	public $pvoutput;

	/**
	 * below are transient variables
	 */
	public $status;
	public $name;
	public $trend;
	public $avgPower;
	public $type;
}
?>