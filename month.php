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
		WSL.init_tabs("index",2,"#main-middle",
			function(){
				WSL.init_PageMonthValues("#columns","#periodList"); // Initial load fast
    		}
		)
	});
	analyticsJSCodeBlock();
	</script>
	<!-- END Wrapper -->
</body>
</html>