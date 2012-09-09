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
<div id="getplantinfo"
	class="getplantinfo"></div>
<script type="text/javascript">
              WSL.init_plantInfo(<?php echo($invtnum); ?>, "#getplantinfo"); // Initial load fast
              window.setInterval(function(){WSL.init_plantInfo(<?php echo($invtnum); ?>, "#getplantinfo");}, 30000); // every 10 seconds
            </script>
<?php include("styles/".$user_style."/footer.php"); ?>