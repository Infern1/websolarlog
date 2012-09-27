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
						WSL.createDayGraph(1, "graphTodayContent","Today"); // Initial load fast
						window.setInterval(function(){WSL.createDayGraph(1, "graphTodayContent","Today");}, 10000); // every 10 seconds
						WSL.createDayGraph(1, "graphYesterdayContent","Today"); // Initial load fast
						window.setInterval(function(){WSL.createDayGraph(1, "graphYesterdayContent","Today");}, 10000); // every 10 seconds
						WSL.init_PageIndexValues("#content"); // Initial load fast
						window.setInterval(function(){WSL.createDayGraph(1, "graphLastDaysContent","Today");}, 10000); // every 10 seconds		
						WSL.init_PageIndexValues("#content","#sidebar"); // Initial load fast
						window.setInterval(function(){WSL.init_PageIndexValues("#content","#sidebar");}, 3000); // every 10 seconds
						init_carousel();					
					}
    			}
		)
	});
	</script>
	<!-- END Wrapper -->
</body>
</html>