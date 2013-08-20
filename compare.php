<?php
require_once("classes/classloader.php");
Session::initializeLight();

$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
	var whichMonth = '<?php echo date("n");?>';
	var whichYear = '<?php echo date("Y");?>';
	var compareMonth = '<?php echo date("n");?>';
	var compareYear = '<?php echo date("Y");?>';
	$(function(){
		$("#contentLoading").hide();
		WSL.init_compare("#content");
	});

	 // Initial load fast
	analyticsJSCodeBlock();
	</script>
</body>
</html>