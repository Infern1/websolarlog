<?php
require_once("classes/classloader.php");
Session::initializeLight();

$config = Session::getConfig();

require_once("template/" .  $config->template . "/header.php");
require_once("template/" .  $config->template . "/index.php");
?>
<input type="hidden" id="graphReady" value="false"/>
	<script type="text/javascript">
    // Make sure the page is loaded
    
    // every minute
    //var sidebar = window.setInterval(function(){WSL.init_PageIndexTotalValues("#sidebar");}, 60000);
	// every 5 seconds
	//var indexLiveInverters = 
	 
	$(function(){
		WSL.init_mainSummary("#main-middle");
		WSL.init_tabs("index",0, "#main-middle",
			function(){
			$('#graphReady').on("change",function(){
				if($('#graphReady').val() == 'true'){			
					WSL.init_PageLiveValues("#content",function(){indexLiveInverters});
					WSL.init_PageIndexTotalValues("#sidebar",function(){sidebar});
					window.setTimeout(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");},100);
					
					$('#graphTodayContent canvas').ready(function(){
						window.setInterval(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");}, 2000);
					});
					analyticsJSCodeBlock();
	    		}		    		
			})	
    		}
		)	
	});

			

	
	</script>
	<!-- END Wrapper -->
	
</body>
</html>