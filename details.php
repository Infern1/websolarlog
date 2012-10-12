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

				WSL.init_nextRelease("#columns"); // Initial load fast
				window.setInterval(function(){WSL.init_nextRelease("#columns");}, 300000); // every 3 seconds

	});
	</script>
	
</body>
</html>