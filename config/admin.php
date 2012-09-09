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
										define('checkaccess', TRUE);
										include("config_main.php");

										echo "<div align=center><br><form action='admin_gen.php' method='post'>
										<fieldset style='width:80%;'>
										<legend><b>General </b></legend>
										<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
										<tr>
										<td>Port <input type='text' name='PORT2' value='$PORT' size=10></td>
										<td>Communication options <img src='../images/info10.png' width='10' height='10' border='0' title='If you got com. errors, please read the aurora manual for all available options'> <input type='text' name='COMOPTION2' value='$COMOPTION' size=10></td>
										</tr><tr>
										<td>Com. debug <img src='../images/info10.png' width='10' height='10' border='0' title='Check .err files in data/errors'>
										<select name='DEBUG2'>";
										if ($DEBUG==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										</td>
										<td>Sync. inverter time daily <img src='../images/info10.png' width='10' height='10' border='0' title='Beware: It may clear the partials counters on some inverters'>
										<select name='SYNC2'>";
										if ($SYNC==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										</td></tr>
										<tr><td>Number of inverter(s) <input type='number' name='NUMINV2' value='$NUMINV' size=2 min='1' max='64'></td>
										<td>
										Auto-pooling <img src='../images/info10.png' width='10' height='10' border='0' title='Start and stop the pooling with sunrise/sunset according to your geographical location'>
										<select name='AUTOMODE2'>";
										if ($AUTOMODE==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										</td></tr>
										<tr>
										<td>Latitude <img src='../images/info10.png' width='10' height='10' border='0' title='For sunrise and sunset'> <input type='text' name='LATITUDE2' value='$LATITUDE' size=5></td>
										<td>Longitude <input type='text' name='LONGITUDE2' value='$LONGITUDE' size=5></td>
										</tr>
										<tr><td colspan=2><b>Web pages : </b></td></tr>
										<tr><td>Title <input type='text' name='TITLE2' value='$TITLE' size=50></td>
										<td>Subtitle <input type='text' name='SUBTITLE2' value='$SUBTITLE' size=50></td>
										</tr>
										<tr><td colspan=2><b>Email : </b></td></tr>
										<tr><td>Your email adress <img src='../images/info10.png' width='10' height='10' border='0' title='Check the guide on how to configure a SMTP client for PHP'> <input type='email' name='EMAIL2' value='$EMAIL'></td>
										<td>Email filter <img src='../images/info10.png' width='10' height='10' border='0' title='Put some words seperated by a comma, if you wish to filter some messages'> <input type='text' name='FILTER2' value='$FILTER'></td></tr>
										<tr><td>Do you want to received alarms ?
										<select name='SENDALARMS2'>";
										if ($SENDALARMS==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										</td>
										<td>Do you want to received messages ?
										<select name='SENDMSGS2'>";
										if ($SENDMSGS==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										</td></tr>
										<tr><td colspan=2><b>Daily cleanup : </td></tr>
										<tr></td><td>Keep <input type='number' name='KEEPDDAYS2' value='$KEEPDDAYS' size=2 min='0'>fully detailed days <img src='../images/info10.png' width='10' height='10' border='0' title='0 is unlimited'>
										<td>Keep logs size to <input type='number' name='AMOUNTLOG2' value='$AMOUNTLOG' size=2 min='0'>lines</td></tr>
										<tr><td colspan=2><b>PVoutput.org : </b></td></tr>
										<tr><td colspan=2>Live Feed <img src='../images/info10.png' width='10' height='10' border='0' title='Do not forget to change the status interval to 5min in your PVoutput system settings'>
										<select name='PVOUTPUT2'>";
										if ($PVOUTPUT==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										API key <input type='text' size=42 name='APIKEY2' value='$APIKEY'><img src='../images/info10.png' width='10' height='10' border='0' title='See in the PVoutput API settings'>&nbsp;SYSID <input type='text' size=3 name='SYSID2' value='$SYSID'></td>
										</tr>
										</table>
										</fieldset>
										<div align=center><input type='submit' value='Save'></div>
										</form>
										";
										if (!empty ($_POST['invt_num'])) {
											$invt_num= $_POST['invt_num'];
										} else { $invt_num=1;
										}
										if ($NUMINV>1) { //multi
											echo "
											<table border='0' cellspacing='5' cellpadding='0' width='80%' align='center'><tr><td>
											<form method='POST' action='admin.php'>
											<div align=left><b>Select an inverter </b><select name='invt_num' onchange='this.form.submit()'>
											";
											for ($i=1;$i<=$NUMINV;$i++) {
												if ($invt_num==$i) {
													echo "<option SELECTED>";
												} else {
													echo "<option>";
												}
												echo "$i</option>";
											}
											echo "
											</select></div>
											</form></td></tr></table>";
										}// multi
										if (file_exists("config_invt".$invt_num.".php")) {
											include("config_invt".$invt_num.".php");
										} else {
											include("config_invt1.php");
											$ADR='';
											$CORRECTFACTOR='1';
											$INITIALCOUNT='0';
											$INVNAME='';
										}
										echo "
										<div align=center><form action='admin_invt.php' method='post'>
										<fieldset style='width:80%;'>
										<legend><b>Inverter #$invt_num </b></legend>
										<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
										<tr>
										<td>RS485 Adress <img src='../images/info10.png' width='10' height='10' border='0' title='2 by default'> <input type='number' name='ADR2' value='$ADR' size=2 min='1' max='256'></td>
										<td>Log com. errors <img src='../images/info10.png' width='10' height='10' border='0' title='Will log communication error in event'>
										<select name='LOGCOM2'>";
										if ($LOGCOM==true) {
											echo "<option SELECTED value=true>Yes</option><option value=false>No</option>";
										} else {
											echo "<option value=true>Yes</option><option SELECTED value=false>No</option>";
										}
										echo "
										</select>
										</td>
										<td>Plant Power <input type='number' name='PLANT_POWER2' value='$PLANT_POWER' size=2 min='0'>W</td>
										</tr>
										<tr><td>Correction factor <img src='../images/info10.png' width='10' height='10' border='0' title='If your inverter production is not equal with another green counter, you may adujst this parameter
										(If your inverter is 2% too optimist, put 0.98)'> <input type='text' name='CORRECTFACTOR2' value='$CORRECTFACTOR' size=3 min='0' max='2'></td>
										</td><td>Initial counter value <img src='../images/info10.png' width='10' height='10' border='0' title='If your total counter have been reset or passed 99999kWh'> <input type='number' name='INITIALCOUNT2' value='$INITIALCOUNT' size=3 min=0>kWh</td>
										<td></td></tr>
										</table>
										";
										echo "<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
										<tr><td>Short description name <input type='text' name='INVNAME2' value='$INVNAME' size=10></td></tr>
										</table>
										<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
										<tr><td colspan=2><b>Front page : </b></td></tr>
										<tr><td>Maximum of the yAxis <input type='number' name='YMAX2' value='$YMAX' min='$PLANT_POWER' size=3>W</td>
										<td>Tick interval of yAxis <input type='number' name='YINTERVAL2' value='$YINTERVAL' size=3 min=200>W</td>
										<tr>
										<td>Number of last days to display <input type='number' name='PRODXDAYS2' value='$PRODXDAYS' size=3 min=7 max=60>days</td><td></td>
										</tr>
										</table>
										<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
										<tr><td colspan=2><b>Info details :</td></tr>
										<tr><td>Location <input type='text' name='LOCATION2' value='$LOCATION'></td>
										</tr>
										<tr><td><u>String 1 detail :</u></td></tr>
										<tr><td>Panels for array1 <input type='text' name='PANELS12' value='$PANELS1' size=20><br>
										Roof orientation <input type='number' name='ROOF_ORIENTATION12' value='$ROOF_ORIENTATION1' size=2>°
										Roof pitch <input type='number' name='ROOF_PICTH12' value='$ROOF_PICTH1' size=2>°</td></tr>
										<tr><td><u>String 2 detail :</u></td></tr>
										<tr><td>Panels for array2 <input type='text' name='PANELS22' value='$PANELS2' size=20><br>
										Roof orientation <input type='number' name='ROOF_ORIENTATION22' value='$ROOF_ORIENTATION2' size=2>°
										Roof pitch <input type='number' name='ROOF_PICTH22' value='$ROOF_PICTH2' size=2>°</td></tr>
										</table>
										<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
										<tr><td colspan=2><b>Expected Production : </b></td></tr>
										<tr><td colspan=4>Expected annual production <input type='number' name='EXPECTEDPROD2' value='$EXPECTEDPROD' size=4>kWh</td>
										</tr>
										<tr><td colspan=4><u>Ratio :</u></td></tr>
										<tr><td>Jan. <input type='text' name='EXPECTJAN2' value='$EXPECTJAN' size=2>%</td>
										<td>Apr. <input type='text' name='EXPECTAPR2' value='$EXPECTAPR' size=2>%</td>
										<td>Jul. <input type='text' name='EXPECTJUI2' value='$EXPECTJUI' size=2>%</td>
										<td>Oct. <input type='text' name='EXPECTOCT2' value='$EXPECTOCT' size=2>%</td>
										</tr>
										<tr><td>Feb. <input type='text' name='EXPECTFEB2' value='$EXPECTFEB' size=2>%</td>
										<td>May <input type='text' name='EXPECTMAY2' value='$EXPECTMAY' size=2>%</td>
										<td>Aug. <input type='text' name='EXPECTAUG2' value='$EXPECTAUG' size=2>%</td>
										<td>Nov. <input type='text' name='EXPECTNOV2' value='$EXPECTNOV' size=2>%</td>
										</tr>
										<tr><td>Mar. <input type='text' name='EXPECTMAR2' value='$EXPECTMAR' size=2>%</td>
										<td>Jun. <input type='text' name='EXPECTJUN2' value='$EXPECTJUN' size=2>%</td>
										<td>Sep. <input type='text' name='EXPECTSEP2' value='$EXPECTSEP' size=2>%</td>
										<td>Dec. <input type='text' name='EXPECTDEC2' value='$EXPECTDEC' size=2>%</td>
										</tr>
										</table>
										</fieldset>
										<table border='0' cellspacing='5' cellpadding='0' width='90%' align='center'>
										<tr><td colspan=2 align=center><input type='submit' value='Save'>&nbsp;<INPUT TYPE='button' onClick=\"location.href='logout.php'\" value='Logout'></td></tr>
										</table>
										<input type='hidden' name='invt_num2' value='$invt_num'>
										</form></div>";
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
