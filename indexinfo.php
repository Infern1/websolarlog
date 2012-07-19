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
      $("#uptime").html(data[0].uptime);
      $("#cpuuse").html(data[0].cpuuse);
      $("#memtot").html(data[0].memtot);
      $("#memuse").html(data[0].memuse);
      $("#memfree").html(data[0].memfree);
      $("#diskuse").html(data[0].diskuse);
      $("#diskfree").html(data[0].diskfree);

      $('#barCpuUsage').progressbar({ value: data[0].cpuuse });
      $('#barMemUsage').progressbar({ value: data[0].memperc });
      $('#barDiskUsage').progressbar({ value: data[0].diskperc });
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
		</b><br />
			<hr align=left size=1 width="90%">
			<table width="60%" border=0>
				<tr>
					<td><?php
					$PLANT_POWER=$PLANT_POWER;
					echo "$lgLOCATION: $LOCATION<br />
					$lgPLANTPOWER: $PLANT_POWER W<br />";
					?>
					</td>
					<td align="left"><a href="plantdetails.php"><img src="images/zoom.png" width="32" height="32" border="0"> </a>
					</td>
				</tr>
			</table>
		</td>
		<td><img src="images/counter.png" width="16" height="16" border="0"><b>&nbsp;<?php echo "$lgCOUNTER";?>
		</b><br />
			<hr align=left size=1 width="90%"> <?php
			$dir = 'data/invt'.$invtnum.'/csv';
			$path = null;
			$timestamp = null;
			$di = new DirectoryIterator($dir);
			foreach ($di as $fileinfo) {
			    if (!$fileinfo->isDot()) {
			        if ($fileinfo->getMTime() > $timestamp) {
			            // current file has been modified more recently
			            // than any other file we've checked until now
			            $path = $dir."/" . $fileinfo->getFilename();
			        }
			    }
			}

			$lines=file($path);
			$contalines = count($lines);
			$array = explode(";",$lines[$contalines-1]);
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

			echo "$lgTOTALPROD $KWHP kWh<br />
			<img src='images/leaf.png' width='16' height='16' border='0' /> $CO2 $CO2v CO<sub>2</sub>&nbsp;<img src='images/info10.png' width='10' height='10' border='0' title='$lgECOLOGICALINFOB' />
			</td></tr>
			<tr valign='top'><td>
			<img src='images/monitor.png' width='16' height='16' border='0' /><b>&nbsp;$lgINVERTERINFO</b>&nbsp;<img src='images/info10.png' width='10' height='10' border='0' title=\"$lgINVERTERINFOB $updtd)\"' /><br />
			<hr align=left size=1 width='90%' />
			";
			$lines=file($info);
			$contalines = count($lines);
			echo "$lines[1]<br />$lines[3]<br />$lines[5]<br />$lines[7]<br />$lines[9]<br />$lines[10]<br />$lines[12]<br />";
			?>
		</td>
		<td><img src="images/gear.png" width="16" height="16" border="0" /><b>&nbsp;<?php echo "$lgLOGGERINFO";?>
		</b><br />
			<hr align=left size=1 width="90%" />
			Uptime: <span id='uptime'><?php system("uptime");?> </span><br />
			OS: <?php system("uname -ors"); ?> <br />
			System: <?php system("uname -nmi"); ?> <br /> <?php system("cat /proc/cpuinfo | grep 'Processor'");?>
			CPU Use: <span id='cpuuse'><?php system("ps -eo pcpu,pid -o comm= | sort -k1 -n -r | head -1 | awk '{ print $1 }'");?> </span>% <br />
			<div id='barCpuUsage'></div>
			Total Memory: <span id='memtot'><?php system("free -t -m | grep 'Total' | awk '{print $2}'"); ?></span>MB, Used: <span id='memuse'><?php system("free -t -m | grep 'Total' | awk '{print $3}'"); ?> </span>MB, Free: <span id='memfree'><?php system("free -t -m | grep 'Total' | awk '{print $4}'"); ?></span>MB <br />
			<div id='barMemUsage'></div>
			Disk Usage: <span id='diskuse'><?php system("df -h | grep root | awk '{print $2}'"); ?> </span>, <span id='diskfree'><?php system("df -h | grep root | awk '{print $4}'"); ?> </span>&nbsp;avail.
			<div id='barDiskUsage'></div>
			<br />Software: <?php echo $VERSION;?>
		</td>
	</tr>
	<tr valign="top">
		<td>
		    <img src="images/calendar-day.png" width="16" height="16" border="0" />
		    <b>&nbsp;<?php echo "$lgEVENTS";?></b><br />
		    <hr align=left size=1 width="90%" />
			<div id="events" class="events"></div>
            <script type="text/javascript">
              WSL.init_events(<?php echo($invtnum); ?>, "#events"); // Initial load fast
              window.setInterval(function(){WSL.init_events(<?php echo($invtnum); ?>, "#events");}, 10000); // every 10 seconds
            </script>
		</td>
		<td><?php
		if ($PVOUTPUT==true) {
		    echo "<img src='images/link.png' width='16' height='16' border='0' /><b>&nbsp;PVoutput</b><br />
		    <hr align=left size=1 width='90%' />
		    <br />
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
