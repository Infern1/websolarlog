<?php include("config/version.php");
define('checkaccess', TRUE);
include("config/config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>123aurora debug</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>
	<br>
	<b>Start as root 123aurora start, then press F5 to refresh commands</b>
	<hr>
	<table border=0 CELLPADDING=10>
		<tr>
			<td><b>Checking hardware :</b><br> <?php
			$datareturn = shell_exec('lsusb');
			?> <textarea style="resize: none; background-color: #DCDCDC"
					cols="100" rows="5">
<?php echo $datareturn; ?>
</textarea>
			</td>
			<td>Check if your USB/RS485 converter is present. eg: Chipset PL2303.</td>
		</tr>
		<tr>
			<td><?php
			$datareturn = shell_exec('lsmod');
			?> <textarea style="resize: none; background-color: #DCDCDC"
					cols="100" rows="5">
<?php echo $datareturn; ?>
</textarea>
			</td>
			<td>usbserial module <b>should</b> be loaded
			</td>
		</tr>
		<tr>
			<td><br> <b>Checking PHP:</b><br> <?php echo 'PHP version: ' . phpversion();
			$input = '{ "jsontest" : " <br>Json extension loaded" }';
			$val = json_decode($input, true);
			if ($val["jsontest"]!="") {
				echo $val["jsontest"];
			} else {
				echo "<br>Json extension -NOT- loaded";
			}

			if (extension_loaded('calendar')) {
				echo "<br>Calendar extension loaded";
			} else {
				echo "<br>Calendar extension -NOT- loaded";
			}

			?>
			</td>
			<td>PHP > v5<br> json and calendar extension <b>must</b> be loaded
			</td>
		</tr>
		<tr>
			<td><br> <b>Checking Software:</b><br> <?php
			$datareturn = shell_exec('ps -ef | grep aurora | grep -v grep');
			?> <textarea style="resize: none; background-color: #DCDCDC"
					cols="100" rows="5">
<?php echo $datareturn; ?>
</textarea>
			</td>
			<td>Version : <?php echo $VERSION;?> <br>You <b>should</b> see
				123aurora.sh start and sometimes aurora -a2 -c -T -d0 -e
			</td>
		</tr>
		<tr>
			<td>Locks : <br> <?php
			$datareturn = shell_exec('ls -l /var/lock/');
			?> <textarea style="resize: none; background-color: #DCDCDC"
					cols="100" rows="5">
<?php echo $datareturn; ?>
</textarea>
			</td>
			<td>You <b>should</b> see 123aurora file and sometimes LCK..ttyUSB0
			</td>
		</tr>
		<tr>
			<td>Flags :<br> <?php
			$datareturn = shell_exec('find data/ -name \*lock\*');
			?> <textarea style="resize: none; background-color: #DCDCDC"
					cols="100" rows="3">
<?php echo $datareturn; ?>
</textarea>
			</td>
			<td>You may see a lock file</td>
		</tr>
		<tr>
			<td><b>Checking aurora communication app :</b> <br>Current settings
				are <?php 
				if ($DEBUG!=true) {
					echo "-a$ADR -c -T $COMOPTION -d0 -e $PORT";
				} else {
					echo "-b -a$ADR -c -T $COMOPTION -d0 -e $PORT 2> data/errors/de.err";
				}
				?> <br> <br> <a href="data/invt1/errors/">Aurora communication
					errors</a> (You have to enable com. debug to get all details) <br>
			</td>
			<td></td>
		</tr>
	</table>
</body>