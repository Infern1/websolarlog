<?php
require_once("classes/classloader.php");
$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>

	<script type="text/javascript">
	$(function(){
		WSL.init_tabs("index","#main-middle",
				function(){WSL.init_events(1,"#columns"); // Initial load fast
		});
		

	});
	</script>
	
</body>
</html>