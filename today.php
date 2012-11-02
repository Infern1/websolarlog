<?php
require_once("classes/classloader.php");

Session::setLanguage("nl_NL");

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
				//var hGraphToday = null;
				//var fnGraphToday = function(handle){hGraphToday=handle;};
				//WSL.createDayGraph(1, "Today", fnGraphToday); // Initial load fast
				//window.setInterval(function(){hGraphToday.destroy(); WSL.createDayGraph(1, "Today", fnGraphToday);}, 10000); // every 10 seconds

				//var hGraphYesterday = null;
				//var fnGraphYesterday = function(handle){hGraphYesterday=handle;};
				//WSL.createDayGraph(1, "Yesterday", fnGraphYesterday); // Initial load fast
				WSL.init_PageTodayValues("#columns",
						function(){
						WSL.init_PageTodayHistoryValues("#history"); // Initial load fast
					}); // Initial load fast

    		}
		);
	});
	</script>
	<!-- END Wrapper -->
</body>
</html>