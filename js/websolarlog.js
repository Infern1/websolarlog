trunkVersion = '223';
// calculate the JS parse time //
$.ajaxSetup({
	cache : false
});
beforeLoad = (new Date()).getTime();
window.onload = pageLoadingTime;

function pageLoadingTime() {
	$('#subversion').html(trunkVersion);
	afterLoad = (new Date()).getTime();
	secondes = (afterLoad - beforeLoad) / 1000;
	document.getElementById("JSloadingtime").innerHTML = secondes;
}

var graphDay = null;
var dataDay = [];
var dataYesterday = [];
var dataLastDays = [];
var alreadyFetched = [];

var currentGraphHandler;
var todayTimerHandler;

function tooltipContentEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = ""; 
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Energy:"+ plot.series[1].data[pointIndex][1]+ " W<br>"+bold[1];
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Cum.: "+ plot.series[0].data[pointIndex][1]+ " W<br>"+bold[1];
	returned += "Date:"+ plot.series[0].data[pointIndex][2]+"";
	return returned;
}

function tooltipPeriodContentEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = ""; 
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Energy:"+ plot.series[1].data[pointIndex][1]+ " W<br>"+bold[1];
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Cum.: "+ plot.series[0].data[pointIndex][1]+ " W<br>"+bold[1];
	returned += "Date:"+ plot.series[1].data[pointIndex][2]+"";
	return returned;
}

function tooltipCompareContentEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = ""; 
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Harvested: "+ plot.series[0].data[pointIndex][1] +" kWh<br>"+bold[1];
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Expected:"+ plot.series[1].data[pointIndex][1]+" kWh<br>"+bold[1];
	( seriesIndex == 2 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum. Expected: "+plot.series[2].data[pointIndex][1]+" kWh<br>"+bold[1];
	( seriesIndex == 3 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum. Harvested:"+plot.series[3].data[pointIndex][1]+" kWh<br>"+bold[1];
	return returned;
}

// WSL class
var WSL = {
	api : {},
	init_nextRelease : function(divId) {
		$(divId).html("<br/><br/><H1>WSL::NextRelease();</h1>");
	},
	init_PageTodayHistoryValues : function(divId) {
		// Retrieve the error events
		WSL.api.getHistoryValues(function(data) {
			$.ajax({
				url : 'js/templates/historyValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(divId).html(html);
			        $( "#todayHistoryAcc" ).accordion({
			            collapsible: true
			        });
				},
				dataType : 'text'
			});
		});
	},
	
	init_PageIndexLiveValues : function(divId) {
		// initialize languages selector on the given div
		WSL.api.getPageIndexLiveValues(function(data) {
			var GP = 3600 / 10;
			var gaugeGPOptions = {
				title : 'AC Power',
				grid : {
					background : '#FFF'
				},
				seriesDefaults : {
					renderer : $.jqplot.MeterGaugeRenderer,
					rendererOptions : {
						min : 0,
						max : GP * 10,
						padding : 0,
						intervals : [ GP, GP * 2, GP * 3, GP * 4, GP * 5,
								GP * 6, GP * 7, GP * 8, GP * 9, GP * 10 ],
						intervalColors : [ '#F9FFFB', '#EAFFEF', '#CAFFD8',
								'#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95',
								'#4BFE78', '#0AFE47', '#01F33E' ]
					}
				}
			};
				var IP = 3600 / 10;
				var gaugeIPOptions = {
					title : 'DC Power',
					grid : {
						background : '#FFF'
					},
					seriesDefaults : {
						renderer : $.jqplot.MeterGaugeRenderer,
						rendererOptions : {
							min : 0,
							max : IP * 10,
							padding : 0,
							intervals : [ IP, IP * 2, IP * 3, IP * 4, IP * 5,
									IP * 6, IP * 7, IP * 8, IP * 9, IP * 10 ],
							intervalColors : [ '#F9FFFB', '#EAFFEF', '#CAFFD8',
									'#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95',
									'#4BFE78', '#0AFE47', '#01F33E' ]
						}
					}
				};
					var EFF = 100 / 10;
					var gaugeEFFOptions = {
						title : 'Efficiency',
						grid : {
							background : '#FFF'
						},
						seriesDefaults : {
							renderer : $.jqplot.MeterGaugeRenderer,
							rendererOptions : {
								min : 0,
								max : EFF * 10,
								padding : 0,
								intervals : [ EFF, EFF * 2, EFF * 3, EFF * 4, EFF * 5,
										EFF * 6, EFF * 7, EFF * 8, EFF * 9, EFF * 10 ],
								intervalColors : [ '#F9FFFB', '#EAFFEF', '#CAFFD8',
										'#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95',
										'#4BFE78', '#0AFE47', '#01F33E' ]
							}
						}
					};
				$.ajax({
					url : 'js/templates/liveInverters.hb',
					success : function(source) {
						var template = Handlebars.compile(source);
						var html = template({
							'data' : data
						});
						$(divId).html(html);
						var gaugeGP = $.jqplot('gaugeGP', [ [ 0.1 ] ],gaugeGPOptions);
						gaugeGP.series[0].data = [ [ 'W',data.IndexValues.sum.GP ] ];
						gaugeGP.series[0].label = data.IndexValues.sum.GP;
						document.title = '('+ data.IndexValues.sum.GP+ ' W) WebSolarLog';
						gaugeGP.replot();
						
						var gaugeIP = $.jqplot('gaugeIP', [ [ 0.1 ] ],gaugeIPOptions);
						gaugeIP.series[0].data = [ [ 'W',data.IndexValues.sum.IP ] ];
						gaugeIP.series[0].label = data.IndexValues.sum.IP;
						gaugeIP.replot();
						
						var gaugeEFF = $.jqplot('gaugeEFF', [ [ 0.1 ] ],gaugeEFFOptions);
						gaugeEFF.series[0].data = [ [ 'W',data.IndexValues.sum.EFF] ];
						gaugeEFF.series[0].label = data.IndexValues.sum.EFF+' %';
						gaugeEFF.replot();
						
					},
					dataType : 'text',
				});


		});
	},

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

	init_tabs : function(page, divId, success) {
		// initialize languages selector on the given div
		WSL.api.getTabs(page, function(data) {
			$.ajax({
				url : 'js/templates/tabs.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(html).prependTo(divId);
					$('#tabs').tabs({
					    show: function(event, ui) {
					        if (currentGraphHandler){
					        	$("#graph"+data.tabs[ui.index]["graphName"]+"Content").html('<div id="loading">loading...</div>');
					        	currentGraphHandler.destroy();
					        }
					        if (todayTimerHandler){
					            window.clearInterval(todayTimerHandler);
					        }

					        if (data.tabs[ui.index]["graphName"] == "Today" || data.tabs[ui.index]["graphName"] == "Yesterday") {
					        	WSL.createDayGraph(1, data.tabs[ui.index]["graphName"],function(handler) {currentGraphHandler = handler;$("#loading").remove();});
					        }else{
					        	WSL.createPeriodGraph(1, data.tabs[ui.index]["graphName"],1 , "graph"+data.tabs[ui.index]["graphName"]+"Content" , function(handler) {currentGraphHandler = handler;$("#loading").remove();});
					        	
					        }
				            // Refresh only the Today tab
				            if (data.tabs[ui.index]["graphName"] == "Today") {
				                todayTimerHandler = window.setInterval(function(){
				                    if (currentGraphHandler){
				                        $("#graph"+data.tabs[ui.index]["graphName"]+"Content").html('<div id="loading">loading...</div>');
				                        currentGraphHandler.destroy();
				                    }
				                    WSL.createDayGraph(1, "Today", function(handler) {currentGraphHandler = handler;$("#loading").remove();});				                    
				                }, 60000); // every minute
				               
				            }
					    }
					});
					success.call();
				},
				dataType : 'text',
			});
		});
		return true;
	},

	init_PageIndexAddContainers : function (divId,sideBar){
		$.ajax({
			url : 'js/templates/liveValues.hb',
			success : function(source) {
				var template = Handlebars.compile(source);
				var html = template({
					'data' : ''
				});
				$(divId).html(html);
			},
			dataType : 'text',
		});	
		WSL.api.getPageIndexValues(function(data) {
		$.ajax({
			url : 'js/templates/totalValues.hb',
			success : function(source) {
				var template = Handlebars.compile(source);
				var html = template({
					'data' : data
				});
				$(sideBar).html(html);
			},
			dataType : 'text',
		});
		});
	},
	init_PageTodayValues : function(todayValues,success) {
		// initialize languages selector on the given div
		WSL.api.getPageTodayValues(function(data) {
			$.ajax({
				url : 'js/templates/todayValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(todayValues).html(html);
					success.call();
				},
				dataType : 'text',
			});
			
		});
	},

	init_PageMonthValues : function(monthValues,periodList) {
		// initialize languages selector on the given div
		WSL.api.getPageMonthValues(function(data) {
			$.ajax({
				url : 'js/templates/monthValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(monthValues).html(html);
				    $(function() {
				        $( "#monthPowerAcc" ).accordion({
				            collapsible: true
				        });
				        $( "#monthEnergyAcc" ).accordion({
				            collapsible: true
				        });
				    });
				},
				dataType : 'text',
			});
			$.ajax({
				url : 'js/templates/periodList.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(periodList).html(html);
				},
				dataType : 'text',
			});

		});
	},

	init_PageYearValues : function(yearValues,periodList) {
		// initialize languages selector on the given div
		WSL.api.getPageYearValues(function(data) {
			$.ajax({
				url : 'js/templates/yearValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(yearValues).html(html);
				},
				dataType : 'text',
			});
			/*
			$.ajax({
				url : 'js/templates/periodList.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(periodList).html(html);
				},
				dataType : 'text',
			});*/
		});
	},

	createDayGraph : function(invtnum, getDay, fnFinish) {
		var graphOptions = {
			series : [
			          {label:'Cum. Power',yaxis:'yaxis',showMarker:false},
			          {label:'Avg. Power',yaxis:'y2axis'}
			          ],
			axesDefaults : {
				useSeriesColor: true, 
				tickRenderer : $.jqplot.CanvasAxisTickRenderer,

			},
			animate: true,
			legend : {
			show : true,
			location:"nw",
			 renderer: $.jqplot.EnhancedLegendRenderer,
			 rendererOptions: {
                // set to true to replot when toggling series on/off
                // set to an options object to pass in replot options.
                seriesToggle: 'normal',
                //seriesToggleReplot: {resetAxes: true}
            }
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
					label : 'Avg. Power(W)',
					min : 0,
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer
				}
			}, 
			seriesDefaults: {
		          rendererOptions: {
		              smooth: true
		          }
		      },
			highlighter : {
				tooltipContentEditor: tooltipContentEditor,
				show : true,
				yvalues:4,
				tooltipLocation:'n'
			},
			cursor : {
				show : false
			}
		};
		$.ajax({
			url : "server.php?method=getGraphPoints&type="+ getDay +"&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {
				var dataDay1 = [];
				var dataDay2 = [];
				if (result.dayData) {
    				for (line in result.dayData.data) {;
    					var object = result.dayData.data[line];
    					dataDay1.push([ object[0], object[1],object[3] ]);
    					dataDay2.push([ object[0], object[2],object[3] ]);
    				} 

    				if (dataDay1[0]) {
    				    graphOptions.axes.xaxis.min = dataDay1[0][0];
    				}
    				handle = $.jqplot('graph' + getDay + 'Content', [ dataDay1,dataDay2 ], graphOptions);
    				mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy ' + getDay.toLowerCase() + ': ' + result.dayData.valueKWHT +' W </div>').insertAfter('#graph' + getDay + ' .jqplot-grid-canvas');
    				fnFinish.call(this, handle);
				}
			}
		});
	},

	createPeriodGraph : function(invtnum, type, count, divId, fnFinish) {
		var graphDayPeriodOptions = {
				
				series : [
				          {label:'Cum. Power',yaxis:'yaxis',showMarker:false,renderer:$.jqplot.BarRenderer, pointLabels: {show: false}},
				          {label:'Avg. Power',yaxis:'y2axis', pointLabels: {show: false}}
				          ],
				seriesDefaults : {
				labelOptions:{
					formatString: '%d-%' ,fontSize: '20pt',
				},
				rendererOptions : {
					fillToZero : true,
					barWidth : 5,
				},
				showMarker : false,
		        pointLabels: {
		            show: true,
		            formatString: '%s',
		        },

			},
			axesDefaults : {
				useSeriesColor: true, 
				tickRenderer : $.jqplot.CanvasAxisTickRenderer,
				tickOptions : {
					angle : -30,
					fontSize : '10pt'
				}
			},
			legend : {
				show : true,
				 renderer: $.jqplot.EnhancedLegendRenderer,
				 rendererOptions: {
	                // set to true to replot when toggling series on/off
	                // set to an options object to pass in replot options.
	                seriesToggle: 'normal',
	                //seriesToggleReplot: {resetAxes: true}
	            }
			},
			highlighter : {
				tooltipContentEditor: tooltipPeriodContentEditor,
				show : true,
				yvalues:4,
				tooltipLocation:'n'
			},
			axes : {
				// Use a category axis on the x axis and use our custom ticks.
				xaxis : {
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
					renderer : $.jqplot.DateAxisRenderer,
					angle : -30,
					tickOptions : {
						formatString : '%d-%m'
					}
				},
				yaxis : {
					label : 'Cum. Power',
					min : 0,
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer
				},
				y2axis : {
					label : 'Avg. Power',
					min : 0,
					labelRenderer : $.jqplot.CanvasAxisLabelRenderer
				}
			}
		};

		
		$.ajax({
			url : "server.php?method=getGraphPoints&type=" + type + "&count=" + count + "&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {
				var dayData1 = [];
				var dayData2 = [];
				var i = 0;
				for (line in result.dayData.data) {
					var object = result.dayData.data[line];
					//alert(object);
					dayData1.push([ object[0], object[1], object[2]]);
					dayData2.push([ object[0], object[3], object[2]]);
					i +=1;
				}
				graphDayPeriodOptions.axes.xaxis.min = result.dayData.data[0][2];
				graphDayPeriodOptions.axes.xaxis.max = result.dayData.data[i-1][2];
				var plot = $.jqplot(divId, [ dayData1,dayData2 ], graphDayPeriodOptions).destroy();
				plot = null; 
				var plot = $.jqplot(divId, [ dayData1,dayData2 ], graphDayPeriodOptions);
			}
		});
	},
	
	init_production : function(invtnum,divId){
		WSL.createProductionGraph(invtnum, divId);
	},
	
	createProductionGraph : function(invtnum, divId) {
		var graphOptions = {
			series : [
			          {label:'Expected(kWh)',yaxis:'yaxis',renderer:$.jqplot.BarRenderer, pointLabels: {show: false}},
			          {label:'Harvested(kWh)',yaxis:'y2axis',renderer:$.jqplot.BarRenderer, pointLabels: {show: false}},
			          {label:'Cum. Expected(kWh)',yaxis:'y3axis',renderer:$.jqplot.LineRenderer, pointLabels: {show: false}},
			          {label:'Cum. Harvested(kWh)',yaxis:'y4axis',renderer:$.jqplot.LineRenderer, pointLabels: {show: false}}
		    ],
			axesDefaults : {useSeriesColor: true, },
			legend : {
				show : true,
				location:"nw",
				 renderer: $.jqplot.EnhancedLegendRenderer,
				 rendererOptions: {
	                // set to true to replot when toggling series on/off
	                // set to an options object to pass in replot options.
	                seriesToggle: 'normal',
	                //seriesToggleReplot: {resetAxes: true}
	            }
			}, 
		    seriesDefaults:{rendererOptions: {barMargin: 40,barWidth:10}},
			axes : {
				xaxis : {labelRenderer : $.jqplot.CanvasAxisLabelRenderer,renderer : $.jqplot.DateAxisRenderer,tickInterval:'1 month',tickOptions : {angle : -50}},
				yaxis : {label : 'Expected(kWh)',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y2axis : {label : 'Harvested(kWh)',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y3axis : {label : 'Cum. Expected(kWh)',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y4axis : {label : 'Cum. Harvested(kWh)',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer}
			},
			highlighter : {
				tooltipContentEditor: tooltipCompareContentEditor,
				show : true,
				yvalues:4,
				tooltipLocation:'n'
			},
			cursor : {show : false}
		};
		
		$.ajax({
			url : "server.php?method=getProductionGraph&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {

				var dataDay1 = [];
				var dataDay2 = [];
				var dataDay3 = [];
				var dataDay4 = [];
				if (result.dayData) {
					
					$("#main-middle").prepend('<div id="ProductionGraph"></div>');
					
					for (line in result.dayData.data) {
						var object = result.dayData.data[line];
						dataDay1.push([  object[0], object[1], object[2] ]);
						dataDay2.push([  object[0], object[3], object[2] ]);
						dataDay3.push([  object[0], object[5], object[2] ]);
						dataDay4.push([  object[0], object[6], object[2] ]);
					}
					
    				if (dataDay1[0][0]) {
    				    graphOptions.axes.xaxis.min = dataDay1[0][0];
    				}
    				handle = $.jqplot("ProductionGraph", [ dataDay1 , dataDay2, dataDay3, dataDay4], graphOptions);
    				//mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy ' + getDay.toLowerCase() + ': ' + result.dayData.valueKWHT + ' kWh</div>').insertAfter('#' + divId + ' .jqplot-grid-canvas');
    				//fnFinish.call(this, handle);
				}
			}
		});
	}
};


//api class
WSL.api.getHistoryValues = function(success) {
	$.getJSON("server.php", {
		method : 'getHistoryValues',
	}, success);
};

WSL.api.programdayfeed = function(invtnum, success) {
	$.getJSON("programs/programdayfeed.php", {
		invtnum : invtnum
	}, success);
};

WSL.api.getTabs = function(page, success) {
	$.getJSON("server.php", {
		method : 'getTabs',
		'page' : page,
	}, success);
};

WSL.api.getPageIndexValues = function(success) {
	$.getJSON("server.php", {
		method : 'getPageIndexValues',
	}, success);
};

WSL.api.getPageIndexLiveValues = function(success) {
	$.getJSON("server.php", {
		method : 'getPageIndexLiveValues',
	}, success);
};



WSL.api.getPageTodayValues = function(success) {
	$.getJSON("server.php", {
		method : 'getPageTodayValues',
	}, success);
};


WSL.api.getPageMonthValues = function(success) {
	$.getJSON("server.php", {
		method : 'getPageMonthValues',
	}, success);
};


WSL.api.getPageYearValues = function(success) {
	$.getJSON("server.php", {
		method : 'getPageYearValues',
	}, success);
};


WSL.api.getEvents = function(invtnum, success) {
	$.getJSON("server.php", {
		method : 'getEvents',
		'invtnum' : invtnum,
	}, success);
};

WSL.api.getInverters = function(success) {
    $.getJSON("server.php", {
        method : 'getInverters'
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