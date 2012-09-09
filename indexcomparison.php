<?php
include("styles/globalheader.php");
if(!empty($_POST['invtnum'])) {
	$invtnum=$_POST['invtnum'];
} else {if ($NUMINV>1) {
	$invtnum=0;
} else {$invtnum=1;
}
}
if ($invtnum==0) {
	$dir = 'data/invt1/production';
} else {
	$dir = 'data/invt'.$invtnum.'/production';
}
$output = scandir($dir);
$output = array_filter($output, "tricsv");
sort($output);
$xyears=count($output);

if($invtnum==0) {
	$startinv=1; $uptoinv=$NUMINV;
} else { $startinv=$invtnum; $uptoinv=$invtnum;
}
for ($invt_num=$startinv;$invt_num<=$uptoinv;$invt_num++) {  // Multi
	$config_invt="config/config_invt".$invt_num.".php";
	include("$config_invt");
	$PLANT_POWERtot+=$PLANT_POWER;
} // multi

if (!empty ($_POST['whichmonth'])) {
	$whichmonth= $_POST['whichmonth'];
} else { $whichmonth= date("n");
}
if (!empty ($_POST['whichyear'])) {
	$whichyear= $_POST['whichyear'];
} else { $whichyear= date("Y");
}
if (!empty ($_POST['comparemonth'])) {
	$comparemonth= $_POST['comparemonth'];
} else { $comparemonth= date("n");
}
if (!empty ($_POST['compareyear'])) {
	$compareyear= $_POST['compareyear'];
} else { $compareyear= "expected";
}
?>
<table width="95%" border=0 align=center cellpadding="8">
	<tr>
		<td>
			<form method="POST" action="indexcomparison.php">
				<?php
				if ($NUMINV>1) {
					echo "<select name='invtnum' onchange='this.form.submit()'>";
					if ($invtnum=='all') {
						echo "<option SELECTED value=0>$lgALL</option>";
					} else {
						echo "<option value=0>$lgALL</option>";
					}
					for ($i=1;$i<=$NUMINV;$i++) {
						if ($invtnum==$i) {
							echo "<option SELECTED value=$i>";
						} else {
							echo "<option value=$i>";
						}
						echo "$lgINVT$i</option>";
					}
					echo "</select>&nbsp;";
				}
				echo "$lgCHOOSEDATE :
				<select name='whichmonth' onchange='this.form.submit()'>";
				for ($i=1;$i<=12;$i++){
					if ($whichmonth==$i) {
						echo "<option SELECTED value='$i'>";
					} else {
						echo "<option value='$i'>";
					}
					echo "$lgMONTH[$i]</option>";
				}
				echo "
				</select>
				<select name='whichyear' onchange='this.form.submit()'>";
				for ($i=($xyears-1);$i>=0;$i--){
					$option = substr($output[$i],6,4);
					if ($whichyear==$option) {
						echo "<option SELECTED>";
					} else {
						echo "<option>";
					}
					echo "$option</option>";
				}
				echo "</select>
				$lgCOMPAREDWITH
				<select name='comparemonth' onchange='this.form.submit()'>";
				for ($i=1;$i<=12;$i++){
					if ($comparemonth==$i) {
						echo "<option SELECTED value='$i'>";
					} else {
						echo "<option value='$i'>";
					}
					echo "$lgMONTH[$i]</option>";
				}
				echo "
				</select>
				<select name='compareyear' onchange='this.form.submit()'>";
				if ($compareyear=='expected') {
					echo "<option SELECTED value='expected'>$lgPRODTOOLTIPEXPECTED";
					$compareyear2=$lgPRODTOOLTIPEXPECTED;
				} else {
					echo "<option value='expected'>$lgPRODTOOLTIPEXPECTED";
					$compareyear2=$compareyear;
				}
				echo "</option>";

				for ($i=($xyears-1);$i>=0;$i--){
					$option = substr($output[$i],6,4);
					if ($compareyear==$option) {
						echo "<option SELECTED>";
					} else {
						echo "<option>";
					}
					echo "$option</option>";
				}
				echo "
				</select>
				";
				?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
var PLANT_POWER='<?php echo $PLANT_POWERtot;?>';

$(document).ready(function() {
Highcharts.setOptions({
global: {
useUTC: true
},
lang: {
months: ['<?php for($i=1;$i<12;$i++) {echo "$lgMONTH[$i]','";} echo $lgMONTH[12]?>'],
shortMonths: ['<?php for($i=1;$i<12;$i++) {echo "$lgSMONTH[$i]','";} echo $lgSMONTH[12]?>'],
weekdays: ['<?php for($i=1;$i<7;$i++) {echo "$lgWEEKD[$i]','";} echo $lgWEEKD[7]?>']
}
});

var Mychart, options = {
chart: {
renderTo: 'container',
type: 'spline',
backgroundColor: null,
zoomType: 'xy',
resetZoomButton: {
                position: {
                    align: 'right',
                    verticalAlign: 'top'
                }
},
spaceRight:20
},
credits: {
enabled: false
},
title: {
  text: '<?php
  if ($invtnum==0 || $NUMINV==1) {
  $parttitle="";
} else {
  $parttitle="$lgINVT$invtnum - ";
}
  echo "$parttitle $lgCOMPARETITLE $lgMONTH[$whichmonth] $whichyear $lgWITH $lgMONTH[$comparemonth] $compareyear2";
  ?>'
},
subtitle: { text: '<?php echo "$lgCOMPARESUBTITLE"; ?>' },
xAxis: [{
type: 'datetime',
maxZoom: 43200000
  }, {
type: 'datetime',
maxZoom: 43200000
}] ,
yAxis: [{
min: 0,
maxZoom: 200,
labels: { formatter: function() { return this.value + 'kWh';}},
title: { text: '<?php echo "$lgPRODCUM"; ?>'}
},
],
tooltip: {
formatter: function() {
    if ((Mychart.series[0].name== this.series.name)&& (Mychart.series[0].name!=Mychart.series[1].name)){
      var firstSeriesLen = Mychart.series[0].data.length;
      var secondSeriesLen =  Mychart.series[1].data.length;
      var secondSeriesMax = Mychart.series[1].data[secondSeriesLen-1].y;
  var s = '';
  s += '<b>' + Highcharts.dateFormat('%A %e',this.x) + ' ' + this.series.name + ' :</b><br/>' + this.y + 'kWh (' + (this.y/(PLANT_POWER/1000)).toFixed(2)+ 'kWh/kWp)<br/>';
  var daynum = ((this.x-Mychart.series[0].data[0].x)/86400000)+1;
  var perf = (((this.y * 100 *firstSeriesLen)/(secondSeriesMax*daynum))-100).toFixed(1);
  s += '<?php echo $lgGLOBPERF?>: '+perf+ '%';
  return s;
   } else {
      return '<b>' + Highcharts.dateFormat('%A %e',this.x) + ' ' + this.series.name + ' :</b><br/>' + this.y + 'kWh (' + (this.y/(PLANT_POWER/1000)).toFixed(2)+ 'kWh/kWp)<br/>';
   }
},
crosshairs: true
},
legend: {
layout: 'horizontal',
align: 'center',
floating: false,
backgroundColor: '#FFFFFF'
},
exporting: {
filename: '123Aurora-chart',
width: 1200
},
series: []
};


<?php
// transmit the value to proceed them via _GET
$destination="programs/programcomparison.php?whichmonth=$whichmonth&whichyear=$whichyear&comparemonth=$comparemonth&compareyear=$compareyear";

echo "
var invtnum = $invtnum;
$.getJSON('$destination', { invtnum: invtnum }, function(data)
{
options.series = data;
Mychart = new Highcharts.Chart(options);
});
});
"; //End of echo
?>

</script>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td><div id="container" style="width: 95%; height: 450px"></div></td>
	</tr>
</table>
<?php include("styles/".$user_style."/footer.php"); ?>