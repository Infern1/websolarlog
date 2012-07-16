<?php
include("styles/globalheader.php");
include("config/version.php");
if(!empty($_POST['invtnum'])) {
    $invtnum=$_POST['invtnum'];
} else {$invtnum=1;
}

$config_invt="config/config_invt".$invtnum.".php";
include("$config_invt");
?>
<script type="text/javascript">
  function updateit() {

  $.getJSON("programs/programloggerinfo.php", function(data){
      json = eval(data);

      document.getElementById("uptime").innerHTML = json[0].uptime;
      document.getElementById("cpuuse").innerHTML = json[0].cpuuse;
      document.getElementById("memtot").innerHTML = json[0].memtot;
      document.getElementById("memuse").innerHTML = json[0].memuse;
      document.getElementById("memfree").innerHTML = json[0].memfree;
      document.getElementById("diskuse").innerHTML = json[0].diskuse;
      document.getElementById("diskfree").innerHTML = json[0].diskfree;

      $('#barCpuUsage').progressbar({ value: json[0].cpuuse });
      $('#barMemUsage').progressbar({ value: json[0].memperc });
      $('#barDiskUsage').progressbar({ value: json[0].diskperc });
    });
  };
updateit();
setInterval(updateit, 1000);
</script>
<?php
if ($NUMINV>1) {
    $currentFile = $_SERVER["PHP_SELF"];

    echo"<table width='95%' border=0 align=center cellpadding=0 CELLSPACING=0>
    <tr><td>
    <form method='POST' action=\"$currentFile\"><select name='invtnum' onchange='this.form.submit()'>";
    for ($i=1;$i<=$NUMINV;$i++) {
        if ($invtnum==$i) {
            echo "<option SELECTED value=$i>";
        } else {
            echo "<option value=$i>";
        }
        echo "$lgINVT$i</option>";
    }
    echo "</select></form></td></tr></table>";
} ?>

<table width="95%" border=0 align=center cellpadding="0" CELLSPACING="20">
	<tr valign="top">
		<td><img src="images/brightness.png" width="16" height="16" border="0"><b>&nbsp;<?php echo "$lgPLANTINFO";?>
		</b><br>
			<hr align=left size=1 width="90%">
			<table width="60%" border=0>
				<tr>
					<td><?php
					$PLANT_POWER=$PLANT_POWER;
					echo "$lgLOCATION: $LOCATION<br>
					$lgPLANTPOWER: $PLANT_POWER W<br>";
					?>
					</td>
					<td align="left"><a href="plantdetails.php"><img src="images/zoom.png" width="32" height="32" border="0"> </a>
					</td>
				</tr>
			</table>
		</td>
		<td><img src="images/counter.png" width="16" height="16" border="0"><b>&nbsp;<?php echo "$lgCOUNTER";?>
		</b><br>
			<hr align=left size=1 width="90%"> <?php
			$dir = 'data/invt'.$invtnum.'/csv';
			$cmd='ls -r '.$dir.' | grep .csv';
			$output = shell_exec($cmd);
			$output = explode ("\n",$output);
			$log=$dir."/".$output[0];
			$lines=file($log);
			$contalines = count($lines);
			$array = preg_split("/;/",$lines[$contalines-1]);
			$KWHP=($array[14]+$INITIALCOUNT)*$CORRECTFACTOR;
			$CO2=(($KWHP/1000)*456);
			if ($CO2>1000) {
			    $CO2v="Tonnes";
			    $CO2=round(($CO2/1000),3);
			}
			else {
			    $CO2v="Kg";
			    $CO2=round(($CO2),1);
			}
			$KWHP=round($KWHP,1);

			$info="data/invt$invtnum/infos/infos.txt";

			$updtd=date ("d M H:i.", filemtime($info));

			echo "$lgTOTALPROD $KWHP kWh<br>
			<img src='images/leaf.png' width='16' height='16' border='0'> $CO2 $CO2v CO<sub>2</sub>&nbsp;<img src='images/info10.png' width='10' height='10' border='0' title='$lgECOLOGICALINFOB' />
			</td></tr>
			<tr valign='top'><td>
			<img src='images/monitor.png' width='16' height='16' border='0'><b>&nbsp;$lgINVERTERINFO</b>&nbsp;<img src='images/info10.png' width='10' height='10' border='0' title=\"$lgINVERTERINFOB $updtd)\"' /><br>
			<hr align=left size=1 width='90%' />
			";
			$lines=file($info);
			$contalines = count($lines);
			echo "$lines[1]<br>";
			echo "$lines[3]<br>";
			echo "$lines[5]<br>";
			echo "$lines[7]<br>";
			echo "$lines[9]<br>";
			echo "$lines[10]<br>";
			echo "$lines[12]<br>";
			?>
		</td>
		<td><img src="images/gear.png" width="16" height="16" border="0"><b>&nbsp;<?php echo "$lgLOGGERINFO";?>
		</b><br>
			<hr align=left size=1 width="90%" />
			Uptime: <span id='uptime'><?php system("uptime");?> </span><br>
			OS: <?php system("uname -ors"); ?> <br>
			System: <?php system("uname -nmi"); ?> <br> <?php system("cat /proc/cpuinfo | grep 'Processor'");?>
			CPU Use: <span id='cpuuse'><?php system("ps -eo pcpu,pid -o comm= | sort -k1 -n -r | head -1 | awk '{ print $1 }'");?> </span>% <br>
			<div id='barCpuUsage'></div>
			Total Memory: <span id='memtot'><?php system("free -t -m | grep 'Total' | awk '{print $2}'"); ?></span>MB, Used: <span id='memuse'><?php system("free -t -m | grep 'Total' | awk '{print $3}'"); ?> </span>MB, Free: <span id='memfree'><?php system("free -t -m | grep 'Total' | awk '{print $4}'"); ?></span>MB <br>
			<div id='barMemUsage'></div>
			Disk Usage: <span id='diskuse'><?php system("df -h | grep root | awk '{print $2}'"); ?> </span>, <span id='diskfree'><?php system("df -h | grep root | awk '{print $4}'"); ?> </span>&nbsp;avail.
			<div id='barDiskUsage'></div>
			<br>Software: <?php echo $VERSION;?>
		</td>
	</tr>
	<tr valign="top">
		<td><img src="images/calendar-day.png" width="16" height="16" border="0"><b>&nbsp;<?php echo "$lgEVENTS";?>
		</b><br>
			<hr align=left size=1 width="90%"> <textarea style="resize: none; background-color: #DCDCDC" cols="55" rows="10">
<?php
$filename="data/invt$invtnum/infos/events.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);
echo $contents;
?>
</textarea>
		</td>
		<td><?php
		if ($PVOUTPUT==true) {
		    echo "<img src='images/link.png' width='16' height='16' border='0'><b>&nbsp;PVoutput</b><br>
		    <hr align=left size=1 width='90%'>
		    <br>
		    <script src='http://pvoutput.org/widget/inc.jsp'></script>
		    <script src='http://pvoutput.org/widget/graph.jsp?sid=$SYSID&w=200&h=80&n=1&d=1&t=1&c=1'></script>
		    <font size='-1'><a href='http://pvoutput.org/listteam.jsp?tid=317'>(123Aurora Team)</a></font>
		    ";
		}
		?>
		</td>
	</tr>
</table>

<?php include("styles/".$user_style."/footer.php"); ?>
