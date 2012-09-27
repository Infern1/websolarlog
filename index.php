<?php
require_once("classes/classloader.php");
$template = $config->template;
$template = "green";
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){
		var sliders = WSL.init_sliders("index","#main-middle",
				function(){
					if (sliders){
						var hGraphToday = null;
						var fnGraphToday = function(handle){hGraphToday=handle;};
						WSL.createDayGraph(1, "graphTodayContent","Today", fnGraphToday); // Initial load fast
						window.setInterval(function(){hGraphToday.destroy(); WSL.createDayGraph(1, "graphTodayContent","Today", fnGraphToday);}, 10000); // every 10 seconds

						var hGraphYesterday = null;
						var fnGraphYesterday = function(handle){hGraphYesterday=handle;};
						WSL.createDayGraph(1, "graphYesterdayContent","Today", fnGraphYesterday); // Initial load fast
						window.setInterval(function(){hGraphYesterday.destroy(); WSL.createDayGraph(1, "graphYesterdayContent","Today", fnGraphYesterday);}, 10000); // every 10 seconds

						//WSL.init_PageIndexValues("#content"); // Initial load fast
						//window.setInterval(function(){WSL.createDayGraph(1, "graphLastDaysContent","Today");}, 10000); // every 10 seconds

						WSL.init_PageIndexValues("#content","#sidebar"); // Initial load fast
						window.setInterval(function(){WSL.init_PageIndexValues("#content","#sidebar");}, 3000); // every 3 seconds
						init_carousel();
					}
    			}
		)
	});
	</script>
	<!-- END Wrapper -->
</body>
</html>