<?php
require_once("classes/classloader.php");
Session::initialize();

$template = Session::getConfig()->template;												
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
	function updatePowerValues(divid) {
		var div = $('#'+divid);
		$.getJSON("api.php/Live", {} , function($result) {
			div.html("");
			$.each($result, function(){
				if (this.type == "metering") {
					div.append(this.name + "&nbsp;live energy:&nbsp;" + this.data.liveEnergy + " Watt<br />");
				}
			});
		});
	}
	
	function updatePowerGraph(divid) {
		var div = $('#'+divid);
		$.getJSON("api.php/History/Metering/", {} , function(result) {
			$('#powerHistory').remove();
			div.after("<div id='powerHistory'></div>");
			
			var data = new Array();
			$.each(result.data, function() {
				data.push([this.time*1000, (this.liveUsage-this.liveReturn)]);
			});
			var plot1 = $.jqplot('powerHistory', [data], {
				    title:'Power usage history',
				    axes:{xaxis:{renderer:$.jqplot.DateAxisRenderer}},
				    series:[{lineWidth:2, markerOptions:{show: false, style:'square'}}],
				    highlighter: {show: true, sizeAdjust: 7.5 }
				    
				  });
		});

		
	}

    // Make sure the page is loaded
	$(function(){
		WSL.init_tabs("index",0, "#main-middle",
			function(){
				WSL.init_PageLiveValues("#content"); // Initial load fast
				WSL.init_PageIndexTotalValues("#sidebar");
    		}
		),			
		window.setInterval(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");}, 5000); // every 5 seconds

		window.setInterval(function(){updatePowerValues("smartMeterLive");}, 5000); // every 5 seconds
		window.setInterval(function(){updatePowerGraph("smartMeterLive");}, 60000); // every 60 seconds
		updatePowerValues("smartMeterLive");
		updatePowerGraph("smartMeterLive");
		window.setInterval(function(){WSL.init_PageIndexTotalValues("#sidebar");}, 60000); // every 5 seconds		
	});
	analyticsJSCodeBlock();
	</script>
	<!-- END Wrapper -->
</body>
</html>