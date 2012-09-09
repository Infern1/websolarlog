<?php include('control.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>123Aurora Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css"
	type="text/css">
</head>
<body>
	<table width="90%" border="0" cellspacing="0" cellpadding="0"
		align="center" height="90%">
		<tr bgcolor="#FFFFFF">
			<td class="cadretopleft" width="128"><img
				src="../styles/default/images/sun12880.png" width="128" height="80"
				alt="123Aurora"></td>
			<td class="cadretop" align="center"><b>123Aurora Administration</b><br>
			</td>
		</tr>
		<tr valign="top">
			<td height="100%" COLSPAN="3">
				<table width="100%" border="0" cellpadding="0" cellspacing="0"
					align="center" height="100%">
					<tr valign="top">
						<td width="128" class="cadrebotleft" bgcolor="#CCCC66"
							height="98%">
							<div class="menu"></div>
						</td>
						<td class="cadrebotright" bgcolor="#d3dae2" height="98%">
							<table border="0" cellspacing="10" cellpadding="0" width="100%"
								height="100%" align="center">
								<tr valign="top">
									<td>
										<!-- #BeginEditable "mainbox" --> <?php

										if (!empty ($_POST['PORT2'])) {
											$PORT2 = $_POST['PORT2'];
										}
										if (!empty ($_POST['COMOPTION2'])) {
											$COMOPTION2 = $_POST['COMOPTION2'];
										}
										if (!empty ($_POST['DEBUG2'])) {
											$DEBUG2 = $_POST['DEBUG2'];
										}
										if (!empty ($_POST['SYNC2'])) {
											$SYNC2 = $_POST['SYNC2'];
										}
										if (!empty ($_POST['NUMINV2'])) {
											$NUMINV2 = $_POST['NUMINV2'];
										}
										if (!empty ($_POST['AUTOMODE2'])) {
											$AUTOMODE2 = $_POST['AUTOMODE2'];
										}
										if (!empty ($_POST['LATITUDE2'])) {
											$LATITUDE2 = $_POST['LATITUDE2'];
										}
										if (!empty ($_POST['LONGITUDE2'])) {
											$LONGITUDE2 = $_POST['LONGITUDE2'];
										}
										if (!empty ($_POST['SENDALARMS2'])) {
											$SENDALARMS2 = $_POST['SENDALARMS2'];
										}
										if (!empty ($_POST['SENDMSGS2'])) {
											$SENDMSGS2 = $_POST['SENDMSGS2'];
										}
										if (!empty ($_POST['FILTER2'])) {
											$FILTER2 = $_POST['FILTER2'];
										}
										if (!empty ($_POST['EMAIL2'])) {
											$EMAIL2 = $_POST['EMAIL2'];
										}
										if (!empty ($_POST['KEEPDDAYS2'])) {
											$KEEPDDAYS2 = $_POST['KEEPDDAYS2'];
										}
										if (!empty ($_POST['AMOUNTLOG2'])) {
											$AMOUNTLOG2 = $_POST['AMOUNTLOG2'];
										}
										if (!empty ($_POST['TITLE2'])) {
											$TITLE2 = $_POST['TITLE2'];
										}
										if (!empty ($_POST['SUBTITLE2'])) {
											$SUBTITLE2 = $_POST['SUBTITLE2'];
										}
										if (!empty ($_POST['PVOUTPUT2'])) {
											$PVOUTPUT2 = $_POST['PVOUTPUT2'];
										}
										if (!empty ($_POST['APIKEY2'])) {
											$APIKEY2 = $_POST['APIKEY2'];
										}
										if (!empty ($_POST['SYSID2'])) {
											$SYSID2 = $_POST['SYSID2'];
										}

										if (!is_numeric($LATITUDE2)) {
											echo "LATITUDE value not correct<br>";
											$Err=true;
										}
										if (!is_numeric($LONGITUDE2)) {
											echo "LONGITUDE value not correct<br>";
											$Err=true;
										}

										function testmail($adress)
										{
											$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
											if(preg_match($Syntaxe,$adress))
												return true;
											else
												return false;
										}

										if(!testmail($EMAIL2)) {
											echo "EMAIL is not correct<br>";
											$Err=true;
										}

										if ($Err!=true) {
											$myFile = 'config_main.php';
											$fh = fopen($myFile, 'w+') or die("<font color='#8B0000'><b>Can't open $myFile file. Configuration not saved !</b></font>");
											$stringData="<?php
											if(!defined('checkaccess')){die('Direct access not permitted');}

											// ### GENERAL
											\$PORT='$PORT2';
											\$COMOPTION='$COMOPTION2';
											\$DEBUG=$DEBUG2;
											\$SYNC=$SYNC2;
											\$NUMINV=$NUMINV2;
											\$AUTOMODE=$AUTOMODE2;
											// ### LOCALIZATION
											\$LATITUDE='$LATITUDE2';
											\$LONGITUDE='$LONGITUDE2';

											// ### WEB PAGE
											\$TITLE='$TITLE2';
											\$SUBTITLE='$SUBTITLE2';

											// ### ALARMS AND MESSAGE EMAILS
											\$SENDALARMS=$SENDALARMS2;
											\$SENDMSGS=$SENDALARMS2;
											\$FILTER='$FILTER2';
											\$EMAIL='$EMAIL2';

											// ### CLEANUP
											\$KEEPDDAYS='$KEEPDDAYS2';
											\$AMOUNTLOG='$AMOUNTLOG2';

											// ### PVOUTPUT.org
											\$PVOUTPUT=$PVOUTPUT2;
											\$APIKEY='$APIKEY2';
											\$SYSID='$SYSID2';
											?>
											";
											fwrite($fh, $stringData);
											fclose($fh);

											echo "
											<br><div align=center><font color='#228B22'><b>Main configuration updated</b></font>
											<br>&nbsp;
											<br>&nbsp;
											<INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>
											</div>
											";
										} else {
											echo "
											<br><div align=center><font color='#8B0000'><b>Error configuration not saved !</b></font><br>
											<INPUT type='button' value='Back' onclick='history.back()'>
											</div>
											";
										}

										?> <!-- #EndEditable -->

									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br>
</body>
</html>
