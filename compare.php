<?php
require_once("classes/classloader.php");
Session::initialize();

$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
	var whichMonth = '<?php echo date("m");?>';
	var whichYear = '<?php echo date("Y");?>';
	var compareMonth = '<?php echo date("m");?>';
	var compareYear = '<?php echo date("Y");?>';
	$(function(){
		var invtnum=(!invtnum)?invtnum=1:invtnum=invtnum;
		WSL.init_compare(invtnum,"#content");
	});

	 // Initial load fast
	analyticsJSCodeBlock();
	</script>
</body>
</html>