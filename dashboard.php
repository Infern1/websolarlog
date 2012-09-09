<?php 
include("styles/globalheader.php");
$invtnum= $_GET['invtnum'];
?>
<script type="text/javascript">
  var myGauge1 = new jGauge();
  myGauge1.id = 'jGauge1'; 
  myGauge1.showAlerts = false;
  myGauge1.autoPrefix = autoPrefix.none; 
  myGauge1.imagePath = 'js/jgauge/img/123aback.png';
  myGauge1.segmentStart = -235
  myGauge1.segmentEnd = 80
  myGauge1.width = 200;
  myGauge1.height = 200;
  myGauge1.needle.imagePath = 'js/jgauge/img/123aneedle.png';
  myGauge1.needle.xOffset = 0;
  myGauge1.needle.yOffset = 0;
  myGauge1.needle.limitAction = limitAction.autoRange;
  myGauge1.label.yOffset = 65;
  myGauge1.label.xOffset= 0;
  myGauge1.label.color = '#333';
  myGauge1.label.precision = 1; 
  myGauge1.label.suffix = 'W'; 
  myGauge1.ticks.labelRadius = 70;
  myGauge1.ticks.labelColor = '#333';
  myGauge1.ticks.start = 0;
  myGauge1.ticks.end = 2000;
  myGauge1.ticks.count = 0;
  myGauge1.ticks.color = 'rgba(0, 0, 0, 0)';
  myGauge1.range.color = 'rgba(0, 0, 0, 0)';
  
   $(document).ready(function(){
      myGauge1.init(); 
   });
</script>

<script type="text/javascript">
  var myGauge2 = new jGauge();
  myGauge2.id = 'jGauge2'; 
  myGauge2.showAlerts = false;
  myGauge2.autoPrefix = autoPrefix.none; 
  myGauge2.imagePath = 'js/jgauge/img/123aback.png';
  myGauge2.segmentStart = -235
  myGauge2.segmentEnd = 80
  myGauge2.width = 200;
  myGauge2.height = 200;
  myGauge2.needle.imagePath = 'js/jgauge/img/123aneedle.png';
  myGauge2.needle.xOffset = 0;
  myGauge2.needle.yOffset = 0;
  myGauge2.needle.limitAction = limitAction.autoRange;
  myGauge2.label.yOffset = 65;
  myGauge2.label.xOffset= 0;
  myGauge2.label.color = '#333';
  myGauge2.label.precision = 1; 
  myGauge2.label.suffix = 'W'; 
  myGauge2.ticks.labelRadius = 70;
  myGauge2.ticks.labelColor = '#333';
  myGauge2.ticks.start = 0;
  myGauge2.ticks.end = 2000;
  myGauge2.ticks.count = 0;
  myGauge2.ticks.color = 'rgba(0, 0, 0, 0)';
  myGauge2.range.color = 'rgba(0, 0, 0, 0)';
  
   $(document).ready(function(){
      myGauge2.init(); 
   });
</script>
<script type="text/javascript">
  function updateit() {
  var invtnum = <?php echo $invtnum; ?>;
  $.getJSON("programs/programlive.php", { invtnum: invtnum }, function(data){
  json = eval(data);
  myGauge1.setValue(json[0].I1P);
  myGauge2.setValue(json[0].I2P);

  document.getElementById("GV").innerHTML = json[0].GV;
  document.getElementById("GA").innerHTML = json[0].GA;
  document.getElementById("FRQ").innerHTML = json[0].FRQ;
  document.getElementById("EFF").innerHTML = json[0].EFF;
  document.getElementById("BOOT").innerHTML = json[0].BOOT;
  document.getElementById("INVT").innerHTML = json[0].INVT;
  })
  }
updateit();
setInterval(updateit, 500);
</script>

<script type="text/javascript">

Highcharts.setOptions({
   global: {
      useUTC: true
   }
});
   
var Mychart1;
$(document).ready(function() {
   Mychart1 = new Highcharts.Chart({
chart: {
renderTo: 'container1',
defaultSeriesType: 'spline',
backgroundColor: null,
         events: {
            load: function() {
      setInterval(function() {  
      var invtnum = <?php echo $invtnum; ?>;
      $.getJSON("programs/programlive.php", { invtnum: invtnum }, function(data){
      json = eval(data);
      x = json[0].SDTE;
      y = json[0].I1V;
      z = json[0].I1A;  
      shift = Mychart1.series[0].data.length > 20; 
      Mychart1.series[0].addPoint([x, y], true, shift);
      Mychart1.series[1].addPoint([x, z], true, shift);
      Mychart1.redraw();
               });
            }, 3000);
            }
         }
},
credits: {
enabled: false
},
title: {
  text: ''
},

xAxis: {
type: 'datetime',
tickPixelInterval: 150
},
yAxis: [{
min: 0,
labels: { formatter: function() { return this.value +'V';}},
title: { text: '<?php echo "$lgVOLTAGE";?>'}
}, {
min: 0,
labels: { formatter: function() { return this.value +'A';}},
title: { text: '<?php echo "$lgAMPERAGE";?>'}
}
],
tooltip: {
formatter: function() {
  var unit = {
  '<?php echo "$lgDVOLTAGE1";?>': 'V',
  '<?php echo "$lgDCURRENT1";?>': 'A'
  }[this.series.name];
return '<b>' + this.y + unit + '</b><br/>' + Highcharts.dateFormat('%H:%M:%S', this.x)
}
},
legend: {
layout: 'horizontal',
align: 'center',
floating: false,
backgroundColor: '#FFFFFF'
},
exporting: {
enabled: false
},
plotOptions: {
       spline: {
         lineWidth: 4,
         states: {
           hover: {
             lineWidth: 5
           }
         },
         marker: {
           enabled: true,
           states: {
             hover: {
               enabled: true,
               symbol: 'circle',
               radius: 5,
               lineWidth: 1
             }
           }   
         }
       }
     },
series: [{
       name: '<?php echo "$lgDVOLTAGE1";?>',
       yAxis: 0,
       data: []
}, {
       name: '<?php echo "$lgDCURRENT1";?>',
       yAxis: 1,
       data: []
}]
});
});
</script>
<script type="text/javascript">

Highcharts.setOptions({
   global: {
      useUTC: true
   }
});
   
var Mychart2;
$(document).ready(function() {
   Mychart2 = new Highcharts.Chart({
chart: {
renderTo: 'container2',
defaultSeriesType: 'spline',
backgroundColor: null,
         events: {
            load: function() {
      setInterval(function() {  
      var invtnum = <?php echo $invtnum; ?>;
      $.getJSON("programs/programlive.php", { invtnum: invtnum }, function(data){
      json = eval(data);
      x = json[0].SDTE;
      y = json[0].I2V;
      z = json[0].I2A;  
      shift = Mychart2.series[0].data.length > 20; 
      Mychart2.series[0].addPoint([x, y], true, shift);
      Mychart2.series[1].addPoint([x, z], true, shift);
      Mychart2.redraw();
               });
            }, 3000);
            }
         }
},
credits: {
enabled: false
},
title: {
  text: ''
},

xAxis: {
type: 'datetime',
tickPixelInterval: 150
},
yAxis: [{
min: 0,
labels: { formatter: function() { return this.value +'V';}},
title: { text: '<?php echo "$lgVOLTAGE";?>'}
}, {
min: 0,
labels: { formatter: function() { return this.value +'A';}},
title: { text: '<?php echo "$lgAMPERAGE";?>'}
}
],
tooltip: {
formatter: function() {
  var unit = {
  '<?php echo "$lgDVOLTAGE2";?>': 'V',
  '<?php echo "$lgDCURRENT2";?>': 'A'
  }[this.series.name];
return '<b>' + this.y + unit + '</b><br/>' + Highcharts.dateFormat('%H:%M:%S', this.x)
}
},
legend: {
layout: 'horizontal',
align: 'center',
floating: false,
backgroundColor: '#FFFFFF'
},
exporting: {
enabled: false
},
plotOptions: {
       spline: {
         lineWidth: 4,
         states: {
           hover: {
             lineWidth: 5
           }
         },
         marker: {
           enabled: true,
           states: {
             hover: {
               enabled: true,
               symbol: 'circle',
               radius: 5,
               lineWidth: 1
             }
           }   
         }
       }
     },
series: [{
       name: '<?php echo "$lgDVOLTAGE2";?>',
       yAxis: 0,
       data: []
}, {
       name: '<?php echo "$lgDCURRENT2";?>',
       yAxis: 1,
       data: []
}]
});
});
</script>
<?php 
if($NUMINV>1) {
	echo "<b>$lgDASHBOARD $lgINVT $invtnum</b>";
}
?>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td><?php echo "$lgINPUT1 :";?>
			<div id="jGauge1" align="center" class="jgauge" valign="MIDDLE"></div>
		</td>
		<td width="90%"><div id="container1" style="height: 300px"></div></td>
	</tr>
</table>
<hr>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td><?php echo "$lgINPUT2 :";?>
			<div id="jGauge2" class="jgauge" valign="MIDDLE"></div></td>
		<td width="90%"><div id="container2" style="height: 300px"></div></td>
	</tr>
</table>
<hr>
<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td><?php echo "$lgGRIDVOLTAGE : ";?><b id='GV'>--</b> V</td>
		<td><?php echo "$lgGRIDCURRENT : ";?><b id='GA'>--</b> A</td>
		<td><?php echo "$lgFREQ : ";?><b id='FRQ'>--</b>Hz</td>
	</tr>
	<tr>
		<td><?php echo "$lgEFFICIENCYINVT : ";?><b id='EFF'>--</b> %</td>
		<td><?php echo "$lgBOOSTERTEMP : ";?><b id='BOOT'>--</b>°c</td>
		<td><?php echo "$lgINVERTERTEMP : ";?><b id='INVT'>--</b>°c</td>
	</tr>
</table>
<?php include("styles/".$user_style."/footer.php"); ?>
