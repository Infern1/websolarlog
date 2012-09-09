<?php
session_start();
include("cfg.php");

if(!isset($_SESSION['_login']) || !isset($_SESSION['_pass']))
{
	header('Location: index.php');
	exit();
}
else
{
	if(($_admin_login != $_SESSION['_login']) || ($_SESSION['_pass'] != $_admin_pass))
	{
		header('Location: index.php');
		exit();
	}
}
?>
