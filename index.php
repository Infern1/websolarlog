<?php
// For debugging only
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("classes/classloader.php");
Session::setTimezone();

$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){
		WSL.init_tabs("index","#main-middle",
			function(){
				WSL.init_PageIndexAddContainers("#content","#sidebar"); // Initial load fast
    		}
		),			
		WSL.init_PageIndexLiveValues("#indexLiveInverters"); // Initial load fast
		window.setInterval(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");}, 2500); // every 5 seconds

	});
	</script>
	<!-- END Wrapper -->
</body>
</html>