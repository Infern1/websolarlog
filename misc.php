<?php
require_once("classes/classloader.php");
Session::initialize();

$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>

	<script type="text/javascript">
	$(function(){
		WSL.init_tabs("index", 0, "#main-middle", function(){
			WSL.init_misc(1,"#columns"); // Initial load fast
		});
		

	});
	analyticsJSCodeBlock();
	</script>
	
</body>
</html>