<?php
require_once("classes/classloader.php");
$template = Session::getConfig()->template;
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
	var whichMonth = <?php echo date("m");?>;
	var whichYear = <?php echo date("Y");?>;
	var compareMonth = <?php echo date("m");?>;
	var compareYear = <?php echo date("Y");?>;
	WSL.init_compare(1,"#content",
			function(){
				WSL.createCompareGraph(1,whichMonth,whichYear,compareMonth,compareYear,0);
			}
	); // Initial load fast
	</script>
</body>
</html>