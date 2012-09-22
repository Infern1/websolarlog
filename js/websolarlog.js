// calculate the JS parse time //
beforeLoad = (new Date()).getTime();
window.onload = pageLoadingTime;
function pageLoadingTime() {
	afterLoad = (new Date()).getTime();
	secondes = (afterLoad - beforeLoad) / 1000;
	document.getElementById("JSloadingtime").innerHTML = secondes;
}
var graphDay = null;
var dataDay = [];
var dataYesterday = [];
var dataLastDays = [];
var alreadyFetched = [];

// WSL class
var WSL = {
	api : {},
	init_events : function(invtnum, divId) {
		// Retrieve the error events
		WSL.api.getEvents(invtnum, function(data) {
			$.ajax({
				url : 'js/templates/events.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(divId).html(html);
				},
				dataType : 'text'
			});
		});
	},
	init_liveData : function(invtnum, divId) {
		// Retrieve the error events
		WSL.api.getLiveData(invtnum, function(data) {
			if (data.liveData.success) {
				$.ajax({
					url : 'js/templates/livedata.hb',
					success : function(source) {
						var template = Handlebars.compile(source);
						var html = template({
							'data' : data.liveData
						});
						$(divId).html(html);
					},
					dataType : 'text'
				});
			} else {
				alert(data.liveData.message);
			}
		});
	},
	init_plantInfo : function(invtnum, divId) {
		// Retrieve the error events
		WSL.api.getPlantInfo(invtnum, function(data) {
			if (data.plantInfo.success) {
				$.ajax({
					url : 'js/templates/plantinfo.hb',
					success : function(source) {
						var template = Handlebars.compile(source);
						var html = template({
							'data' : data.plantInfo
						});
						$(divId).html(html);
					},
					dataType : 'text'
				});
			} else {
				alert(data.plantInfo.message);
			}
		});

	},
	init_menu : function(divId) {
		WSL.api.getMenu(function(data) {
			$.ajax({
				url : 'js/templates/menu.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(divId).html(html);
				},
				dataType : 'text'
			});
		});
	},
	init_languages : function(divId) {
		// initialize languages selector on the given div
		WSL.api.getLanguages(function(data) {
			$.ajax({
				url : 'js/templates/languageselect.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(divId).html(html);
				},
				dataType : 'text'
			});
		});
	},
	createDayGraph : function(invtnum, divId, getDay) {
		var graphOptions = {
			series : [ {
				showMarker : false,
				fill : true
			} ],
			axesDefaults : {
				tickRenderer : $.jqplot.CanvasAxisTickRenderer
			},
			axes : {
				xaxis : {
					label : '',
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
					renderer : $.jqplot.DateAxisRenderer,
					tickInterval : '3600', // 1 hour
					tickOptions : {
						angle : -30,
						formatString : '%H:%M'
					}
				},
				yaxis : {
					label : 'Avg. Power(W)',
					min : 0,
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer
				}
			},
			highlighter : {
				show : true,
				sizeAdjust : 7.5
			},
			cursor : {
				show : false
			}
		};

		$.ajax({
			url : "server.php?method=get" + getDay + "Values&invtnum=" + invtnum + "&r=" + Math.floor(Math.random() * 111111),
			method : 'GET',
			dataType : 'json',
			async : false,
			success : function(result) {
				var dataDay = [];
				for (line in result.dayData.data) {;
					var object = result.dayData.data[line];
					dataDay.push([ object[0], object[1] ]);
				}
				
				
				graphOptions.axes.xaxis.min = result.dayData.data[0][0];
				$.jqplot(divId, [ dataDay ], graphOptions).destroy();
				$('.graph' + getDay + 'Content').remove();
				$('.graph' + getDay).append('<div id="graph' + getDay + 'Content"></div>');
				$.jqplot(divId, [ dataDay ], graphOptions);
				mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy ' + getDay.toLowerCase() + ': ' + result.dayData.valueKWHT + ' kWh</div>').insertAfter('#graph' + getDay + ' .jqplot-grid-canvas');
			}
		});
	},

	createGraphLastDays : function(invtnum, divId) {
		var graphLastDaysOptions = {
			// The "seriesDefaults" option is an options object that will
			// be applied to all series in the chart.
			seriesDefaults : {
				renderer : $.jqplot.BarRenderer,
				rendererOptions : {
					fillToZero : true,
					barWidth : 5
				},
				showMarker : false,
				pointLabels : {
					show : true
				}
			},
			// Custom labels for the series are specified with the "label"
			// option on the series option. Here a series option object
			// is specified for each series.
			highlighter : {
				show : true,
				sizeAdjust : 7.5
			},

			// series:[{label:'Hotel'}],
			axesDefaults : {
				tickRenderer : $.jqplot.CanvasAxisTickRenderer,
				tickOptions : {
					angle : -30,
					fontSize : '10pt'
				}
			},
			// Show the legend and put it outside the grid, but inside the
			// plot container, shrinking the grid to accomodate the legend.
			// A value of "outside" would not shrink the grid and allow
			// the legend to overflow the container.
			legend : {
				show : false
			},
			axes : {
				// Use a category axis on the x axis and use our custom ticks.
				xaxis : {
					label : '',
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
					renderer : $.jqplot.DateAxisRenderer,
					angle : -30,
					tickOptions : {
						formatString : '%d-%m'
					}
				},
				// Pad the y axis just a little so bars can get close to, but
				// not touch, the grid boundaries. 1.2 is the default padding.
				yaxis : {
					label : 'Power(W)',
					min : 0,
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer
				}
			}
		};

		$.ajax({
			url : "server.php?method=getLastDaysValues&invtnum="+ invtnum + "&r=" + Math.floor(Math.random() * 111111),
			method : 'GET',
			dataType : 'json',
			async : false,
			success : function(result) {
				var lastDaysData = [];
				for (line in result.lastDaysData.data) {;
					var object = result.lastDaysData.data[line];
					lastDaysData.push([ object[0], object[1] ]);
				}
				
				graphLastDaysOptions.axes.xaxis.min = result.lastDaysData.data[0][0];
				$.jqplot(divId, [ lastDaysData ], graphLastDaysOptions).destroy();
				// alert('destroy');
				//$('.graphLastDaysContent').remove();
				//$('.graphLastDays').append('<div id="graphLastDaysContent"></div>');
					
				$.jqplot(divId, [ lastDaysData ], graphLastDaysOptions);
				//mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy ' + getDay.toLowerCase() + ': ' + result.dayData.valueKWHT + ' kWh</div>').insertAfter('#graph' + getDay + ' .jqplot-grid-canvas');
			}
		});
	

		// mytitle = $('<div class="my-jqplot-title"
		// style="position:absolute;text-align:center;padding-top:
		// 1px;width:100%">Total energy today2: ' + source.kwht + '
		// kWh</div>').insertAfter('#graphToday2 .jqplot-grid-canvas');
	}

};

// api class
WSL.api.programdayfeed = function(invtnum, success) {
	$.getJSON("programs/programdayfeed.php", {
		invtnum : invtnum
	}, success);
};

WSL.api.getEvents = function(invtnum, success) {
	$.getJSON("server.php", {
		method : 'getEvents',
		'invtnum' : invtnum,
	}, success);
};

WSL.api.getLiveData = function(invtnum, success) {
	$.getJSON("server.php", {
		method : 'getLiveData',
		'invtnum' : invtnum,
	}, success);
};

WSL.api.getPlantInfo = function(invtnum, success) {
	$.getJSON("server.php", {
		method : 'getPlantInfo',
		'invtnum' : invtnum,
	}, success);
};

WSL.api.getLanguages = function(success) {
	$.getJSON("server.php", {
		method : 'getLanguages'
	}, success);
};

WSL.api.getMenu = function(success) {
	$.getJSON("server.php", {
		method : 'getMenu'
	}, success);
};