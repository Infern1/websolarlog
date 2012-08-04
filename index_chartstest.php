<?php
require_once("classes/classloader.php");

include("styles/globalheader.php");

if(!empty($_GET['invtnum'])) {
    $invtnum = $_GET['invtnum'];
} else {$invtnum = 1;
}
include("config/config_invt".$invtnum.".php");
?>

<?php
if ($NUMINV>1) {
    echo "<b>$lgINVT $invtnum -</b>";
}
if (!empty($INVNAME)) {
    echo "<b>$INVNAME</b>";
}
?>
<div id="graphToday"></div>
<div id="gaugeWatt"></div>
<script type="text/javascript">
var $PLANT_POWER = <?php echo($PLANT_POWER); ?>;

$(document).ready(function(){
    var invtnum = <?php echo($invtnum); ?>;
	//WSL.createGraphToday('graphToday', invtnum);
        var dataToday = [];
        var graphTodayOptions = {
                series:[{showMarker:false}],
                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer
                },
                axes:{
                  xaxis:{
                    label:'',
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                    renderer: $.jqplot.DateAxisRenderer,
                    tickInterval: '1800', // 30 minutes
                    tickOptions : {
                        angle: -30,
                        formatString: '%H:%M'
                        }
                  },
                  yaxis:{
                    label:'Avg. Power(W)',
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                  }
                }
        };

        $.ajax({
            url: "server.php?method=getTodayValues&invtnum=" + invtnum,
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                for (line in result.data) {
				  var object = result.data[line];
				    //alert(object[0]);
				    dataToday.push([object[0], object[1]]);
				}
                graphTodayOptions.axes.xaxis.min = result.data[0][0];
                $.jqplot('graphToday', [dataToday], graphTodayOptions);
            }
        });

        setInterval(function(){
            $.ajax({
                url: "server.php?method=getLiveData&invtnum=" + invtnum,
                method: 'GET',
                dataType: 'json',
                success: function(result) {
                    dataToday.push([result.liveData.valueSDTE, result.liveData.valueGP]);
                    $.jqplot('graphToday', [dataToday], graphTodayOptions).replot();
                }
            });
        }, 120000); // 2 min refresh
});
</script>
<script type="text/javascript">
var PLANT_POWER = <?php echo($PLANT_POWER); ?>;
var part1 = PLANT_POWER / 4;
var part2 = PLANT_POWER / 4 * 2;
var part3 = PLANT_POWER / 4 * 3 ;
var part4 = PLANT_POWER;

var gaugeToday;
$(document).ready(function(){
    var invtnum = <?php echo($invtnum); ?>;
	//WSL.createGraphToday('graphToday', invtnum);
        var gaugeOptions = {
                    title: 'Current Power',
                    seriesDefaults: {
                        renderer: $.jqplot.MeterGaugeRenderer,
                        rendererOptions: {
                            label: 'W',
                            min: 0,
                            max: PLANT_POWER,
                            //showTickLabels: true,
                            intervals:[0, part1, part2, part3, part4],
                            intervalColors:['#fff','#c66', '#E7E658', '#93b75f', '#6c6']
                        }
                    }
        };

        var gaugeToday = $.jqplot('gaugeWatt',[[0.1]], gaugeOptions);
        setInterval(function(){
            $.ajax({
                url: "server.php?method=getLiveData&invtnum=" + invtnum,
                method: 'GET',
                dataType: 'json',
                success: function(result) {
                    gaugeToday.series[0].data = [['W', result.liveData.valueGP]];
                    gaugeToday.series[0].label = Math.round(result.liveData.valueGP) + ' W';
                    gaugeToday.replot();
                }
            });
        }, 2500); // 2.5 secs refresh
});
</script>
<a href="http://www.jqplot.com/deploy/dist/examples/meterGauge.html" target="_blank">test</a>
<?php include("styles/".$user_style."/footer.php"); ?>