<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

return array(
		'cssPrint' =>array(
				'//websolarlog/css/blueprint/print.css'
		),
		'cssProjection' =>array(
				'//websolarlog/css/blueprint/screen.css'
		),
		'css' => array(
				'//websolarlog/css/blueprint/screen.css',
				'//websolarlog/template/green/css/style.css',
				'//websolarlog/css/jquery.jqplot.min.css',
				'//websolarlog/css/jquery.jqplot.overrule.style.css',
				'//websolarlog/css/jquery.pnotify.default.css',
				'//websolarlog/js/jqueryuicss/jquery-ui.min.css',
				'//websolarlog/js/jqueryuicss/jquery.ui.overrule.css',
				'//websolarlog/template/green/css/custom.css',
		),
		'js1' => array(
				'//websolarlog/js/jquery-2.1.0.min.js', 
				'//websolarlog/js/jquery-ui-1.10.4.custom.min.js',
		),
		'js2' => array(
				'//websolarlog/js/jquery.pnotify-1.2.0.min.js',
				'//websolarlog/js/handlebars-1.3.js',
				'//websolarlog/js/astrocal.js',
				'//websolarlog/js/jquery.jqplot-1.0.8r1250.min.js',
		),
		
		'js3'=>array(
				'//websolarlog/js/moment-2.4.0.min.js',
				'//websolarlog/js/helpers.js',
				'//websolarlog/js/suncalc.js',
				'//websolarlog/js/jqplot_plugins/jqplot.json2.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.barRenderer.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.canvasTextRenderer.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.canvasAxisTickRenderer.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.canvasAxisLabelRenderer.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.canvasOverlay.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.dateAxisRenderer.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.meterGaugeRenderer.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.cursor.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.trendline.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.pointLabels.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.highlighter.min.js',
				'//websolarlog/js/jqplot_plugins/jqplot.enhancedLegendRenderer.min.js',
				'//websolarlog/js/websolarlog.js',
		),
		
);
