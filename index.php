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
					$( "#tabs" ).tabs();
					$( ".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *" ).removeClass( "ui-corner-all ui-corner-top" ).addClass( "ui-corner-bottom" );

						/*
						/* old graph code for memory optimizations.
						*/
						
						var hGraphToday = null;
						var fnGraphToday = function(handle){hGraphToday=handle;};
						WSL.createDayGraph(1, "Today", fnGraphToday); // Initial load fast
						//window.setInterval(function(){hGraphToday.destroy(); WSL.createDayGraph(1, "Today", fnGraphToday);}, 10000); // every 10 seconds

						//var hGraphYesterday = null;
						//var fnGraphYesterday = function(handle){hGraphYesterday=handle;};
						//WSL.createDayGraph(1, "Yesterday", fnGraphYesterday); // Initial load fast
						
						/*
						/*  / old graph code for memory optimizations.
						*/
						
						//WSL.createDayGraph(1,"Today");


						//WSL.init_PageIndexValues("#content","#sidebar"); // Initial load fast
						//window.setInterval(function(){WSL.init_PageIndexValues("#content","#sidebar");}, 3000); // every 3 seconds

					}
    			}
		)
	});
	</script>
	<!-- END Wrapper -->
</body>
</html>