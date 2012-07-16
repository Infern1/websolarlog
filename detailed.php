<?php 
include("styles/globalheader.php");

$date1 = $_POST['date1'];
$date2 = $_GET['date2'];
$invtnum= $_GET['invtnum'];

if (!empty ($date2)) {
    $ts = strftime("%s", floor($date2/1000));
    $date1=date('d/m/Y', $ts);
}

if (!empty ($_POST['invtnum'])) {
    $invtnum= $_POST['invtnum'];
}
if (!empty ($_POST['checkpower'])) {
    $checkpower = $_POST['checkpower'];
} else { $checkpower=false;
}
if (!empty ($_POST['checkavgpower'])) {
    $checkavgpower = $_POST['checkavgpower'];
} else { $checkavgpower=false;
}
if (!empty ($_POST['checkI1V'])) {
    $checkI1V = $_POST['checkI1V'];
} else { $checkI1V=false;
}
if (!empty ($_POST['checkI1A'])) {
    $checkI1A = $_POST['checkI1A'];
} else { $checkI1A=false;
}
if (!empty ($_POST['checkI1P'])) {
    $checkI1P = $_POST['checkI1P'];
} else { $checkI1P=false;
}
if (!empty ($_POST['checkI2V'])) {
    $checkI2V = $_POST['checkI2V'];
} else { $checkI2V=false;
}
if (!empty ($_POST['checkI2A'])) {
    $checkI2A = $_POST['checkI2A'];
} else { $checkI2A=false;
}
if (!empty ($_POST['checkI2P'])) {
    $checkI2P = $_POST['checkI2P'];
} else { $checkI2P=false;
}
if (!empty ($_POST['checkGV'])) {
    $checkGV = $_POST['checkGV'];
} else { $checkGV=false;
}
if (!empty ($_POST['checkGA'])) {
    $checkGA = $_POST['checkGA'];
} else { $checkGA=false;
}
if (!empty ($_POST['checkGP'])) {
    $checkGP = $_POST['checkGP'];
} else { $checkGP=false;
}
if (!empty ($_POST['checkFRQ'])) {
    $checkFRQ = $_POST['checkFRQ'];
} else { $checkFRQ=false;
}
if (!empty ($_POST['checkEFF'])) {
    $checkEFF = $_POST['checkEFF'];
} else { $checkEFF=false;
}
if (!empty ($_POST['checkINVT'])) {
    $checkINVT = $_POST['checkINVT'];
} else { $checkINVT=false;
}
if (!empty ($_POST['checkBOOT'])) {
    $checkBOOT = $_POST['checkBOOT'];
} else { $checkBOOT=false;
}

$config_invt="config/config_invt".$invtnum.".php";
include("$config_invt");

if (ereg ("([0-9]{2})/([0-9]{2})/([0-9]{4})", $date1)) { // test date1
    //Nothing selected
    if ($checkpower==false&&$checkavgpower==false&&$checkI1V==false&&$checkI1A==false&&$checkI1P==false&&$checkI2V==false&&$checkI2A==false&&$checkI2P==false&&$checkGV==false&&$checkGA==false&&$checkGP==false&&$checkFRQ==false&&$checkEFF==false&&$checkINVT==false&&$checkBOOT==false) {
        $checkavgpower=true;
    }
    $titledate = substr($date1,0,10) ;
    $date1 =(substr($date1,6,4)).(substr($date1,3,2)).(substr($date1,0,2)).".csv";
    ?>
<script type="text/javascript">

$(document).ready(function()
{
Highcharts.setOptions({
global: {
useUTC: true
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
<?php
$log="data/invt".$invtnum."/csv/".$date1;
if (file_exists($log)) {
$lines=file($log);
$contalines = count($lines);
$array = preg_split("/;/",$lines[0]);
$array2 = preg_split("/;/",$lines[$contalines-1]);
$array[14] = str_replace(",", ".", $array[14]); 
$array2[14] = str_replace(",", ".", $array2[14]); 
$KWHD=round((($array2[14]-$array[14])*$CORRECTFACTOR),2);
if ($invtnum==0 || $NUMINV==1) {
  $parttitle="";
} else {
  $parttitle="$lgINVT$invtnum - ";
}
echo "text: '$parttitle $lgDETAILEDOFTITLE $titledate ($KWHD kWh)'";
} else {
echo "text: 'No data $titledate (-- kWh)'";
}
echo "
},
subtitle: { text: '$lgDETAILSUBTITLE' },
xAxis: {
type: 'datetime',
maxZoom: 300000,
dateTimeLabelFormats: {minute: '%H:%M'}
},
yAxis: [";
if ($checkpower==true || $checkavgpower==true || $checkI1P==true || $checkI2P==true || $checkGP==true) {
if (!empty ($date2)) {
  echo "{ max: $YMAX,";
} else {
  echo "{";
}
echo"
min: 0,
maxZoom: 100,
labels: { formatter: function() { return this.value +'W';}},
title: { text: '$lgPOWER'}
},";
}
if ($checkI1V==true || $checkI2V==true || $checkGV==true) {
echo "{
maxZoom: 10,
labels: { formatter: function() { return this.value +'V';}},
title: { text: '$lgVOLTAGE'}
},";
}
if ($checkI1A==true || $checkI2A==true || $checkGA==true) {
echo"{
min: 0,
maxZoom: 1,
labels: { formatter: function() { return this.value +'A';}},
title: { text: '$lgAMPERAGE'}
},";
}
if ($checkFRQ==true) {
echo"{
min: 40,
maxZoom: 5,
labels: { formatter: function() { return this.value +'Hz';}},
title: { text: '$lgFREQ'},
opposite: true
},";
}
if ($checkEFF==true) {
echo"{
min: 0,
max:110,
maxZoom: 5,
labels: { formatter: function() { return this.value +'%';}},
title: { text: '$lgEFFICIENCY'},
opposite: true
},";
}
if ($checkINVT==true || $checkBOOT==true) {
echo "{
min: 10,
maxZoom: 2,
labels: { formatter: function() { return this.value +'c';}},
title: { text: '$lgTEMP'},
opposite: true
},";
}
echo"
],
tooltip: {
formatter: function() {
  var unit = {
  '$lgDPOWERINSTANT' : 'W',
  '$lgDPOWERAVG': 'W',
  '$lgDCURRENT1': 'A',
  '$lgDVOLTAGE1': 'V',
  '$lgDPOWER1': 'W',
  '$lgDCURRENT2': 'A',
  '$lgDVOLTAGE2': 'V',
  '$lgDPOWER2': 'W',
  '$lgDGRIDCURRENT': 'A',
  '$lgDGRIDVOLTAGE': 'V',
  'Grid Power': 'W',
  '$lgDFREQ': 'Hz',
  '$lgDEFFICIENCY': '%',
  '$lgDINVERTERTEMP': 'c',
  '$lgDBOOSTERTEMP': 'c'
  }[this.series.name];
return '<b>' + this.y + unit + '</b><br/>' + Highcharts.dateFormat('%H:%M', this.x)
}
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
"; // End of echo

// transmit the value to proceed them via _GET
$destination="programs/programdetailed.php?date1=$date1&checkpower=$checkpower&checkavgpower=$checkavgpower&checkI1V=$checkI1V&checkI1A=$checkI1A&checkI1P=$checkI1P&checkI2V=$checkI2V&checkI2A=$checkI2A&checkI2P=$checkI2P&checkGV=$checkGV&checkGA=$checkGA&checkGP=$checkGP&checkFRQ=$checkFRQ&checkEFF=$checkEFF&checkINVT=$checkINVT&checkBOOT=$checkBOOT";

echo "
var invtnum = $invtnum
$.getJSON('$destination', { invtnum: invtnum }, function(data)
{
options.series = data;
Mychart = new Highcharts.Chart(options);
});

});
</script>
"; //End of echo
} // End of date valid
?>
<div align="center">
<div id="container" style="width: 100%; height: 550px"></div>
<?php
$year = substr($date1, 0, 4);
$month = substr($date1, 4, 2);
$day = substr($date1, 6, 2);
$sun_info = date_sun_info(strtotime("$year-$month-$day"), $LATITUDE, $LONGITUDE);
echo "<font size='-1'>  $lgSUNRISE ".date("H:i", $sun_info[sunrise])." - $lgTRANSIT ".date("H:i", $sun_info[transit])." - $lgSUNSET ".date("H:i", $sun_info[sunset])."</font>";
?>
<br>&nbsp;
<FORM><INPUT type="button" value="<?php echo "$lgBACK";?>" OnClick="window.location.href='indexdetailed.php'"></FORM>
</div>

<?php include("styles/default/footer.php"); ?>
