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
		var tabs = WSL.init_tabs("index","#main-middle",
			function(){
				if (tabs){
					var hGraphToday = null;
					var fnGraphToday = function(handle){hGraphToday=handle;};
					WSL.createDayGraph(1, "Today", fnGraphToday); // Initial load fast
					//window.setInterval(function(){hGraphToday.destroy(); WSL.createDayGraph(1, "Today", fnGraphToday);}, 10000); // every 10 seconds

					//var hGraphYesterday = null;
					//var fnGraphYesterday = function(handle){hGraphYesterday=handle;};
					//WSL.createDayGraph(1, "Yesterday", fnGraphYesterday); // Initial load fast

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