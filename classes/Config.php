<?php
class Config
{

	/**
	 * Constructor
	 */
	function __construct()
	{
		// ### DATABASE CONFIG
		$this->basepath = dirname(dirname(__FILE__));
		$this->dbType='sqlite';
		$this->dbHost= $this->basepath.'/database/wsl.sdb';
		$this->dbDatabase='wsl';
		$this->dbUser='wsl';
		$this->dbPassword='wsl';
		$this->dbPort='0';


		// ### GENERAL FOR INVERTER #1
		$this->ADR='2';
		$this->LOGCOM=false;
		$this->PLANT_POWER='4600';
		$this->CORRECTFACTOR='0.987';
		$this->INITIALCOUNT='0';
		$this->INVNAME='East';

		// ### FRONT PAGE
		$this->YMAX='4600';
		$this->YINTERVAL='1000';
		$this->PRODXDAYS='20';

		// ### INFO DETAILS
		$this->LOCATION='Home Sweet Home';
		$this->LATITUDE = '52.061152';
		$this->LONGITUDE = '4.493330';
		$this->PANELS1='10 Aleo S_18 230W';
		$this->ROOF_ORIENTATION1='100';
		$this->ROOF_PICTH1='45';
		$this->PANELS2='10 Aleo S_18 230W';
		$this->ROOF_ORIENTATION2='100';
		$this->ROOF_PICTH2='45';

		// ### EXPECTED PRODUCTION
		$this->EXPECTEDPROD='3400';
		$this->EXPECTJAN='2.4';
		$this->EXPECTFEB='3.6';
		$this->EXPECTMAR='8.1';
		$this->EXPECTAPR='12.3';
		$this->EXPECTMAY='13.9';
		$this->EXPECTJUN='14.35';
		$this->EXPECTJUI='13.65';
		$this->EXPECTAUG='11.8';
		$this->EXPECTSEP='9.1';
		$this->EXPECTOCT='5.9';
		$this->EXPECTNOV='3';
		$this->EXPECTDEC='1.9';
	}
}
?>