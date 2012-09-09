<?php
if (file_exists('/tmp/123AURORAPASS')) {

	$datareturn = shell_exec('more /tmp/123AURORAPASS');
	$array = preg_split("/[[:space:]]+/",$datareturn);

	$salt = '123aurora';
	$_admin_pass = md5($array[3].$salt);
	$_admin_login = 'admin';
}
?>
