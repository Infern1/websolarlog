<?php
require_once("classes/classloader.php");
Session::initializeLight();

$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){
		$("#contentLoading").hide();
		WSL.init_summaryPage("#content");
	});

	 // Initial load fast
	analyticsJSCodeBlock();
	</script>
	<!-- END Wrapper -->
</body>
</html>