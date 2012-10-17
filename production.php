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

		var invtnum=(!invtnum)?invtnum=1:invtnum=invtnum;
		WSL.init_production(invtnum,"content"); // Initial load fast
	});
	</script>
	
</body>
</html>