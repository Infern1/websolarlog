<?php
define('checkaccess', TRUE);
define('starttime', microtime(true));
include("config/config_main.php");

if (!empty ($_POST['user_lang'])) {
	setcookie('user_lang',$_POST['user_lang'],strtotime('+5 year'));
	$user_lang=$_POST['user_lang'];
} elseif (isset($_COOKIE['user_lang'])){
	$user_lang=$_COOKIE['user_lang'];
} else {
	$user_lang="English";
}
include("languages/".$user_lang.".php");

if (!empty ($_POST['user_style'])) {
	setcookie('user_style',$_POST['user_style'],strtotime('+5 year'));
	$user_style=$_POST['user_style'];
} elseif (isset($_COOKIE['user_style'])){
	$user_style=$_COOKIE['user_style'];
} else {
	$user_style="default";
}

function tricsv($var){
	return !is_dir($var)&& preg_match('/.*\.csv/', $var);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<title><?php echo "$TITLE";?></title>
<link href="favicon.ico" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="js/jqueryuicss/jquery-ui.css"
	type="text/css" />
<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css" />
<link rel="stylesheet" href="js/jgauge/css/jgauge.css" type="text/css" />
<link rel="stylesheet" href="css/main.css" type="text/css" />
<link rel="stylesheet"
	href="styles/<?php echo $user_style;?>/css/style.css" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/modules/exporting.js"></script>
<script type="text/javascript" src="js/handlebars.js"></script>
<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.json2.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.meterGaugeRenderer.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.trendline.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript"
	src="js/jqplot_plugins/jqplot.highlighter.min.js"></script>
<!--[if IE]><script type="text/javascript" language="javascript" src="js/jgauge/js/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript"
	src="js/jgauge/js/jQueryRotate.min.js"></script>
<script language="javascript" type="text/javascript"
	src="js/jgauge/js/jgauge-0.3.0.a3.js"></script>
<script language="javascript" type="text/javascript"
	src="js/websolarlog.js"></script>
<?php include("styles/yourheader.php");?>
</head>
<?php
if ($NUMINV==1) {
	include("styles/".$user_style."/header.php");
} else {
	include("styles/".$user_style."/header_multi.php");
  }
?>