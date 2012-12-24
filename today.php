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
				$('#tabs').tabs({ selected: 0 });
				WSL.init_PageTodayValues("#columns",
						function(){
						WSL.init_PageTodayHistoryValues("#history"); // Initial load fast
					}); // Initial load fast
    		}
		);
	});
	analyticsJSCodeBlock();
	</script>
	<!-- END Wrapper -->
</body>
</html>