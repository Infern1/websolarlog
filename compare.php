<?php
require_once("classes/classloader.php");
//$template = $config->template;
$template = "green";
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>

	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){
		//WSL.init_compare("content"); // Initial load fast
	});
	</script>
	
</body>
</html>