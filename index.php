<?php
require_once("classes/classloader.php");
Session::initialize();

$template = Session::getConfig()->template;												
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){
		WSL.init_tabs("index","#main-middle",
			function(){
				WSL.init_PageLiveValues("#content"); // Initial load fast
				WSL.init_PageIndexTotalValues("#sidebar");
    		}
		),			
		window.setInterval(function(){WSL.init_PageIndexLiveValues("#indexLiveInverters");}, 2500); // every 5 seconds
		window.setInterval(function(){WSL.init_PageIndexTotalValues("#sidebar");}, 60000); // every 5 seconds		
	});
	analyticsJSCodeBlock();
	</script>
	<!-- END Wrapper -->
</body>
</html>