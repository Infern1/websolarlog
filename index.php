<?php
require_once("classes/classloader.php");
$template = "green";
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){
		WSL.init_tabs("index","#main-middle",
			function(){
					WSL.init_PageIndexValues("#content","#sidebar"); // Initial load fast
					window.setInterval(function(){WSL.init_PageIndexValues("#content","#sidebar");}, 30000); // every 3 seconds
    		}
		)
	});
	</script>
	<!-- END Wrapper -->
</body>
</html>