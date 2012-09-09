<?php include("styles/globalheader.php");
for ($i=1;$i<=$NUMINV;$i++) {
	include ('config/config_invt'.$i.'.php');
	$PLANTPOWER[$i] = $PLANT_POWER;
	$PLANT_POWERtot+=$PLANT_POWER;
	$YMAXtot=$YMAX+$YMAXtot;
	if ($PRODXDAYS>$PRODXDAYStot) {
		$PRODXDAYStot= $PRODXDAYS;
	}
	if ($YINTERVAL>$YINTERVALtot) {
		$YINTERVALtot= $YINTERVAL;
	}
}
$YINTERVALtot*=2;
?>
<!-- /// Main Day prod /// -->
<script type="text/javascript">
$(document).ready(function()
{
Highcharts.setOptions({
global: {useUTC: true}
});

var Mychartmain, options = {
chart: {
renderTo: 'container1',
backgroundColor: null,
         events: {
            load: function() {
              setInterval(function() {
               $.getJSON('programs/programmultidayfeed.php', function(data){
               json = eval(data);
    totTime=json[0].totTime;
    totValue=json[0].totValue;
<?php
$j=1;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
echo "
    x$invtnum = json[$j].x;
    y$invtnum = json[$j].y;";
$j++;
}
echo "
    scat_MaxTime = json[$j].MaxTime;
    scat_MaxPow = json[$j].MaxPow;";
$j++;
echo "
    scat_LastTime = json[$j].LastTime;
    scat_LastValue = json[$j].LastValue;";
$j++;
echo "
    ptitle = json[$j].PTITLE;
         });
    Mychartmain.series[0].addPoint([totTime, totValue]);";
$j=1;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) { echo "\n    Mychartmain.series[$j].addPoint([x$invtnum, y$invtnum]);";
$j++;
}
echo "
    Mychartmain.series[$j].data[0].update([scat_MaxTime, scat_MaxPow],false,false,true);
    Mychartmain.series[$j].data[1].update([scat_LastTime, scat_LastValue ],false,false,true);";
?>

    Mychartmain.setTitle({ text: '<?php echo "$lgTODAYTITLE " ?>' + ptitle});
    Mychartmain.redraw();
               }, 10000);
            }
         }
},
credits: {enabled: false},
legend: { enabled: true},
title: {
<?php
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
include('config/config_invt'.$invtnum.'.php');
$dir = 'data/invt'.$invtnum.'/csv';
$output = scandir($dir);
$output = array_filter($output, 'tricsv');
sort($output);
$cnt=count($output);
$lines=file($dir."/".$output[$cnt-1]);
$contalines = count($lines);
$array = preg_split("/;/",$lines[0]);
$array[14] = str_replace(',', '.', $array[14]);
$array2 = preg_split("/;/",$lines[$contalines-1]);
$array2[14] = str_replace(',', '.', $array2[14]);
$KWHD = Formulas::calcKiloWattHourDay($array[14], $array2[14], $CORRECTFACTOR, 1);
$KWHDTOT=$KWHD+$KWHDTOT;
}
$KWHDTOT= round($KWHDTOT,1);
echo "text: '$lgTODAYTITLE ($KWHDTOT kWh)'},
subtitle: {";
$year = substr($output[$cnt-1], 0, 4);
$month = substr($output[$cnt-1], 4, 2);
$day = substr($output[$cnt-1], 6, 2);
$sun_info = date_sun_info(strtotime("$year-$month-$day"), $LATITUDE, $LONGITUDE);
echo "text: '$lgSUNRISE ".date("H:i", $sun_info['sunrise'])." - $lgTRANSIT ".date("H:i", $sun_info['transit'])." - $lgSUNSET ".date("H:i", $sun_info['sunset'])."'},
"; ?>
plotOptions: {
areaspline: {
fillOpacity: 0.5,
   marker: {
   enabled: false,
   symbol: 'circle',
   radius: 2,
   states: { hover: { enabled: true } }
   }
},
scatter: {
  dataLabels: {
  enabled: true,
  color: '#4572A7',
  formatter: function() {
  return this.y + 'W'
  }
  },
   marker: {
   fillColor: '#FFFFFF',
   lineWidth: 2,
   lineColor: '#4572A7',
   states: {hover: {enabled: false}}
   }
}
},
xAxis: {type: 'datetime'},
yAxis: {
title: { text: '<?php echo $lgAVGP ?> (W)'},
max: <?php echo $YMAXtot ?>,
min: 0,
endOnTick: false,
tickInterval: <?php echo $YINTERVALtot ?>,
minorTickInterval: 'auto'
},
tooltip: {
formatter: function() { return '<b>' + this.y + 'W' + '</b><br/>' + Highcharts.dateFormat('%H:%M', this.x)}
},
exporting: {enabled: false},
series: []
};

$.getJSON('programs/programmultiday.php', function(data)
{
options.series = data;
Mychartmain= new Highcharts.Chart(options);
}
);

});
</script>
<!-- /// Yesterday prod /// -->
<script type="text/javascript">
$(document).ready(function()
{
Highcharts.setOptions({
global: {useUTC: true}
});

var Mychart2, options = {
chart: {
renderTo: 'container2',
backgroundColor: null
},
credits: {enabled: false},
legend: {enabled: false},
title: {
<?php
$KWHDTOT=0;
for ($invtnum=1;$invtnum<=$NUMINV;$invtnum++) {
include('config/config_invt'.$invtnum.'.php');
$dir = 'data/invt'.$invtnum.'/csv';
$output = scandir($dir);
$output = array_filter($output, 'tricsv');
sort($output);
$cnt=count($output)-2;
$lines=file($dir."/".$output[$cnt]);
$contalines = count($lines);
$array = preg_split("/;/",$lines[0]);
$array[14] = str_replace(',', '.', $array[14]);
$array2 = preg_split("/;/",$lines[$contalines-1]);
$array2[14] = str_replace(',', '.', $array2[14]);
$KWHD = Formulas::calcKiloWattHourDay($array[14], $array2[14], $CORRECTFACTOR, 1);
$KWHDTOT=$KWHD+$KWHDTOT;
}
$KWHDTOT= round($KWHDTOT,1);
echo "text: '$lgYESTERDAYTITLE ($KWHDTOT kWh)'";
?>
},
plotOptions: {
areaspline: {
  marker: {
    enabled: false,
    symbol: 'circle',
    radius: 2,
    states: {hover: {enabled: true}}
  }
},
scatter: {
  dataLabels: {
  enabled: true,
  color: '#4572A7',
  formatter: function() {
  return this.y + 'W'
  }
  },
   marker: {
   fillColor: '#FFFFFF',
   lineWidth: 2,
   lineColor: '#4572A7',
   states: {hover: {enabled: false}}
   }
}
},
xAxis: {type: 'datetime'},
yAxis: {
<?php echo "max: $YMAXtot,";?>
title: {text: '<?php echo "$lgAVGP";?> (W)'},
endOnTick: false,
minorTickInterval: 'auto',
<?php echo "tickInterval: $YINTERVALtot,";?>
min: 0
},
tooltip: {
formatter: function() {
return '<b>' + this.y + 'W' + '</b><br/>' + Highcharts.dateFormat('%H:%M', this.x)
}
},
exporting: {enabled: false},
series: []
};

$.getJSON('programs/programmultiyesterday.php', function(data)
{
options.series = data;
Mychart2 = new Highcharts.Chart(options);
});

});
</script>
<!-- /// Last days prod /// -->
<script type="text/javascript">
var myPLANT_POWER=new Array();
<?php
for ($i=1;$i<=$NUMINV;$i++) {
echo "myPLANT_POWER[$i] ='$PLANTPOWER[$i]';\n";
}
echo "var PLANT_POWERtot='$PLANT_POWERtot';\n";
?>
$(document).ready(function()
{
Highcharts.setOptions({
global: {useUTC: true},
lang: {
months: ['<?php for($i=1;$i<12;$i++) {echo "$lgMONTH[$i]','";} echo $lgMONTH[12]?>'],
shortMonths: ['<?php for($i=1;$i<12;$i++) {echo "$lgSMONTH[$i]','";} echo $lgSMONTH[12]?>'],
weekdays: ['<?php for($i=1;$i<7;$i++) {echo "$lgWEEKD[$i]','";} echo $lgWEEKD[7]?>']
}
});

var Mychart3, options = {
chart: {
renderTo: 'container3',
backgroundColor: null,
defaultSeriesType: 'column'
},
credits: {enabled: false},
title: {
<?php echo "text: '$lgLASTPRODTITLE $PRODXDAYStot $lgDAYS'";?>
},
subtitle: {text: ''},
xAxis: {
type: 'datetime',
tickmarkPlacement: 'on',
dateTimeLabelFormats: {day: '%e %b'}
},
yAxis: {
title: {text: '<?php echo "$lgENERGY";?> (kWh)'},
stackLabels: {
enabled: true,
formatter: function() { return this.total.toFixed(1)}
},
minorGridLineWidth: 1,
minorTickInterval: 'auto'
},
min: 0,
legend: {enabled: false},
tooltip: {
formatter: function() {
var point = this.point,
s = '<b>'+Highcharts.dateFormat('%a %e %b', this.x) + ': '+ (this.point.stackTotal).toFixed(1) +' kWh</b><br>';
s += '<?php echo "$lgEFFICIENCY";?>: ' + (this.point.stackTotal/(PLANT_POWERtot/1000)).toFixed(2)+ ' kWh/kWp<br>';
s +=  this.series.name+': '+ this.y + ' kWh ('+ (this.y/(myPLANT_POWER[this.series.index]/1000)).toFixed(2)+ ' kWh/kWp)';
return s;
}
},
plotOptions: {
column: {
stacking: 'normal',
dataLabels: { enabled: false }
}
},
exporting: {enabled: false},
series: []
};

$.getJSON('programs/programmultilastdays.php', function(data)
{
options.series = data;
Mychart3 = new Highcharts.Chart(options);
});

});
</script>
<!-- /// Live gauge /// -->
<script type="text/javascript">
  var myGauge = new jGauge();
  myGauge.id = 'jGauge';
  myGauge.showAlerts = false;
  myGauge.autoPrefix = autoPrefix.none;
  myGauge.imagePath = 'js/jgauge/img/123aback.png';
  myGauge.segmentStart = -235
  myGauge.segmentEnd = 55
  myGauge.width = 200;
  myGauge.height = 200;
  myGauge.needle.imagePath = 'js/jgauge/img/123aneedle.png';
  myGauge.needle.xOffset = 0;
  myGauge.needle.yOffset = 0;
  myGauge.needle.limitAction = limitAction.autoRange;
  myGauge.label.yOffset = 65;
  myGauge.label.xOffset= 0;
  myGauge.label.color = '#333';
  myGauge.label.precision = 1;
  myGauge.label.suffix = 'W';
  myGauge.ticks.labelRadius = 70;
  myGauge.ticks.labelColor = '#333';
  myGauge.ticks.start = 0;
  myGauge.ticks.end = <?php echo $PLANT_POWERtot ?>;
  myGauge.ticks.count = 0;
  myGauge.ticks.color = 'rgba(0, 0, 0, 0)';
  myGauge.range.color = 'rgba(0, 0, 0, 0)';

   $(document).ready(function(){
      myGauge.init();
   });
</script>
<script type="text/javascript">
  function updateGauge() {
  $.getJSON('programs/programmultilive.php', function(data){
  json = eval(data);
  myGauge.setValue(json[0].GPTOT);
  document.getElementById('PMAXOTD').innerHTML = json[0].PMAXOTD;
  document.getElementById('PMAXOTDTIME').innerHTML = json[0].PMAXOTDTIME;
  })
  }
updateGauge();
setInterval(updateGauge, 500);
</script>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td width="90%"><b><?php echo $lgPOWERPLANT ?> </b>
			<div id="container1" style="height: 300px"></div></td>
		<td width="200"><div id="jGauge" class="jgauge" align="center"
				valign="MIDDLE"></div> <?php
				function using_ie()
				{
					$u_agent = $_SERVER['HTTP_USER_AGENT'];
					$ub = False;
					echo $ub;
					if(preg_match('/MSIE/i',$u_agent)) {
						$ub = True;
					}
					return $ub;
				}
				if (using_ie()) {
					?>
			<div class="iebox">
				<font size="-2"><br>The gauge won't update with Intenet Explorer.
					Please use a compliant browser, such as <a
					href="http://www.google.com/chrome">Chrome</a> or <a
					href="http://www.firefox.com">Firefox</a> </font>.
			</div> <?php
				}
				?>
			<p align="center">
				<font size="-2"><?php echo "$lgPMAX";?><br> <b id='PMAXOTD'>--</b> W
					@ <b id='PMAXOTDTIME'>--</b> </font>
			</p>
		</td>
	</tr>
</table>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td width="50%"><div id="container2" style="height: 300px"></div></td>
		<td width="50%"><div id="container3" style="height: 300px"></div></td>
	</tr>
</table>
<?php include("styles/".$user_style."/footer.php"); ?>