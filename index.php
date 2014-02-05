<input type="hidden" id="graphReady" value="false"/>
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
    //var sidebar = window.setInterval(function(){WSL.init_PageIndexTotalValues("#sidebar");}, 60000);
	// every 5 seconds
	//var indexLiveInverters = 
	 
	$(function(){
		WSL.init_mainSummary("#main-middle");
		WSL.init_tabs("index",0, "#main-middle",
				function(){
			$('#graphReady').on("change",function(){
				if($('#graphReady').val() == 'true'){
				
					console.log('loading');
					WSL.init_PageLiveValues("#content",function(){indexLiveInverters});
					WSL.init_PageIndexTotalValues("#sidebar",function(){sidebar});
					analyticsJSCodeBlock();
					$('#graphTodayContent canvas').ready(function(){
						window.setInterval(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");}, 5000);
					});
	    		}else{
		    		console.log('graph not ready....');
	    		}
		    		
			})
		}
		)	
	});

			

	
	</script>
	<!-- END Wrapper -->

</body>
</html>