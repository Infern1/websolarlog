<?php
class Config
{
	public $config;

	function getConfig(){
		// ### GENERAL FOR INVERTER #1
		$config->ADR='2';
		$config->LOGCOM=false;
		$config->PLANT_POWER='4600';
		$config->CORRECTFACTOR='0.987';
		$config->INITIALCOUNT='0';
		$config->INVNAME='East';

		// ### FRONT PAGE
		$config->YMAX='4600';
		$config->YINTERVAL='1000';
		$config->PRODXDAYS='20';

		// ### INFO DETAILS
		$config->LOCATION='Home Sweet Home';
		$config->PANELS1='10 Aleo S_18 230W';
		$config->ROOF_ORIENTATION1='100';
		$config->ROOF_PICTH1='45';
		$config->PANELS2='10 Aleo S_18 230W';
		$config->ROOF_ORIENTATION2='100';
		$config->ROOF_PICTH2='45';

		// ### EXPECTED PRODUCTION
		$config->EXPECTEDPROD='3400';
		$config->EXPECTJAN='2.4';
		$config->EXPECTFEB='3.6';
		$config->EXPECTMAR='8.1';
		$config->EXPECTAPR='12.3';
		$config->EXPECTMAY='13.9';
		$config->EXPECTJUN='14.35';
		$config->EXPECTJUI='13.65';
		$config->EXPECTAUG='11.8';
		$config->EXPECTSEP='9.1';
		$config->EXPECTOCT='5.9';
		$config->EXPECTNOV='3';
		$config->EXPECTDEC='1.9';
		return $config;
	}

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->config = array();
	}
}
?>