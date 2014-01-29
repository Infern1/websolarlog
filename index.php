<?php
require_once("classes/classloader.php");
Session::initializeLight();

$config = Session::getConfig();

require_once("template/" .  $config->template . "/header.php");
require_once("template/" .  $config->template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
    
    // every minute
    var sidebar = window.setInterval(function(){WSL.init_PageIndexTotalValues("#sidebar");}, 60000);
	// every 5 seconds
	var indexLiveInverters = window.setInterval(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");}, 5000);
	 
	$(function(){
		WSL.init_mainSummary("#main-middle");
		WSL.init_tabs("index",0, "#main-middle",
			function(){
				WSL.init_PageLiveValues("#content",function(){sidebar});
				WSL.init_PageIndexTotalValues("#sidebar",function(){ndexLiveInverters});
    		}
		)	
	});
	analyticsJSCodeBlock();
	</script>
	<!-- END Wrapper -->
</body>
</html>