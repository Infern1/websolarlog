<?php
require_once("classes/classloader.php");

include("styles/globalheader.php");

if(!empty($_GET['invtnum'])) {
    $invtnum = $_GET['invtnum'];
} else {$invtnum = 1;
}
include("config/config_invt".$invtnum.".php");
?>

<div id="top">
    <div id="topGauges">
        <div id="gaugeWatt" class="topGauge"></div>
        <div id="gaugeVolt" class="topGauge"></div>
        <div id="gaugeAmp" class="topGauge"></div>
    </div>
    <div id="topInfo">More info</div>
</div>
<div id="today">
    <div id="titleToday"></div>
    <div id="graphToday"></div>
</div>
<script type="text/javascript">
var $PLANT_POWER = <?php echo($PLANT_POWER); ?>;

$(document).ready(function(){
    var invtnum = <?php echo($invtnum); ?>;
	//WSL.createGraphToday('graphToday', invtnum);

	//$.jqplot.config.enablePlugins = true;
        var dataToday = [];
        var graphTodayOptions = {
                series:[{showMarker:false, fill: true}],
                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer
                },
                axes:{
                  xaxis:{
                    label:'',
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                    renderer: $.jqplot.DateAxisRenderer,
                    tickInterval: '3600', // 1 hour
                    tickOptions : {
                        angle: -30,
                        formatString: '%H:%M'
                        }
                  },
                  yaxis:{
                    label:'Avg. Power(W)',
                    min: 0,
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                  }
                }
        };



        var graphToday = null;
        function updateGraphToday() {
            $.ajax({
                url: "server.php?method=getTodayValues&invtnum=" + invtnum,
                method: 'GET',
                dataType: 'json',
                success: function(result) {
                    for (line in result.data) {
    				  var object = result.data[line];
    				  dataToday.push([object[0], object[1]]);
    				}
                    graphTodayOptions.axes.xaxis.min = result.data[0][0];
                    if (graphToday != null) {
                        graphToday.destroy();
                    }
                    graphToday = $.jqplot('graphToday', [dataToday], graphTodayOptions);

                    mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy today: ' + result.kwht + ' kWh</div>').insertAfter('#graphToday .jqplot-grid-canvas');
                    //$('#titleToday').html();
                }
            });
        }

        updateGraphToday(); // init
        setInterval(updateGraphToday, 120000); // 2 min refresh = 120000
});
</script>
<script type="text/javascript">
var PLANT_POWER = <?php echo($PLANT_POWER); ?>;
var partw = PLANT_POWER / 10;
var partv = 500 / 10;
var parta = 16 / 10;

var gaugeToday;
$(document).ready(function(){
    var invtnum = <?php echo($invtnum); ?>;
    var gaugeWattOptions = {
        title: 'Power', grid: { background: '#D3DAE2' },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: 'W', min: 0, max: PLANT_POWER, padding: 0,
                intervals:[partw, partw * 2, partw * 3, partw *4, partw * 5, partw * 6, partw * 7, partw * 8, partw * 9, partw * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };

    var gaugeVoltOptions = {
        title: 'Volt', grid: { background: '#D3DAE2' },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: 'V', min: 0, max: 500, padding: 0,
                intervals:[partv, partv * 2, partv * 3, partv *4, partv * 5, partv * 6, partv * 7, partv * 8, partv * 9, partv * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };

    var gaugeAmpOptions = {
        title: 'Amp', grid: { background: '#D3DAE2' },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: 'A', min: 0, max: 16, padding: 0,
                intervals:[parta, parta * 2, parta * 3, parta *4, parta * 5, parta * 6, parta * 7, parta * 8, parta * 9, parta * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };

    // Init gauges
    var gaugeWatt = $.jqplot('gaugeWatt',[[0.1]], gaugeWattOptions);
    var gaugeVolt = $.jqplot('gaugeVolt',[[0.1]], gaugeVoltOptions);
    var gaugeAmp = $.jqplot('gaugeAmp',[[0.1]], gaugeAmpOptions);


    setInterval(function(){
        $.ajax({
            url: "server.php?method=getLiveData&invtnum=" + invtnum,
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                gaugeWatt.series[0].data = [['W', result.liveData.valueGP]];
                gaugeWatt.series[0].label = Math.round(result.liveData.valueGP) + ' W';
                gaugeWatt.replot();

                gaugeVolt.series[0].data = [['V', result.liveData.valueGV]];
                gaugeVolt.series[0].label = Math.round(result.liveData.valueGV) + ' V';
                gaugeVolt.replot();

                gaugeAmp.series[0].data = [['A', result.liveData.valueGA]];
                gaugeAmp.series[0].label = Math.round(result.liveData.valueGA * 10) / 10 + ' A';
                gaugeAmp.replot();


            }
        });
    }, 2500); // 2.5 secs refresh
});
</script>
<?php include("styles/".$user_style."/footer.php"); ?>