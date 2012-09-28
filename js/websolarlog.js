// calculate the JS parse time //
$.ajaxSetup({ cache: false });
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
	
	init_sliders : function(page ,divId,success) {
		// initialize languages selector on the given div
		WSL.api.getSliders(page ,function(data) {
			$.ajax({
				url : 'js/templates/slider.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(html).prependTo(divId);
					success.call(); 
				},
				dataType : 'text',
			});
		});
		return true;
	},	
	
	init_PageIndexValues : function(divId,SideBar) {
		// initialize languages selector on the given div
		WSL.api.getPageIndexValues(function(data) {
			var GP = 3600 / 10;
			   var gaugeGPOptions = {
			            title: 'AC Power', grid: { background: '#FFF' },
			            seriesDefaults: {
			                renderer: $.jqplot.MeterGaugeRenderer,
			                rendererOptions: {
			                    min: 0, max: GP*10, padding: 0,
			                    intervals:[GP, GP * 2, GP * 3, GP *4, GP * 5, GP * 6, GP * 7, GP * 8, GP * 9, GP * 10],
			                    intervalColors:['#F9FFFB','#EAFFEF', '#CAFFD8', '#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95', '#4BFE78', '#0AFE47', '#01F33E']
			                }
			            }
			        };
			
			$.ajax({
				url : 'js/templates/liveValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(divId).html(html);
				},
				dataType : 'text',
			});
			$.ajax({
				url : 'js/templates/totalValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(SideBar).html(html);
					var gaugeGP = $.jqplot('gaugeGP',[[0.1]], gaugeGPOptions);
					gaugeGP.series[0].data = [['W', data.IndexValues.inverters[0].live[2].value]];
	                gaugeGP.series[0].label = Math.round(data.IndexValues.inverters[0].live[2].value) + ' W';
	                gaugeGP.replot();
				},
				dataType : 'text',
			});
		});
	},
	
	createDayGraph : function(invtnum, divId, getDay, fnFinish) {
		var graphOptions = {
			series : [{label: '1',yaxis:'yaxis'},{label:'2',yaxis:'y2axis'}],
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
					label : 'Cum. Power(W)',
					min : 0,
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer
				},
				y2axis : {
					label : 'Avg. Power(W)2',
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
			async: false,
			success : function(result) {
				var dataDay1 = [];
				var dataDay2 = [];
				for (line in result.dayData.data) {;
					var object = result.dayData.data[line];
					dataDay1.push([ object[0], object[1] ]);
					dataDay2.push([ object[0], object[2] ]);
				}
				
				
				graphOptions.axes.xaxis.min = result.dayData.data[0][0];
				//$.jqplot(divId, [ dataDay ], graphOptions).destroy();
				$('.graph' + getDay + 'Content').remove();
				$('.graph' + getDay).append('<div id="graph' + getDay + 'Content"></div>');
				handle = $.jqplot(divId, [ dataDay1,dataDay2 ], graphOptions);
				mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy ' + getDay.toLowerCase() + ': ' + result.dayData.valueKWHT + ' kWh</div>').insertAfter('#graph' + getDay + ' .jqplot-grid-canvas');
				
				fnFinish.call(this, handle);
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

WSL.api.getSliders = function(page, success) {
	$.getJSON("server.php", {
		method : 'getSlider',
		'page' : page,
	}, success);
};

WSL.api.getPageIndexValues = function(success) {
	$.getJSON("server.php", {
		method : 'getPageIndexValues',
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