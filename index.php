<?php
require_once("classes/classloader.php");

$template = "green";

include("template/" . $template . "/header.php");
?>
- kWh van vandaag
- kWh deze maand
- kWh totaal
- CO2 totaal
- Huidige wattage
- Grafiek van vandaag
- Grafiek van gisteren
- Grafiek laatste xx dagen
<?php 
include("template/" . $template . "/index.php");

?>
	<script type="text/javascript">
		var sliders = WSL.init_sliders("index","#main-middle");

		if (sliders){
		WSL.createDayGraph(1, "graphTodayContent","Today"); // Initial load fast
		window.setInterval(function(){WSL.createDayGraph(1, "graphTodayContent","Today");}, 10000); // every 10 seconds
		WSL.createDayGraph(1, "graphYesterdayContent","Today"); // Initial load fast
		window.setInterval(function(){WSL.createDayGraph(1, "graphYesterdayContent","Today");}, 10000); // every 10 seconds

		WSL.init_PageIndexValues("#content"); // Initial load fast
		window.setInterval(function(){WSL.init_PageIndexValues("todayValues");}, 3000); // every 10 seconds
		}
		
	</script>
	<!-- END Wrapper -->
</body>
</html>