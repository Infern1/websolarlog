<!DOCTYPE HTML>
<html>
<head>
<?php $config = new Config;
$dataAdapter = new PDODataAdapter();
$config = $dataAdapter->readConfig();
?>
	<title><?php echo $config->title; ?></title>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print"/>
    <!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"/><![endif]-->

	<link rel="shortcut icon" href="template/green/css/images/favicon.ico" />
	<link rel="stylesheet" href="template/green/css/style.css" type="text/css" media="all" />
	<link rel="stylesheet" href="template/green/css/custom.css" type="text/css" media="all" />
	<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery.jqplot.overrule.style.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery.pnotify.default.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery.pnotify.default.css" type="text/css" />

	<link rel="stylesheet" href="js/jqueryuicss/jquery-ui-custom.css" type="text/css" />
	
	<script type="text/javascript" src="js/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="js/jquery.pnotify.min.js"></script>
	<script type="text/javascript" src="js/handlebars.js"></script>
	<script type="text/javascript" src="js/helpers.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.json2.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.barRenderer.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.meterGaugeRenderer.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.cursor.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.trendline.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.pointLabels.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.highlighter.min.js"></script>
    <script type="text/javascript" src="js/jqplot_plugins/jqplot.enhancedLegendRenderer.min.js"></script>

    
    <script type="text/javascript" src="js/websolarlog.js"></script>
</head>