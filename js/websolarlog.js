// calculate the JS parse time //
$.ajaxSetup({
	cache : false
});

function ajaxReady(){
	$('#reqLoading').hide();
	$( '.tooltip' ).tooltip({});
}

function ajaxStart(){
	$('#reqLoading').show();
	$('.ui-tooltip').remove();
}


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
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Energy:"+ plot.series[0].data[pointIndex][1]+ " kWh<br>"+bold[1];
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Cum.: "+ plot.series[1].data[pointIndex][1]+ " kWh<br>"+bold[1];
	returned += "Date:"+ plot.series[1].data[pointIndex][2]+"";
	return returned;
}

function tooltipCompareContentEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = ""; 
	var diff = plot.series[0].data[pointIndex][1] - plot.series[1].data[pointIndex][1];
	var yearDiff = plot.series[2].data[pointIndex][1] - plot.series[3].data[pointIndex][1];
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Harvested:"+ plot.series[0].data[pointIndex][1]+" kWh<br>"+bold[1];
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Expected: "+ plot.series[1].data[pointIndex][1] +" kWh<br>"+bold[1];
	returned += "This month: "+ diff +" kWh<br>";
	( seriesIndex == 2 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum. Harvested:"+plot.series[2].data[pointIndex][1]+" kWh<br>"+bold[1];
	( seriesIndex == 3 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum. Expected: "+plot.series[3].data[pointIndex][1]+" kWh<br>"+bold[1];
	
	returned += "This year: "+ yearDiff +" kWh<br>";
	return returned;
}

function tooltipCompareEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = "";
	
	
	if($.isArray(plot.series[0].data[pointIndex])){
		( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+""+ plot.series[1].label +": "+ plot.series[1].data[pointIndex][1]+" kWh<br>"+bold[1];
		( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"date: "+ plot.series[1].data[pointIndex][2]+" <br>"+bold[1];
	}else{
		returned += "<br>Expected: No data available for "+ plot.series[1].data[pointIndex][2]+"<br>";
	}
	
	if($.isArray(plot.series[1].data[pointIndex])){
		( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+""+ plot.series[0].label +": "+ plot.series[0].data[pointIndex][1] +" kWh<br>"+bold[1];
		( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"date: "+ plot.series[0].data[pointIndex][2]+" <br>"+bold[1];
	
	}else{
		returned += "<br>Harvested: No data available for "+plot.series[0].data[pointIndex][2];
	}

	return returned;
}

function tooltipDetailsContentEditor(str, seriesIndex, pointIndex, plot,series	){
	var returned = ""; 
	return returned;	
}


function handleGraphs(request,invtnum){

	invtnum = $('#pickerInv').val();

	
	//console.log('invtnum:'+invtnum);
	// get activated Tab;
	var tabSelected = $('#tabs').tabs('option', 'selected');
	// set type to Today
	var tab='Today';
	(tabSelected == 0)? tab= 'Today' : tab=tab;
	(tabSelected == 1)? tab= 'Yesterday' : tab=tab;
	(tabSelected == 2)? tab= 'Month' : tab=tab;
	(tabSelected == 3)? tab= 'Year'	: tab=tab;
	var period = tab;
	
	var date = $('#datepicker').val();
	
	//console.log('data:'+date);
	if (currentGraphHandler){
    	$("#graph"+tab+"Content").html('<div id="loading">loading...</div>');
    	currentGraphHandler.destroy();
    }
    if (todayTimerHandler){
        window.clearInterval(todayTimerHandler);
    }
    
    if (request=='picker'){
    	$('#lastCall').val('picker');
    	period= $('#pickerPeriod').val();
    	if (period == "Today") {
    		//console.log("period1:"+tab);
    		WSL.createDayGraph(invtnum, period, tab,date ,function(handler) {currentGraphHandler = handler;$("#loading").remove();});
    	}else{
    		//console.log("period2:"+tab);
    		WSL.createPeriodGraph(invtnum, period,1 ,date, "graph"+tab+"Content" , function(handler) {currentGraphHandler = handler;$("#loading").remove();});
    	}    
    }else{
    	$('#lastCall').val('normal');
    	if (tab == "Today" || tab == "Yesterday") {
    		WSL.createDayGraph(invtnum, period, tab,date ,function(handler) {currentGraphHandler = handler;$("#loading").remove();});
    	}else{
    		WSL.createPeriodGraph(invtnum, period,1 , date,"graph"+tab+"Content" , function(handler) {currentGraphHandler = handler;$("#loading").remove();});
    	}
    }
    // Refresh only the Today tab
    if (tab == "Today" && $('#lastCall').val() == 'normal') {
        todayTimerHandler = window.setInterval(function(){
            if (currentGraphHandler){
                $("#graph"+tab+"Content").html('<div id="loading">loading...</div>');
                currentGraphHandler.destroy();
            }
            WSL.createDayGraph(invtnum, "Today",tab, date ,function(handler) {currentGraphHandler = handler;$("#loading").remove();});				                    
        }, 60000); // every minute
       
    }
    
    
    
}


function populateTabs(){
	$.getJSON('server.php?method=getPeriodFilter&type=all', function(data) {
		$.ajax({
			url : 'js/templates/datePeriodFilter.hb',
			success : function(source) {
				var template = Handlebars.compile(source);
			
				var html = template({
				'data' : data,
				'lang' : data.lang
				});
				$('#pickerFilter').html(html);
				
				var invtnum = $('#pickerInv').val();
				
				$(".mainTabContainer").hover(function() {
		    		$("#pickerFilterDiv").hide();
		    		$( "#datepicker" ).datepicker("hide");
				}, function() {
					$("#pickerFilterDiv").show();
				});

				$('#pickerPeriod').live("change",
						function(){
							handleGraphs('picker',invtnum);
						}
				);
				$('#next').unbind('click');
				$('#previous').unbind('click');
				$('#pickerPeriod').unbind('click');
				
				
				$('#next').click(function () {
				    var $picker = $("#datepicker");
				    var date=new Date($picker.datepicker('getDate'));
				    var splitDate = $('#datepicker').val().split('-');
				    if($('#pickerPeriod').val()=='Today'){
				    	date.setDate(date.getDate()+1);	
				    }else if($('#pickerPeriod').val()=='Week'){
				    	date.setDate(date.getDate()+7);
				    }else if($('#pickerPeriod').val()=='Month'){
				    	var value = splitDate[1];
				    	date.setMonth(value);
				    }else if($('#pickerPeriod').val()=='Year'){
				    	var value = parseInt(splitDate[2])+1;
				    	date.setFullYear(value);
				    }
				    $picker.datepicker('setDate', date);
				    handleGraphs('picker',invtnum);
				});
				
				$('#previous').click(function () {
				    var $picker = $("#datepicker");
				    var date=new Date($picker.datepicker('getDate'));
				    var splitDate = $('#datepicker').val().split('-');
				    if($('#pickerPeriod').val()=='Today'){
				    	date.setDate(date.getDate()-1);	
				    }else if($('#pickerPeriod').val()=='Week'){
				    	date.setDate(date.getDate()-7);
				    }else if($('#pickerPeriod').val()=='Month'){
				    	var value = splitDate[1]-2;
				    	date.setMonth(value);
				    }else if($('#pickerPeriod').val()=='Year'){
				    	var value = parseInt(splitDate[2])-1;
				    	date.setFullYear(value);
				    }
				    $picker.datepicker('setDate', date);
				    handleGraphs('picker',invtnum);
				});
				invtnum = $('#pickerInv').val();
		    	handleGraphs('standard',invtnum);
			},
			dataType : 'text'
		});
    });
}


// WSL class
var WSL = {
	api : {},
	init_nextRelease : function(divId) {
		$(divId).html("<br/><br/><H1>WSL::NextRelease();</h1>");
	},
	
	init_PageTodayHistoryValues : function(divId) {
		ajaxStart();
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
			        ajaxReady();
				},
				dataType : 'text'
			});
		});
	},
	
	init_PageIndexLiveValues : function(divId) {
		// initialize languages selector on the given div
		ajaxStart();
		WSL.api.getPageIndexLiveValues(function(data) {
				$.ajax({
					url : 'js/templates/liveInverters.hb',
					success : function(source) {
						var template = Handlebars.compile(source);
						var html = template({
							'data' : data,
							'lang':data.lang
						});
						$(divId).html(html);
						
						var GP = 3600 / 10;
						var gaugeGPOptions = {
							title : data.lang.ACPower,
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
								title : data.lang.DCPower,
								grid : {
									background : '#FFF'
								},
								seriesDefaults : {
									renderer : $.jqplot.MeterGaugeRenderer,
									rendererOptions : {
										min : 0,
										max : IP * 10,
										padding : 0,
										intervals : [ IP, IP * 2, IP * 3, IP * 4, IP * 5, IP * 6, IP * 7, IP * 8, IP * 9, IP * 10 ],
										intervalColors : [ '#F9FFFB', '#EAFFEF', '#CAFFD8','#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95','#4BFE78', '#0AFE47', '#01F33E' ]
									}
								}
							};
							var EFF = 100 / 10;
							var gaugeEFFOptions = {
								title : data.lang.Efficiency,
								grid : {
									background : '#FFF'
								},
								seriesDefaults : {
									renderer : $.jqplot.MeterGaugeRenderer,
									rendererOptions : {
										min : 0,
										max : EFF * 10,
										padding : 0,
										intervals : [ EFF, EFF * 2, EFF * 3, EFF * 4, EFF * 5,EFF * 6, EFF * 7, EFF * 8, EFF * 9, EFF * 10 ],
										intervalColors : [ '#F9FFFB', '#EAFFEF', '#CAFFD8','#B5FFC8', '#A3FEBA', '#8BFEA8', '#72FE95','#4BFE78', '#0AFE47', '#01F33E' ]
									}
								}
							};
						
						//////////////////
						////////////////////////////////////////
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
						ajaxReady();
					},
					dataType : 'text',
				});


		});
	},

	init_misc : function(invtnum, divId) {
		// Retrieve the error events
		//ajaxStart();
		WSL.api.getEvents(invtnum, function(data) {
			$.ajax({
				url : 'js/templates/events.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'lang' : data.lang,
						'data' : data
					});
					
					$(divId).html(html);
				},
				dataType : 'text'
			});
		});
	},
	
	init_plantInfo : function(invtnum, divId) {
		ajaxStart();
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
						ajaxReady();
					},
					dataType : 'text'
				});
			} else {
				alert(data.plantInfo.message);
			}
		});
	},
	
	init_menu : function(divId) {
		ajaxStart();
		WSL.api.getMenu(function(data) {
			$.ajax({
				url : 'js/templates/menu.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data
					});
					$(divId).html(html);
					ajaxReady();
				},
				dataType : 'text'
			});
		});
	},
	init_languages : function(divId) {
		ajaxStart();
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
					ajaxReady();
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
						'data' : data,
						'lang' : data.lang
					});
					$(html).prependTo(divId);
					
					$('#tabs').tabs({
					    show: function(event, ui) {
					    	//invtnum = $('#pickerInv').val();
					    	//handleGraphs('standard',invtnum);
					    	
					    	
					    	// populate the tabs:
					    	populateTabs();
					    	
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
		ajaxStart();
		WSL.api.getPageIndexValues(function(data) {
			ajaxStart();
			$.ajax({
				url : 'js/templates/liveValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : '',
						'lang' : data.lang
					});
					$(divId).html(html);
					ajaxReady();
				},
				dataType : 'text',
			});	
			if (typeof data.result != "undefined" && data.result != 'true') {
				$.pnotify({
                    title: 'Error',
                    text: data.message,
                    type: 'error'
                });
			}
			$.ajax({
				url : 'js/templates/totalValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data.IndexValues,
						'lang' : data.lang
					});
					$(sideBar).html(html);
					ajaxReady();
				},
				dataType : 'text',
			});
		});
	},
	
	
	init_PageTodayValues : function(todayValues,success) {
		ajaxStart();
		// initialize languages selector on the given div
		WSL.api.getPageTodayValues(function(data) {
			$.ajax({
				url : 'js/templates/todayValues.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data.dayData.data,
						'lang' : data.lang
					});
					$(todayValues).html(html);
					success.call();
					ajaxReady();
				},
				dataType : 'text',
			});
			
		});
	},

	init_PageMonthValues : function(monthValues,periodList) {
		ajaxStart();
		
		
		if($('#datePickerPeriod').val()){
			var completeDate = "01-"+$('#datePickerPeriod').val();
		}else{
			var completeDate = $('#datePickerPeriod').val();
		}
		var pickerDate = $('#datePickerPeriod').val();
		
		// initialize languages selector on the given div
		WSL.api.getPageMonthValues(completeDate,function(data) {
			$.ajax({
				url : 'js/templates/monthValues.hb',
				success : function(source) {
					//console.log('start compile');
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data,
						'lang' : data.lang
					});
					//console.log('add html');
					$(monthValues).html(html);

				    $(function() {
				    	//console.log('add accordion');
				        $( ".accordion" ).accordion({collapsible: true});
				        $( ".accordion" ).accordion({collapsible: true});
				    });
				    
				    $.ajax({
						url : 'js/templates/pageDateFilter.hb',
						success : function(source) {
							var template = Handlebars.compile(source);
							var html = template({
								'data' : data,
								'lang' : data.lang
							});
							$('#pageMonthDateFilter').html(html);

							if(!pickerDate){
								//console.log('standaard datum');
								$("#datePickerPeriod").datepicker({
							        dateFormat: 'mm-yy',
							        changeMonth: true,
							        changeYear: true,
							        //showButtonPanel: true,

							        onClose: function(dateText, inst) {
							            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
							            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
							            //new Date(year, month, day) // W3schools
							            $(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
										WSL.init_PageMonthValues("#columns","#periodList"); // Initial load fast
										
										//console.log('datePickerMonth');
							        }
								});
					            //new Date(year, month, day) // W3schools
								$("#datePickerPeriod").datepicker('setDate', new Date());
							}else{
								//console.log('oude datum');
								$("#datePickerPeriod").datepicker({
							        dateFormat: 'mm-yy',
							        changeMonth: true,
							        changeYear: true,
							        //showButtonPanel: true,

							        onClose: function(dateText, inst) {
							            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
							            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
							            
							            //new Date(year, month, day) // W3schools
							            $(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
										WSL.init_PageMonthValues("#columns","#periodList"); // Initial load fast
										//console.log('datePickerMonth');
							        }
								});
								
								//console.log('setPicker');
								//console.log(pickerDate);
								pickerDate = pickerDate.split('-'); // 01-2012 (month-year)
								pickerDate[0] = pickerDate[0]-1;
					            //new Date(year, month, day) // W3schools
								$("#datePickerPeriod").datepicker('setDate', new Date(pickerDate[1],pickerDate[0],1));
								//console.log('dateSet');
							}
							 $("#datePickerPeriod").focus(function () {
								 $(".ui-datepicker-calendar").hide();
								 
							        
							        $("#ui-datepicker-div").position({
							            my: "center top",
							            at: "center bottom",
							            of: $(this)
							        });
							    });
						},
						dataType : 'text',
					});
					
				    
				    
				},
				dataType : 'text',
			});
			
			
		});
		ajaxReady();
	},

	init_PageYearValues : function(yearValues,periodList) {
		ajaxStart();
		
		if($('#datePickerPeriod').val()){
			var completeDate = "01-01-"+$('#datePickerPeriod').val();
		}else{
			var completeDate = $('#datePickerPeriod').val();
		}
		var pickerDate = $('#datePickerPeriod').val();
		//console.log("completeDate:"+completeDate);
		//console.log("pickerDate:"+pickerDate);
		// initialize languages selector on the given div
		WSL.api.getPageYearValues(completeDate,function(data) {
			$.ajax({
				url : 'js/templates/yearValues.hb',
				success : function(source) {
					//console.log('start compile');
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data,
						'lang' : data.lang
					});
					//console.log('add html');
					$(yearValues).html(html);

				    $(function() {
				    	//console.log('add accordion');
				        $( ".accordion" ).accordion({collapsible: true});
				        $( ".accordion" ).accordion({collapsible: true});
				    });
				    $.ajax({
						url : 'js/templates/pageDateFilter.hb',
						success : function(source) {
							var template = Handlebars.compile(source);
							var html = template({
								'data' : data,
								'lang' : data.lang
							});
							$('#pageYearDateFilter').html(html);

							if(!pickerDate){
								//console.log('standaard datum');
								$("#datePickerPeriod").datepicker({
							        dateFormat: 'yy',
							        changeMonth: false,
							        changeYear: true,
							        //showButtonPanel: true,

							        onClose: function(dateText, inst) {
							            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
							            //new Date(year, month, day) // W3schools
							            $(this).val($.datepicker.formatDate('yy', new Date(year, 1, 1)));
										WSL.init_PageYearValues("#columns","#periodList"); // Initial load fast
										//console.log('datePickerYear');
							        }
								});
								$("#datePickerPeriod").datepicker('setDate', new Date());
							}else{
								//console.log('oude datum');
								$("#datePickerPeriod").datepicker({
							        dateFormat: 'yy',
							        changeMonth: false,
							        changeYear: true,
							        //showButtonPanel: true,

							        onClose: function(dateText, inst) {
							            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
							            //new Date(year, month, day) // W3schools
							            $(this).val($.datepicker.formatDate('yy', new Date(year, 1, 1)));
										WSL.init_PageYearValues("#columns","#periodList"); // Initial load fast
										//console.log('datePickerYear');
							        }
								});
								
								//console.log('setPicker');
								//console.log(pickerDate);
					            //new Date(year, month, day) // W3schools
								$("#datePickerPeriod").datepicker('setDate', new Date(pickerDate,1,1));
								//onsole.log('dateSet');
							}
							 $("#datePickerPeriod").focus(function () {
							        $(".ui-datepicker-calendar").hide();
							        $(".ui-datepicker-month").hide();
							        $(".ui-icon-circle-triangle-w").hide();
							        $(".ui-icon-circle-triangle-e").hide();
							        $("#ui-datepicker-div").position({
							            my: "center top",
							            at: "center bottom",
							            of: $(this)
							        });
							    });
						},
						dataType : 'text',
					});
				    
				},
				dataType : 'text',
			});
			
			
			
		});
		ajaxReady();
	},

	
	createDayGraph : function(invtnum, getDay, tab,date ,fnFinish) {
		ajaxStart();
		
		$.ajax({
			url : "server.php?method=getGraphPoints&type="+ getDay +"&date="+ date +"&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {
				
				////////////////////////////////////////
				/////////////
				
				var graphOptions = {
						series : [ {label:result.lang.cumPowerW,yaxis:'yaxis',showMarker:false},{label:result.lang.avgPowerW,yaxis:'y2axis'}],
						axesDefaults : {useSeriesColor: true, tickRenderer : $.jqplot.CanvasAxisTickRenderer,},
						animate: true,
						legend : {
						show : true,
						location:"nw",
						renderer: $.jqplot.EnhancedLegendRenderer,
							 rendererOptions: {seriesToggle: 'normal',}
						}, 
						
						axes : {
							xaxis : {
								label : '',
								labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
								renderer : $.jqplot.DateAxisRenderer,
								tickInterval : '3600', // 1 hour
								tickOptions : {angle : -30,formatString : '%H:%M'}
							},
							yaxis : {
								label : result.lang.cumPowerW,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer
							},
							y2axis : {
								label : result.lang.avgPowerW,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer
							}
						}, 
						seriesDefaults: {
					          rendererOptions: {smooth: true}
					      },
						highlighter : {
							tooltipContentEditor: tooltipContentEditor,show : true,yvalues:4,tooltipLocation:'n'
						},
						cursor : {
							show : false
						}
					};
				
				/////////////
				////////////////////////////////////////
				
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
    				handle = $.jqplot('graph' + tab + 'Content', [ dataDay1,dataDay2 ], graphOptions);
    				mytitle = 
    					$('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">'+
    							result.lang.totalEnergy+': ' + result.dayData.valueKWHT +
    							' '+result.dayData.KWHTUnit+' ('+result.dayData.KWHKWP+' kWh/kWp)</div>').insertAfter('#graph' + 
    									getDay + ' .jqplot-grid-canvas');
    				fnFinish.call(this, handle);
    				ajaxReady();
				}
			}
		});
	},

	createPeriodGraph : function(invtnum, type, count, date,divId, fnFinish) {
		ajaxStart();

		
		$.ajax({
			url : "server.php?method=getGraphPoints&type=" + type + "&count=" + count  +"&date="+ date + "&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {
				var dayData1 = [];
				var dayData2 = [];
				var i = 0;
				for (line in result.dayData.data) {
					var object = result.dayData.data[line];
					// alert(object);
					dayData1.push([ object[0], object[1], object[2]]);
					dayData2.push([ object[0], object[3], object[2]]);
					i +=1;
				}

				var graphDayPeriodOptions = {
						
						series : [
						          {label:result.lang.harvested,yaxis:'yaxis',showMarker:false,renderer:$.jqplot.BarRenderer, pointLabels: {show: false}},
						          {label:result.lang.cumulative,yaxis:'y2axis', pointLabels: {show: false}}
						          ],
						seriesDefaults : {
						labelOptions:{formatString: '%d-%' ,fontSize: '20pt'},
						rendererOptions : {fillToZero : true,barWidth : 5},
						showMarker : false,
				        pointLabels: {show: true,formatString: '%s'},
					},
					axesDefaults : {
						useSeriesColor: true, 
						tickRenderer : $.jqplot.CanvasAxisTickRenderer,
						tickOptions : {angle : -30,fontSize : '10pt'}
					},
					legend : {
						show : true,
						location:"nw",
						 renderer: $.jqplot.EnhancedLegendRenderer,
						 rendererOptions: {
			                // set to true to replot when toggling series on/off
			                // set to an options object to pass in replot options.
			                seriesToggle: 'normal',
			                // seriesToggleReplot: {resetAxes: true}
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
							label : result.lang.harvested,
							min : 0,
							labelRenderer : $.jqplot.CanvasAxisLabelRenderer
						},
						y2axis : {
							label : result.lang.cumulative,
							min : 0,
							labelRenderer : $.jqplot.CanvasAxisLabelRenderer
						}
					}
				};

				
				
				graphDayPeriodOptions.axes.xaxis.min = result.dayData.data[0][2];
				graphDayPeriodOptions.axes.xaxis.max = result.dayData.data[i-1][2];
				var plot = $.jqplot(divId, [ dayData1,dayData2 ], graphDayPeriodOptions).destroy();
				plot = null; 
				var plot = $.jqplot(divId, [ dayData1,dayData2 ], graphDayPeriodOptions);
				ajaxReady();
			}
		});
	},
	
	init_production : function(invtnum,divId){
		WSL.createProductionGraph(invtnum, divId);
	},
	
	createProductionGraph : function(invtnum, divId) {
		ajaxStart();
		var graphOptions = {
			series : [
			          {label:'Harvested(kWh)',yaxis:'yaxis',renderer:$.jqplot.BarRenderer, pointLabels: {show: false}},
			          {label:'Expected(kWh)',yaxis:'y2axis',renderer:$.jqplot.BarRenderer, pointLabels: {show: false}},
			          {label:'Cum. Expected(kWh)',yaxis:'y3axis',renderer:$.jqplot.LineRenderer, pointLabels: {show: false}},
			          {label:'Cum. Harvested(kWh)',yaxis:'y4axis',renderer:$.jqplot.LineRenderer, pointLabels: {show: false}},
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
	                // seriesToggleReplot: {resetAxes: true}
	            }
			}, 
		    seriesDefaults:{rendererOptions: {barMargin: 40,barWidth:10}},
			axes : {
				xaxis : {labelRenderer : $.jqplot.CanvasAxisLabelRenderer,renderer : $.jqplot.DateAxisRenderer,tickInterval:'1 month',tickOptions : {angle : -50}},
				yaxis : {label : 'Harvested(kWh)',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y2axis : {label : 'Expected(kWh)',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y3axis : {label : 'Cum. Expected(kWh)',min : 0,max: 3000,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y4axis : {label : 'Cum. Harvested(kWh)',min : 0,max: 3000,labelRenderer : $.jqplot.CanvasAxisLabelRenderer},
				y5axis : {label : '',min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,show:false},
			},
			highlighter : {
				tooltipContentEditor: tooltipCompareContentEditor,
				show : true,
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
						dataDay3.push([  object[0], object[6], object[2] ]);
						dataDay4.push([  object[0], object[5], object[2] ]);
					}
					
    				if (dataDay1[0][0]) {
    				    graphOptions.axes.xaxis.min = dataDay1[0][0];
    				}
    				var harvested = [];
    				var expected = [];
    				for (var i=0; i<dataDay1.length; i++) {
    				    // Iterates over numeric indexes from 0 to 5, as
						// everyone expects
    					harvested.push(dataDay1[i][1]);
    				}
    				var maxHarvested = Math.max.apply(Math, harvested);

    				for (var i=0; i<dataDay2.length; i++) {
    				    // Iterates over numeric indexes from 0 to 5, as
						// everyone expects
    					expected[i] = dataDay2[i][1];
    				}
    				var maxExpected = Math.max.apply(Math, expected);

    				maxAxesValue = Math.max(Math.round(maxHarvested/100)*100,Math.round(maxExpected/100)*100); 
    				var axesMargin = Math.round(maxAxesValue/100)*10;
    				graphOptions.axes.yaxis.max = maxAxesValue+axesMargin;
    				graphOptions.axes.y2axis.max = maxAxesValue+axesMargin;

    				maxAxesValue = Math.max(Math.round(dataDay3[11][1]/100)*100,Math.round(dataDay4[11][1]/100)*100);
    				var axesMargin = Math.round(maxAxesValue/100)*10;
    				graphOptions.axes.y3axis.max = maxAxesValue+axesMargin;
    				graphOptions.axes.y4axis.max = maxAxesValue+axesMargin;
    				$("#ProductionGraph").height(450);
    				handle = $.jqplot("ProductionGraph", [ dataDay1 , dataDay2, dataDay3, dataDay4], graphOptions);

    				ajaxReady();
				}
			}
		});
	},

	init_details : function(divId){
		
		$("#main-middle").prepend('<div id="datePeriodFilter"></div><div id="detailsSwitches"></div><div id="detailsGraph"></div>');

		$.getJSON('server.php?method=getDetailsSwitches', function(data) {
			$.ajax({
				url : 'js/templates/detailsSwitches.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : '',
						'lang' :data.lang
					});
					$('#detailsSwitches').html(html);
				},
				dataType : 'text',
			});
		});
		
	    $.getJSON('server.php?method=getPeriodFilter&type=today', function(data) {
			$.ajax({
				url : 'js/templates/datePeriodFilter.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data,
						'lang' : data.lang
					});
					$('#datePeriodFilter').html(html);
					
					var invtnum = $('#pickerInv').val();
					//console.log('invtnum:'+invtnum);
					
					// get the details graph....
					WSL.createDetailsGraph(invtnum, divId);
					
					$(".mainTabContainer").hover(function() {
			    		$("#pickerFilterDiv").hide();
			    		$( "#datepicker" ).datepicker("hide");
					}, function() {
						$("#pickerFilterDiv").show();
					});

					$('#pickerPeriod').live("change",
							function(){
						WSL.createDetailsGraph(invtnum, divId);
							}
					);
					
					
					$('#datepicker').live("change",
							function(){
						WSL.createDetailsGraph(invtnum, divId);
							}
					);
					
					$('#pickerInv').live("change",
							function(){
						WSL.createDetailsGraph(invtnum, divId);
							}
					);
					
					
					
					$('#next').unbind('click');
					$('#previous').unbind('click');
					$('#pickerPeriod').unbind('click');
					
					
					$('#next').click(function () {
					    var $picker = $("#datepicker");
					    var date=new Date($picker.datepicker('getDate'));
					    var splitDate = $('#datepicker').val().split('-');
					    if($('#pickerPeriod').val()=='Today'){
					    	date.setDate(date.getDate()+1);	
					    }else if($('#pickerPeriod').val()=='Week'){
					    	date.setDate(date.getDate()+7);
					    }else if($('#pickerPeriod').val()=='Month'){
					    	var value = splitDate[1];
					    	date.setMonth(value);
					    }else if($('#pickerPeriod').val()=='Year'){
					    	var value = parseInt(splitDate[2])+1;
					    	date.setFullYear(value);
					    }
					    $picker.datepicker('setDate', date);
						var invtnum = $('#pickerInv').val();
					    WSL.createDetailsGraph(invtnum, divId);
					});
					
					$('#previous').click(function () {
					    var $picker = $("#datepicker");
					    var date=new Date($picker.datepicker('getDate'));
					    var splitDate = $('#datepicker').val().split('-');
					    if($('#pickerPeriod').val()=='Today'){
					    	date.setDate(date.getDate()-1);	
					    }else if($('#pickerPeriod').val()=='Week'){
					    	date.setDate(date.getDate()-7);
					    }else if($('#pickerPeriod').val()=='Month'){
					    	var value = splitDate[1]-2;
					    	date.setMonth(value);
					    }else if($('#pickerPeriod').val()=='Year'){
					    	var value = parseInt(splitDate[2])-1;
					    	date.setFullYear(value);
					    }
					    $picker.datepicker('setDate', date);
						var invtnum = $('#pickerInv').val();
					    WSL.createDetailsGraph(invtnum, divId);
					});
							
				},
				dataType : 'text'
			});
			
	    });
	    
	},

	createDetailsGraph : function(invtnum, divId) {
		//console.log(invtnum);
		var date = $('#datepicker').val();
		$.ajax({
			url : "server.php?method=getDetailsGraph&invtnum=" + invtnum+"&date="+date,method : 'GET',dataType : 'json',
			success : function(result) {
				if (result.dayData.data) {
					var seriesLabels= [];
					var seriesData = [];
					var switches = [];
					for (line in result.dayData.data) {
						var json = [];
						for (values in result.dayData.data[line]) {
							json.push([result.dayData.data[line][values][0],result.dayData.data[line][values][1]]);
						}
						seriesLabels.push(line);
						seriesData.push(json);
					}
					var lang = result.lang;
					var graphOptions = {
							series:[
				          {yaxis:'yaxis'},// 0
				          {yaxis:'y2axis'},// 1
				          {yaxis:'y3axis'},// 2
				          {yaxis:'y4axis'},// 3
				          {yaxis:'yaxis'},// 4
				          {yaxis:'y2axis'},// 5
				          {yaxis:'y3axis'},// 6
				          {yaxis:'y5axis'},// 7
				          {yaxis:'yaxis'},// 8
				          {yaxis:'y2axis'},// 9
				          {yaxis:'y3axis'},// 10
				          {yaxis:'y5axis'},// 11
				          {yaxis:'y7axis'},// 12
				          {yaxis:'y6axis'},// 13
				          {yaxis:'y6axis'}// 13
				          ],
							axesDefaults : {useSeriesColor: true },legend : {show: true, location: 's', placement: 'outsideGrid',renderer: $.jqplot.EnhancedLegendRenderer,rendererOptions: {seriesToggle: 'normal',numberRows: 3,// seriesToggleReplot:
																																																									// {resetAxes:["yaxis"]}
							}}, 
							seriesDefaults:{
								tickOptions : {
									formatString: '%d'
								},
								rendererOptions: {barMargin: 40,barWidth:10},pointLabels: {show: false},},
							axes : {xaxis : {label : '',labelRenderer : $.jqplot.CanvasAxisLabelRenderer,renderer : $.jqplot.DateAxisRenderer,tickInterval : '3600', /*1 hour*/ 
								tickOptions : {angle : -30,formatString : '%H:%M'}},
								yaxis : {label:lang.P,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}},
								y2axis : {label:lang.V,min : 100,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}},
								y3axis : {label:lang.A,min : 0,max:20,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}},
								y4axis : {label:lang.F,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}},
								y5axis : {label:lang.R,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}},
								y6axis : {label:lang.T,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}},
								y7axis : {label:lang.E,min : 0,labelRenderer : $.jqplot.CanvasAxisLabelRenderer,autoscale:true,tickOptions:{formatString:'%.0f'}}
							},
							highlighter : {tooltipContentEditor: tooltipDetailsContentEditor,show : true}
					};


					var maxP = result.dayData.max.P;
					graphOptions.axes.yaxis.max = maxP+((maxP/100)*10);
					graphOptions.axes.y7axis.max = maxP+((maxP/100)*10);
					
					var maxV = result.dayData.max.V;
					graphOptions.axes.y2axis.max = maxV+((maxV/100)*10);
					
					var maxA = result.dayData.max.A;
					graphOptions.axes.y3axis.max = maxA+((maxA/100)*10);
					
					var maxFRQ = result.dayData.max.FRQ;
					graphOptions.axes.y4axis.max = maxFRQ+((maxFRQ/100)*10);
					
					var maxRatio = result.dayData.max.Ratio;
					(!maxRatio)? maxRatio = 10:maxRatio = maxRatio;
					graphOptions.axes.y5axis.max = maxRatio+((maxRatio/100)*10);
					
					var maxT = result.dayData.max.T;
					
					graphOptions.axes.y6axis.max = maxT+((maxT/100)*10);
					
					var maxEFF = result.dayData.max.EFF;
					(!maxEFF)? maxEFF = 10:maxEFF = maxEFF;
					graphOptions.axes.y7axis.max = maxEFF+((maxEFF/100)*10);
					
					switches = result.dayData.switches;

					$("#detailsGraph").height(450);

					graphOptions.axes.xaxis.min = seriesData[0][0][0];
    				graphOptions.legend.labels = result.dayData['labels'];
    				

    				handle = $.jqplot("detailsGraph",seriesData, graphOptions).destroy();
    				handle = null;
    				handle = $.jqplot("detailsGraph",seriesData, graphOptions);
    				
    				$('table.jqplot-table-legend').attr('class', 'jqplot-table-legend-custom');
    				$('table.jqplot-table-legend-custom').attr('left', 40);

					$('[type=checkbox]').live('change',function(){
    					 var id = $(this).attr("id");
    				     if(id == 'every'){
    				    	 if($(this).is(':checked')){
    				    		 for (var i=0; i<handle.series.length; i++) {
    				    			 handle.series[i].show = true; // i is an integer
    				    		 } 
    				    		 $('[type="checkbox"]').attr('checked', true);
    				    	 }else{ 
    				    		 for (var i=0; i<handle.series.length; i++) {
    				    			 handle.series[i].show = false; // i is an integer
    				    		 }
    				    		 $('[type="checkbox"]').attr('checked', false);
    				    	 }
    				     }else{
	    					if($(this).is(':checked')){
	    				    	for (var i=0; i<switches[this.id].length; i++) {
	    				    		handle.series[switches[this.id][i]].show = true; // i is an integer
	    				    	}
	    				     } else {
	     				    	for (var i=0; i<switches[this.id].length; i++) {
	    				    		 handle.series[switches[this.id][i]].show = false; // i is an integer
	    				    	}
	    				    } 
    				     }
  				    	handle.replot();
				    	$('table.jqplot-table-legend').attr('class', 'jqplot-table-legend-custom');
					});
					ajaxReady();
				}
			}
		});	

	},

	init_compare : function( invtnum,divId, fnFinish) {
		ajaxStart();
		
		// initialize languages selector on the given div
	    $.getJSON('server.php?method=getCompareFilters&type=today', function(data) {
			$.ajax({
				url : 'js/templates/compareFilters.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data,
						'lang' : data.lang
					});
					$(divId).html(html);
					fnFinish.call();
					ajaxReady();
					
				},
				dataType : 'text',
			});
		});
		$('#invtnum').live('change', function(){
			WSL.createCompareGraph(1,$('#whichMonth').val(),$('#whichYear').val(),$('#compareMonth').val(),$('#compareYear').val()); // Initial// load// fast
		});
		$('#whichMonth').live('change', function(){
			WSL.createCompareGraph(1,$('#whichMonth').val(),$('#whichYear').val(),$('#compareMonth').val(),$('#compareYear').val()); // Initial// load// fast
		});
		$('#whichYear').live('change', function(){
			WSL.createCompareGraph(1,$('#whichMonth').val(),$('#whichYear').val(),$('#compareMonth').val(),$('#compareYear').val()); // Initial// load// fast
		});
		$('#compareMonth').live('change', function(){
			WSL.createCompareGraph(1,$('#whichMonth').val(),$('#whichYear').val(),$('#compareMonth').val(),$('#compareYear').val()); // Initial// load// fast
		});
		$('#compareYear').live('change', function(){
			WSL.createCompareGraph(1,$('#whichMonth').val(),$('#whichYear').val(),$('#compareMonth').val(),$('#compareYear').val()); // Initial// load// fast
		});
		
	},

	createCompareGraph : function(invtnum,whichMonth,whichYear,compareMonth,compareYear,type) {
		$('#whichMonth').val(whichMonth);
		$('#whichYear').val(whichYear);
		$('#compareMonth').val(compareMonth);
		$('#compareYear').val(compareYear);
		(type==0) ? compareYear=0 : compareYear=compareYear;
		$('#compareYear').val(compareYear);
		var graphOptions = {
				series:[
				        {xaxis:'x2axis',renderer:$.jqplot.LineRenderer},
				        {xaxis:'xaxis',renderer:$.jqplot.LineRenderer},
				        {label:'', xaxis:'yaxis',renderer:$.jqplot.LineRenderer}
				        ],
	          axesDefaults: {useSeriesColor: true,
	              tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
	              tickOptions: {
	                angle: -30,
	                fontSize: '10pt'
	              }
	          },
				legend : {
					show: true, location: 's', placement: 'outsideGrid',
					renderer: $.jqplot.EnhancedLegendRenderer,
					rendererOptions: {
						seriesToggle: 'normal',
						numberRows: 1,
				}}, 
				seriesDefaults:{rendererOptions: {barMargin: 10,barWidth:10},pointLabels: {show: false},},				
	            axes: {
	                xaxis: {
						labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
						renderer : $.jqplot.DateAxisRenderer,
						angle : -30,
						tickOptions : {formatString : '%d-%m'}
	                },
	                x2axis: {
						labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
						renderer : $.jqplot.DateAxisRenderer,
						angle : -30,
						tickOptions : {formatString : '%d-%m'}
	                },
	                yaxis: {}
	            },
				highlighter : {tooltipContentEditor: tooltipCompareEditor,show : true}
		};
		
		$.ajax({
			url : "server.php?method=getCompareGraph&invtnum=" + invtnum+'&whichMonth=' + whichMonth+'&whichYear=' + whichYear+'&compareMonth=' + compareMonth+'&compareYear=' + compareYear,
			method : 'GET',
			dataType : 'json',
			success : function(result) {

				if (result.dayData.data) {
					var dataDay1 = [];
					var dataDay2 = [];
					for (line in result.dayData.data.compare) {
						var object = result.dayData.data.compare[line];
						dataDay1.push([  object[0], object[1], object[2] ]);
					}
					for (line in result.dayData.data.which) {
						var object = result.dayData.data.which[line];
						dataDay2.push([  object[0], object[1], object[2] ]);
					}
					
					$("#compareFilter").append('<div id="compareGraph"></div>');
					$("#compareGraph").height(350);
					$("#compareGraph").width(830);

					graphOptions.axes.x2axis.label = $("#whichMonth option:selected").text()+' '+$("#whichYear option:selected").text();
					graphOptions.axes.xaxis.label = $("#compareMonth option:selected").text()+' '+$("#compareYear option:selected").text();
					
					graphOptions.series[0].label = $("#whichMonth option:selected").text()+' '+$("#whichYear option:selected").text();
					graphOptions.series[1].label = $("#compareMonth option:selected").text()+' '+$("#compareYear option:selected").text();
					
					graphOptions.axes.x2axis.min = result.dayData.data.which[0][0];
					graphOptions.axes.xaxis.min = result.dayData.data.compare[0][0];
					
					graphOptions.axes.yaxis.min = 0;
					
    				handle = $.jqplot("compareGraph", [ dataDay2, dataDay1], graphOptions);
    				handle.replot();
				}
			}
		});
	}
};


// api class
WSL.api.getHistoryValues = function(success) {
	$.getJSON("server.php", {
		method : 'getHistoryValues',
	}, success);
};

WSL.api.getTabs = function(page, success) {
	$.getJSON("server.php", {
		method : 'getTabs',
		'page' : page,
	}, success);
};

WSL.api.getCompare = function(success) {
	$.getJSON("server.php", {
		method : 'getCompareGraph'
	}, success);
};

WSL.api.getCompareFilters = function(succes){
	$.getJSON("server.php", {
		method : 'getCompareFilters'
	}, success);	
}

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


WSL.api.getPageMonthValues = function(date,success) {
	//console.log(date);
	$.getJSON("server.php", {
		method : 'getPageMonthValues',
		'date' : date,
	}, success);
};


WSL.api.getPageYearValues = function(date,success) {
	$.getJSON("server.php", {
		method : 'getPageYearValues',
		'date': date,
	}, success);
};


WSL.api.getEvents = function(invtnum, success) {
	$.getJSON("server.php", {
		method : 'getEvents',
		'invtnum' : invtnum,
	}, success);
};

WSL.api.getInvInfo = function(success) {
	$.getJSON("server.php", {
		method : 'getInvInfo'
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