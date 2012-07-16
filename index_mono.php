<?php
include("styles/globalheader.php");
if(!empty($_GET['invtnum'])) {
    $invtnum = $_GET['invtnum'];
} else {$invtnum = 1;
}
include("config/config_invt".$invtnum.".php");
?>

<!-- /// Day prod /// -->
<script type="text/javascript">
$(document).ready(function()
{
Highcharts.setOptions({
global: {useUTC: true}
});

var Mychart1, options = {
chart: {
renderTo: 'container1',
backgroundColor: null,
         events: {
            load: function() {
            var series = this.series[0];
            var invtnum = <?php echo $invtnum; ?>;
              setInterval(function() {
               $.getJSON("programs/programdayfeed.php", { invtnum: invtnum }, function(data){
               json = eval(data);
                 x = json[0].LastTime;
                 y = json[0].LastValue;
                 a = json[1].MaxTime;
                 b = json[1].MaxPow;
                 ptitle = json[2].PTITLE;
               });
    series.addPoint([x, y]);
    Mychart1.series[1].data[0].update([a, b],false,false,true);
    Mychart1.series[1].data[1].update([x, y],false,false,true);
    Mychart1.setTitle({ text: '<?php echo "$lgTODAYTITLE ";?>' + ptitle});
    Mychart1.redraw();
               }, 10000);
            }
         }
},
credits: {enabled: false},
legend: {enabled: false},
title: {
<?php
$dir = 'data/invt'.$invtnum.'/csv';

$output = scandir($dir);
$output = array_filter($output, "tricsv");
sort($output);
$cnt=count($output);
$lines=file($dir."/".$output[$cnt-1]);
$contalines = count($lines);
$array = preg_split("/;/",$lines[0]);
$array[14] = str_replace(",", ".", $array[14]);
$array2 = preg_split("/;/",$lines[$contalines-1]);
$array2[14] = str_replace(",", ".", $array2[14]);
$KWHD=round((($array2[14]-$array[14])*$CORRECTFACTOR),1);
echo "text: '$lgTODAYTITLE ($KWHD kWh)'";
?>
},
subtitle: {
<?php
$year = substr($output[$cnt-1], 0, 4);
$month = substr($output[$cnt-1], 4, 2);
$day = substr($output[$cnt-1], 6, 2);
$sun_info = date_sun_info(strtotime("$year-$month-$day"), $LATITUDE, $LONGITUDE);
echo "text: '$lgSUNRISE ".date("H:i", $sun_info['sunrise'])." - $lgTRANSIT ".date("H:i", $sun_info['transit'])." - $lgSUNSET ".date("H:i", $sun_info['sunset'])."'";
?>
},
plotOptions: {
    areaspline: {
       marker: {
           enabled: false,
           symbol: 'circle',
           radius: 2,
           states: { hover: {enabled: true} }
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
<?php echo "max: $YMAX,";?>
title: {text: '<?php echo "$lgAVGP";?> (W)'},
endOnTick: false,
minorTickInterval: 'auto',
<?php echo "tickInterval: $YINTERVAL,";?>
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

var invtnum = <?php echo $invtnum; ?>;
$.getJSON('programs/programday.php', { invtnum: invtnum }, function(data)
{
options.series = data;
Mychart1= new Highcharts.Chart(options);
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
$lines=file($dir."/".$output[$cnt-2]);
$contalines = count($lines);
$array = preg_split("/;/",$lines[0]);
$array[14] = str_replace(",", ".", $array[14]);
$array2 = preg_split("/;/",$lines[$contalines-1]);
$array2[14] = str_replace(",", ".", $array2[14]);
$KWHD=round((($array2[14]-$array[14])*$CORRECTFACTOR),1);
echo "text: '$lgYESTERDAYTITLE ($KWHD kWh)'";
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
   states: {hover: {enabled: false}
   }
}
}
},
xAxis: {type: 'datetime'},
yAxis: {
<?php echo "max: $YMAX,";?>
title: {text: '<?php echo "$lgAVGP";?> (W)'},
endOnTick: false,
minorTickInterval: 'auto',
<?php echo "tickInterval: $YINTERVAL,";?>
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

var invtnum = <?php echo $invtnum; ?>;
$.getJSON('programs/programyesterday.php', { invtnum: invtnum }, function(data)
{
options.series = data;
Mychart2 = new Highcharts.Chart(options);
});

});
</script>
<!-- /// Last days prod /// -->
<script type="text/javascript">
var PLANT_POWER='<?php echo $PLANT_POWER;?>';
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
<?php echo "text: '$lgLASTPRODTITLE $PRODXDAYS $lgDAYS'";?>
},
subtitle: {text: '<?php echo $lgLASTPRODSUBTITLE;?>'},
xAxis: {
type: 'datetime',
tickmarkPlacement: 'on',
dateTimeLabelFormats: {day: '%e %b'}
},
yAxis: {
title: {text: '<?php echo "$lgENERGY";?> (kWh)'},
minorGridLineWidth: 1,
minorTickInterval: 'auto'
},
min: 0,
legend: {enabled: false},
tooltip: {
formatter: function() {
var point = this.point,
s = '<b>'+Highcharts.dateFormat('%a %e %b', this.x) + ': '+ this.y +' kWh</b><br>';
s += '<?php echo "$lgEFFICIENCY";?>: ' + (this.y/(PLANT_POWER/1000)).toFixed(2)+ ' kWh/kWp';
return s;
}
},
plotOptions: {
  series: {
    minPointLength: 3,
    point:{
      events: {
        click: function(event) {
          window.location = 'detailed.php?invtnum='+invtnum+'&date2='+this.x;
        }
      }
    }
  },
column: {dataLabels: {enabled: true}}
},
exporting: {enabled: false},
series: []
};

var invtnum = <?php echo $invtnum; ?>;
$.getJSON('programs/programlastdays.php', { invtnum: invtnum }, function(data)
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
  myGauge.ticks.end = <?php echo "$PLANT_POWER";?>;
  myGauge.ticks.count = 0;
  myGauge.ticks.color = 'rgba(0, 0, 0, 0)';
  myGauge.range.color = 'rgba(0, 0, 0, 0)';

   $(document).ready(function(){
      myGauge.init();
   });
</script>
<script type="text/javascript">
  function updateGauge() {
  var invtnum = <?php echo $invtnum; ?>;
  $.getJSON('programs/programlive.php', { invtnum: invtnum }, function(data){
  json = eval(data);
  myGauge.setValue(json[0].GP);
  document.getElementById('PMAXOTD').innerHTML = json[0].PMAXOTD;
  document.getElementById('PMAXOTDTIME').innerHTML = json[0].PMAXOTDTIME;
  })
  }
updateGauge();
setInterval(updateGauge, 500);
</script>
<?php
if ($NUMINV>1) {
    echo "<b>$lgINVT $invtnum -</b>";
}
if (!empty($INVNAME)) {
    echo "<b> $INVNAME</b>";
}
?>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td width="90%"><div id="container1" style="height: 300px"></div></td>
		<td width="200"><div id="jGauge" class="jgauge" align="center" valign="MIDDLE"></div> <?php
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
				<font size="-2"><br>The gauge won't update with Intenet Explorer. Please use a compliant browser, such as <a href="http://www.google.com/chrome">Chrome</a> or <a href="http://www.firefox.com">Firefox</a>
				</font>.
			</div> <?php
		}
		?>
			<p align="center">
				<font size="-2"><?php echo "$lgPMAX";?><br> <b id='PMAXOTD'>--</b> W @ <b id='PMAXOTDTIME'>--</b> <br> <?php
				echo "<a href='dashboard.php?invtnum=$invtnum'>$lgDASHBOARD</a>";
				?> </font>
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