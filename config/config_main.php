<?php
if(!defined('checkaccess')){
	die('Direct access not permitted');
}

// ### GENERAL
$PORT='/dev/ttyUSB0';
$COMOPTION='-Y3 -l3 -M3';
$DEBUG=false;
$SYNC=false;
$NUMINV=1;
$AUTOMODE=false;
// ### LOCALIZATION
$LATITUDE='50.61';
$LONGITUDE='4.635';

// ### WEB PAGE
$TITLE='WebSolarLog';
$SUBTITLE='« The sun is new each day »';

// ### ALARMS AND MESSAGE EMAILS
$SENDALARMS=false;
$SENDMSGS=false;
$FILTER='';
$EMAIL='no@be.com';

// ### CLEANUP
$KEEPDDAYS='365';
$AMOUNTLOG='500';

// ### PVOUTPUT.org
$PVOUTPUT=false;
$APIKEY='abc123';
$SYSID='';
?>