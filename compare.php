<?php
require_once("classes/classloader.php");
$template = $config->template;
$template = "green";
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>
	<script type="text/javascript">
    // Make sure the page is loaded
	
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