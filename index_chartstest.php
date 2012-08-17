<?php
require_once("classes/classloader.php");

include("styles/globalheader.php");

if(!empty($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
} else {$invtnum = 1;
}
include("config/config_invt".$invtnum.".php");
?>

<div id="right">
	<div id="topGauges">
		<div class="chartGroup">Power</div>
		<div id="gaugeDCP" class="topGauge"></div>
		<div id="gaugeGP2" class="topGauge"></div>
		<div id="gaugeEff" class="topGauge"></div>
		<!-- div id="topInfo">More info</div -->
		<div class="chartGroup">String1</div>
		<div id="gaugeStr1P" class="topGauge"></div>
		<div id="gaugeStr1V" class="topGauge"></div>
		<div id="gaugeStr1A" class="topGauge"></div>
		<div class="chartGroup">String2</div>
		<div id="gaugeStr2P" class="topGauge"></div>
		<div id="gaugeStr2V" class="topGauge"></div>
		<div id="gaugeStr2A" class="topGauge"></div>
		<div class="chartGroup">Grid</div>
		<div id="gaugeGP" class="topGauge"></div>
		<div id="gaugeGV" class="topGauge"></div>
		<div id="gaugeGA" class="topGauge"></div>
		<div class="chartGroup">Misc</div>
		<div id="gaugeFRQ" class="topGauge"></div>
		<div id="gaugeBOOT" class="topGauge"></div>
		<div id="gaugeINVT" class="topGauge"></div>
	</div>
</div>
<div id="left">1
	<div id="titleToday2"></div>
	<div id="graphToday2"></div>
	2
	<div id="titleToday"></div>
	<div id="graphToday"></div>
	3
	<div id="titleYesterday"></div>
	<div id="graphYesterday"></div>
	4
	<div id="titleLastDays"></div>
	<div id="graphLastDays"></div>


</div>

<script type="text/javascript">
              WSL.createGraphToday(<?php echo($invtnum); ?>, "graphToday2"); // Initial load fast
              //window.setInterval(function(){WSL.createGraphToday(<?php echo($invtnum); ?>, "graphToday2");}, 3000); // every 10 seconds
            </script>

<script type="text/javascript">
var $PLANT_POWER = <?php echo($PLANT_POWER); ?>;

$(document).ready(function(){
    var invtnum = <?php echo($invtnum); ?>;
	//WSL.createGraphToday('graphToday', invtnum);

	//$.jqplot.config.enablePlugins = true;
        var dataToday = [];
        var dataYesterday = [];
        var dataLastDays = [];

        var graphOptions = {
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
                },
                highlighter: {
                    show: true,
                    sizeAdjust: 7.5
                  },
                  cursor: {
                    show: false
                  }
        };


	    var graphLastDaysOptions = {
	        // The "seriesDefaults" option is an options object that will
	        // be applied to all series in the chart.
	        seriesDefaults:{
	            renderer:$.jqplot.BarRenderer,
	            rendererOptions: {fillToZero: true, barWidth:5},

	          showMarker:false,
	          pointLabels: { show:true }

	        },
	        // Custom labels for the series are specified with the "label"
	        // option on the series option.  Here a series option object
	        // is specified for each series.
	                        highlighter: {
                    show: true,
                    sizeAdjust: 7.5
                  },

	        //series:[{label:'Hotel'}],
	        axesDefaults: {
		        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,

		        tickOptions: {
		          angle: -30,
		          fontSize: '10pt'
		        }
		    },
	        // Show the legend and put it outside the grid, but inside the
	        // plot container, shrinking the grid to accomodate the legend.
	        // A value of "outside" would not shrink the grid and allow
	        // the legend to overflow the container.
	        legend: {
	            show: false
	        },
	        axes: {
	            // Use a category axis on the x axis and use our custom ticks.
	            xaxis: {
	            	label:'',
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                    renderer: $.jqplot.DateAxisRenderer,
                    angle: -30,
                    tickOptions : {
                    	formatString: '%d-%m'
                    }
	            },
	            // Pad the y axis just a little so bars can get close to, but
	            // not touch, the grid boundaries.  1.2 is the default padding.
	            yaxis: {
	                label:'Power(W)',
	                min: 0,
	                labelRenderer: $.jqplot.CanvasAxisLabelRenderer
	            }
	        }
	    };

        var graphLastDays = null;
        function updateGraphLastDays() {
            $.ajax({
                url: "server.php?method=getLastDaysValues&invtnum=" + invtnum,
                method: 'GET',
                dataType: 'json',
                success: function(result) {
                    for (line in result.data) {
    				  var object = result.data[line];
    				  dataLastDays.push([object[0], object[1]]);
    				}
                    //graphLastDaysOptions.axes.xaxis.min = result.data[0][0];
                    if (graphLastDays != null) {
                    	graphLastDays.destroy();
                    }
                    graphLastDays = $.jqplot('graphLastDays', [dataLastDays], graphLastDaysOptions);

                    mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Power per day</div>').insertAfter('#graphLastDays .jqplot-grid-canvas');
                    //$('#titleToday').html();
                }
            });
        }

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
                    graphOptions.axes.xaxis.min = result.data[0][0];
                    if (graphToday != null) {
                    	graphToday.destroy();
                    }
                    graphToday = $.jqplot('graphToday', [dataToday], graphOptions);

                    mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy today: ' + result.kwht + ' kWh</div>').insertAfter('#graphToday .jqplot-grid-canvas');
                    //$('#titleToday').html();
                }
            });
        }


        var graphYesterday = null;
        function updateGraphYesterday() {
            $.ajax({
                url: "server.php?method=getYesterdayValues&invtnum=" + invtnum,
                method: 'GET',
                dataType: 'json',
                success: function(result) {
                    for (line in result.data) {
    				  var object = result.data[line];
    				  dataYesterday.push([object[0], object[1]]);
    				}
                    graphOptions.axes.xaxis.min = result.data[0][0];
                    if (graphYesterday != null) {
                    	graphYesterday.destroy();
                    }
                    graphYesterday = $.jqplot('graphYesterday', [dataYesterday], graphOptions);

                    mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy Yesterday: ' + result.kwht + ' kWh</div>').insertAfter('#graphYesterday .jqplot-grid-canvas');
                    //$('#titleYesterday').html();
                }
            });
        }

        updateGraphToday(); // init
        updateGraphYesterday(); // init
        updateGraphLastDays(); // init
        //setInterval(updateGraphToday, 120000); // 2 min refresh = 120000
});
</script>
<script type="text/javascript">
var PLANT_POWER = <?php echo($PLANT_POWER); ?>;

var strP = (PLANT_POWER / 2) / 10;
var strA = 8 / 10;
var strV = 500 / 10;

var GP = 3600 / 10;
var GA = 16 / 10;
var GV = 230 / 10;
var Eff = 100 / 10;

var FRQ = 50;
var BOOT = 70 / 10;
var INVT = 70 / 10;

var gaugeToday;
$(document).ready(function(){
    var invtnum = <?php echo($invtnum); ?>;

    var gaugeStrPOptions = {
        title: 'DC Power', grid: { background: '#D3DAE2' },

        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {

                label: 'W', min: 0, max: PLANT_POWER, padding: 0,
                intervals:[strP, strP * 2, strP * 3, strP *4, strP * 5, strP * 6, strP * 7, strP * 8, strP * 9, strP * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };

    var gaugeStrVOptions = {
        title: 'Volt', grid: { background: '#D3DAE2' },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: 'V', min: 0, max: strV*10, padding: 0,
                intervals:[strV, strV * 2, strV * 3, strV *4, strV * 5, strV * 6, strV * 7, strV * 8, strV * 9, strV * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };

    var gaugeStrAOptions = {
        title: 'Amp', grid: { background: '#D3DAE2' },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: 'A', min: 0, max: strA*10, padding: 0,
                intervals:[strA, strA * 2, strA * 3, strA *4, strA * 5, strA * 6, strA * 7, strA * 8, strA * 9, strA * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };
   var gaugeGPOptions = {
            title: 'AC Power', grid: { background: '#D3DAE2' },
            seriesDefaults: {
                renderer: $.jqplot.MeterGaugeRenderer,
                rendererOptions: {
                    label: 'P', min: 0, max: GP*10, padding: 0,
                    intervals:[GP, GP * 2, GP * 3, GP *4, GP * 5, GP * 6, GP * 7, GP * 8, GP * 9, GP * 10],
                    intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
                }
            }
        };


    var gaugeGVOptions = {
            title: 'Volt', grid: { background: '#D3DAE2' },
            seriesDefaults: {
                renderer: $.jqplot.MeterGaugeRenderer,
                rendererOptions: {
                    label: 'V', min: 0, max: GV*10, padding: 0,
                    intervals:[GV, GV * 2, GV * 3, GV *4, GV * 5, GV * 6, GV * 7, GV * 8, GV * 9, GV * 10],
                    intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
                }
            }
        };


    var gaugeGAOptions = {
        title: 'Amp', grid: { background: '#D3DAE2' },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: 'A', min: 0, max: GA*10, padding: 0,
                intervals:[GA, GA * 2, GA * 3, GA *4, GA * 5, GA * 6, GA * 7, GA * 8, GA * 9, GA * 10],
                intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
            }
        }
    };

    var gaugeEffOptions = {
            title: 'Eff', grid: { background: '#D3DAE2' },
            seriesDefaults: {
                renderer: $.jqplot.MeterGaugeRenderer,
                rendererOptions: {
                    label: '%', min: 0, max: Eff*10, padding: 0,
                    intervals:[Eff, Eff * 2, Eff * 3, Eff *4, Eff * 5, Eff * 6, Eff * 7, Eff * 8, Eff * 9, Eff * 10],
                    intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
                }
            }
        };

    var gaugeFRQOptions = {
            title: 'FRQ', grid: { background: '#D3DAE2' },
            seriesDefaults: {
                renderer: $.jqplot.MeterGaugeRenderer,
                rendererOptions: {
                    label: '%', min: FRQ-1, max: FRQ+1, padding: 0,
                    intervals:[FRQ-1, FRQ, FRQ+1],
                    intervalColors:['#F9FFFB', '#8BFEA8', '#01F33E']
                }
            }
        };

    var gaugeBOOTOptions = {
            title: 'BOOT', grid: { background: '#D3DAE2' },
            seriesDefaults: {
                renderer: $.jqplot.MeterGaugeRenderer,
                rendererOptions: {
                    label: '%', min: 0, max: BOOT*10, padding: 0,
                    intervals:[BOOT, BOOT * 2, BOOT * 3, BOOT *4, BOOT * 5, BOOT * 6, BOOT * 7, BOOT * 8, BOOT * 9, BOOT * 10],
                    intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
                }
            }
        };

    var gaugeINVTOptions = {
            title: 'INVT', grid: { background: '#D3DAE2' },labelPosition: 'bottom',
            seriesDefaults: {
                renderer: $.jqplot.MeterGaugeRenderer,
                rendererOptions: {
                    label: '%', min: 0, max: INVT*10, padding: 0,

                    intervals:[INVT, INVT * 2, INVT * 3, INVT *4, INVT * 5, INVT * 6, INVT * 7, INVT * 8, INVT * 9, INVT * 10],
                    intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
                }
            }
        };


    // Init gauges
    var gaugeGP2 = $.jqplot('gaugeGP2',[[0.1]], gaugeStrPOptions);
    var gaugeDCP = $.jqplot('gaugeDCP',[[0.1]], gaugeGPOptions);
    var gaugeEff = $.jqplot('gaugeEff',[[0.1]], gaugeEffOptions);


    var gaugeStr1P = $.jqplot('gaugeStr1P',[[0.1]], gaugeStrPOptions);
    var gaugeStr1V = $.jqplot('gaugeStr1V',[[0.1]], gaugeStrVOptions);
    var gaugeStr1A = $.jqplot('gaugeStr1A',[[0.1]], gaugeStrAOptions);

    var gaugeStr2P = $.jqplot('gaugeStr2P',[[0.1]], gaugeStrPOptions);
    var gaugeStr2V = $.jqplot('gaugeStr2V',[[0.1]], gaugeStrVOptions);
    var gaugeStr2A = $.jqplot('gaugeStr2A',[[0.1]], gaugeStrAOptions);

    var gaugeGP = $.jqplot('gaugeGP',[[0.1]], gaugeGPOptions);
    var gaugeGV = $.jqplot('gaugeGV',[[0.1]], gaugeGVOptions);
    var gaugeGA = $.jqplot('gaugeGA',[[0.1]], gaugeGAOptions);

    var gaugeFRQ = $.jqplot('gaugeFRQ',[[0.1]], gaugeFRQOptions);
    var gaugeBOOT = $.jqplot('gaugeBOOT',[[0.1]], gaugeBOOTOptions);
    var gaugeINVT = $.jqplot('gaugeINVT',[[0.1]], gaugeINVTOptions);

    setInterval(function(){
        $.ajax({
            url: "server.php?method=getLiveData&invtnum=" + invtnum,
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                gaugeGP2.series[0].data = [['W', result.liveData.valueGP]];
                gaugeGP2.series[0].label = Math.round(result.liveData.valueGP) + ' W';
                gaugeGP2.replot();

                gaugeDCP.series[0].data = [['W', result.liveData.valueI1P+result.liveData.valueI2P]];
                gaugeDCP.series[0].label = Math.round(result.liveData.valueI1P + result.liveData.valueI2P) + ' W';
                gaugeDCP.replot();

                gaugeStr1P.series[0].data = [['W', result.liveData.valueI1P]];
                gaugeStr1P.series[0].label = Math.round(result.liveData.valueI1P) + ' W';
                gaugeStr1P.replot();

                gaugeStr1V.series[0].data = [['V', result.liveData.valueI1V]];
                gaugeStr1V.series[0].label = Math.round(result.liveData.valueI1V) + ' V';
                gaugeStr1V.replot();

                gaugeStr1A.series[0].data = [['A', result.liveData.valueI1A]];
                gaugeStr1A.series[0].label = Math.round(result.liveData.valueI1A * 10) / 10 + ' A';
                gaugeStr1A.replot();

                gaugeStr2P.series[0].data = [['W', result.liveData.valueI2P]];
                gaugeStr2P.series[0].label = Math.round(result.liveData.valueI2P) + ' W';
                gaugeStr2P.replot();

                gaugeStr2V.series[0].data = [['V', result.liveData.valueI2V]];
                gaugeStr2V.series[0].label = Math.round(result.liveData.valueI2V) + ' V';
                gaugeStr2V.replot();

                gaugeStr2A.series[0].data = [['A', result.liveData.valueI2A]];
                gaugeStr2A.series[0].label = Math.round(result.liveData.valueI2A * 10) / 10 + ' A';
                gaugeStr2A.replot();

                gaugeGP.series[0].data = [['W', result.liveData.valueGP]];
                gaugeGP.series[0].label = Math.round(result.liveData.valueGP) + ' W';
                gaugeGP.replot();

                gaugeGV.series[0].data = [['V', result.liveData.valueGV]];
                gaugeGV.series[0].label = Math.round(result.liveData.valueGV) + ' V';
                gaugeGV.replot();

                gaugeGA.series[0].data = [['A', result.liveData.valueGA]];
                gaugeGA.series[0].label = Math.round(result.liveData.valueGA * 10) / 10 + ' A';
                gaugeGA.replot();

                gaugeEff.series[0].data = [['A', result.liveData.valueEFF]];
                gaugeEff.series[0].label = Math.round(result.liveData.valueEFF) + ' %';
                gaugeEff.replot();

                gaugeFRQ.series[0].data = [['Hz', result.liveData.valueFRQ]];
                gaugeFRQ.series[0].label = Math.round(result.liveData.valueFRQ) + ' Hz';
                gaugeFRQ.replot();

                gaugeBOOT.series[0].data = [['�C', result.liveData.valueBOOT]];
                gaugeBOOT.series[0].label = Math.round(result.liveData.valueBOOT * 10) / 10 + ' C';
                gaugeBOOT.replot();

                gaugeINVT.series[0].data = [['�C', result.liveData.valueINVT]];
                gaugeINVT.series[0].label = Math.round(result.liveData.valueINVT) + ' C';
                gaugeINVT.replot();
            }
        });
    }, 2500); // 2.5 secs refresh
});
</script>
<?php include("styles/".$user_style."/footer.php"); ?>