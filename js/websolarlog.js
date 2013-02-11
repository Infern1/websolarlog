// calculate the JS parse time //

//Only for developer purposes
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

function analyticsJSCodeBlock() {
	$.getJSON('server.php?method=analyticsSettings', function(data) {
		if (data.googleSuccess) {
			$.ajax({
				url : 'js/templates/GoogleAnalyticsJSCodeBlock.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({'data' : data});
					$("body").append(html);
				}, dataType : 'text'
			});
		}
		if (data.piwikSuccess) {
			$.ajax({
				url : 'js/templates/PiwikAnalyticsJSCodeBlock.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({ 'data' : data });
					$("body").append(html);
				}, dataType : 'text'
			});
		}
		
	});
}

beforeLoad = (new Date()).getTime();
window.onload = pageLoadingTime;


function pageLoadingTime() {
	afterLoad = (new Date()).getTime();
	secondes = (afterLoad - beforeLoad) / 1000;
	document.getElementById("JSloadingtime").innerHTML = secondes;	
}



function is_array(input){
    return typeof(input)=='object'&&(input instanceof Array);
  }

var graphDay = null;
var dataDay = [];
var dataYesterday = [];
var dataLastDays = [];
var alreadyFetched = [];

var currentGraphHandler;
var todayTimerHandler;

function tooltipTodayContentEditor(str, seriesIndex, pointIndex, plot,series	) {
	
	
	var returned = "";
	if(is_array(plot.series[0].data[pointIndex])==true){
		( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum.: "+ plot.series[0].data[pointIndex][1]+ " W<br>"+bold[1]; //0
	}
	if(is_array(plot.series[1].data[pointIndex])==true){
		( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Energy:"+ plot.series[1].data[pointIndex][1]+ " W<br>"+bold[1];//1
	}
	if(is_array(plot.series[2].data[pointIndex])==true){
		var smoothGasLineLength = plot.series[2].data.length-1;
		var GasLineLength = plot.series[7].data.length;
		var multiply = GasLineLength/smoothGasLineLength;
		pointIndex2 = Math.ceil((multiply*pointIndex) * 1) / 1;
		//console.log(""+GasLineLength+"/"+smoothGasLineLength+"="+multiply+ "*"+pointIndex+"="+pointIndex*multiply+" round("+pointIndex2+")");
		( seriesIndex == 2 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Gas:"+ plot.series[7].data[pointIndex2][1]+ " l<br>"+bold[1];//2
	}
	if(is_array(plot.series[2].data[pointIndex])==true){
	( seriesIndex == 2 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Gas2: "+ plot.series[2].data[pointIndex][1]+ " l<br>"+bold[1];//2
	}
	if(is_array(plot.series[3].data[pointIndex])==true){
		( seriesIndex == 3 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"low Usage:"+ plot.series[3].data[pointIndex][1]+ " W<br>"+bold[1];//3
	}
	if(is_array(plot.series[4].data[pointIndex])==true){
		( seriesIndex == 4 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"high Usage:"+ plot.series[4].data[pointIndex][1]+ " W<br>"+bold[1];//4
	}
	if(is_array(plot.series[5].data[pointIndex])==true){
		( seriesIndex == 5 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"low Return:"+ plot.series[5].data[pointIndex][1]+ " W<br>"+bold[1];//5
	}
	if(is_array(plot.series[6].data[pointIndex])==true){
		( seriesIndex == 6 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"high Return:"+ plot.series[6].data[pointIndex][1]+ " W<br>"+bold[1];//6
	}
	if(is_array(plot.series[7].data[pointIndex])==true){
		( seriesIndex == 7 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"gas dummy:"+ plot.series[7].data[pointIndex][1]+ " l<br>"+bold[1];//6
	}
	if(is_array(plot.series[3].data[pointIndex])==true){
		returned += "Date:"+ plot.series[3].data[pointIndex][2]+"";
	}
	return returned;
}


function tooltipPeriodContentEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = ""; 
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Energy:"+ plot.series[0].data[pointIndex][1]+ " kWh<br>"+bold[1];
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Cum.: "+ plot.series[1].data[pointIndex][1]+ " kWh<br>"+bold[1];
	returned += "Date:"+ plot.series[1].data[pointIndex][2]+"";
	return returned;
}

function tooltipProductionContentEditor(str, seriesIndex, pointIndex, plot,series	) { 
	var returned = ""; 
	var diff_add = '';
	var yearDiff_add = "";
	var diff = plot.series[0].data[pointIndex][1] - plot.series[1].data[pointIndex][1];
	
	
	var yearDiff = plot.series[3].data[pointIndex][1] - plot.series[2].data[pointIndex][1];
	(diff>0)? diff_add = '+': diff_add = diff_add ;
	(yearDiff>0)? yearDiff_add = '+': yearDiff_add = yearDiff_add ;
	( seriesIndex == 0 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Harvested:"+ plot.series[0].data[pointIndex][1]+" kWh<br>"+bold[1];
	( seriesIndex == 1 ) ? bold=["<b>","</b>"] : bold=["",""]; returned += bold[0]+"Expected: "+ plot.series[1].data[pointIndex][1] +" kWh<br>"+bold[1];
	returned += "This month: "+diff_add+""+ diff +" kWh<br>";
	( seriesIndex == 3 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum. Harvested:"+plot.series[3].data[pointIndex][1]+" kWh<br>"+bold[1];
	( seriesIndex == 2 ) ? bold=["<b>","</b>"] : bold=["",""];returned += bold[0]+"Cum. Expected: "+plot.series[2].data[pointIndex][1]+" kWh<br>"+bold[1];
	
	returned += "This year: "+ yearDiff_add +""+ yearDiff +" kWh<br>";
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
	

	if (currentGraphHandler){
    	currentGraphHandler.destroy();
    	$("#graph"+tab+"Content").html('<div id="loading">loading...</div>');
    }
    if (todayTimerHandler){
        window.clearInterval(todayTimerHandler);
    }
    
    if (request=='picker'){
    	$('#lastCall').val('picker');
    	period= $('#pickerPeriod').val();
    	if (period == "Today") {

    		WSL.createDayGraph(invtnum, period, tab,date ,function(handler) {currentGraphHandler = handler;$("#loading").remove();});
    	}else{

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
                currentGraphHandler.destroy();
                $("#graph"+tab+"Content").empty();
                $("#graph"+tab+"Content").html('<div id="loading">refreshing graph...</div>');
            }
            WSL.createDayGraph(invtnum, "Today",tab, date ,function(handler) {currentGraphHandler = handler;$("#loading").remove();});				                    
        }, 90000); // every minute
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
				    var picker = $("#datepicker");
				    var date=new Date(picker.datepicker('getDate'));
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
				    picker.datepicker('setDate', date);
				    handleGraphs('picker',invtnum);
				});
				
				$('#previous').click(function () {
				    var picker = $("#datepicker");
				    var date=new Date(picker.datepicker('getDate'));
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
				    picker.datepicker('setDate', date);
				    handleGraphs('picker',invtnum);
				});
				invtnum = $('#pickerInv').val();
		    	handleGraphs('standard',invtnum);
			},
			dataType : 'text'
		});
    });
}

var gaugeGP;
var gaugeIP;
var gaugeEFF;


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


						
						GP = 3600 / 10;
						gaugeGPOptions = {
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
						IP = 3600 / 10;
						gaugeIPOptions = {
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
						EFF = 100 / 10;
						gaugeEFFOptions = {
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
						

				delete data;
				$.ajax({
					url : 'js/templates/liveInverters.hb',
					success : function(source) {
						var template = Handlebars.compile(source);
						var html = template({
							'data' : data,
							'lang':data.lang
						});
						$(divId).html(html);
						
						
						//////////////////
						////////////////////////////////////////

						if(gaugeGP){
							//console.log('gauge destroy');
							gaugeGP.destroy();
						}
						$('#gaugeGP').empty();
						gaugeGP = $.jqplot('gaugeGP', [ [ 0.1 ] ],gaugeGPOptions);
						gaugeGP.series[0].data = [ [ 'W',data.sumInverters.GP ] ];
						gaugeGP.series[0].label = data.sumInverters.GP;
						document.title = '('+ data.sumInverters.GP+ ' W) WebSolarLog';
						gaugeGP.replot();
						
						if(gaugeIP){
							gaugeIP.destroy();
						}
						$('#gaugeIP').empty(); 
						gaugeIP = $.jqplot('gaugeIP', [ [ 0.1 ] ],gaugeIPOptions);
						gaugeIP.series[0].data = [ [ 'W',data.sumInverters.IP ] ];
						gaugeIP.series[0].label = data.sumInverters.IP;
						gaugeIP.replot();
						
						if(gaugeEFF){
							gaugeEFF.destroy();
						}
						$('#gaugeEFF').empty(); 
						gaugeEFF = $.jqplot('gaugeEFF', [ [ 0.1 ] ],gaugeEFFOptions);
						gaugeEFF.series[0].data = [ [ 'W',data.sumInverters.EFF] ];
						gaugeEFF.series[0].label = data.sumInverters.EFF+' %';
						gaugeEFF.replot();


						ajaxReady();
					},
					dataType : 'text',
				});


		});
	},

	init_misc : function(invtnum, divId) {
		// Retrieve the error events
		ajaxStart();
		WSL.api.getMisc(invtnum, function(data) {
			$.ajax({
				url : 'js/templates/misc.hb',
				success : function(source) {
					var template = Handlebars.compile(source);
					var html = template({
						'lang' : data.lang,
						'data' : data
					});
					
					$(divId).html(html);
					$( ".accordion" ).accordion({collapsible: true});
					
					ajaxReady();
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
					var template = Handlebars.compile(source);
					var html = template({
						'data' : data,
						'lang' : data.lang
					});

					$(monthValues).html(html);

				    $(function() {

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
										
							        }
								});
					            //new Date(year, month, day) // W3schools
								$("#datePickerPeriod").datepicker('setDate', new Date());
							}else{
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
							        }
								});
								

								pickerDate = pickerDate.split('-'); // 01-2012 (month-year)
								pickerDate[0] = pickerDate[0]-1;
					            //new Date(year, month, day) // W3schools
								$("#datePickerPeriod").datepicker('setDate', new Date(pickerDate[1],pickerDate[0],1));

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

		// initialize languages selector on the given div
		WSL.api.getPageYearValues(completeDate,function(data) {
			$.ajax({
				url : 'js/templates/yearValues.hb',
				success : function(source) {

					var template = Handlebars.compile(source);
					var html = template({
						'data' : data,
						'lang' : data.lang
					});

					$(yearValues).html(html);

				    $(function() {
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
							        }
								});
								$("#datePickerPeriod").datepicker('setDate', new Date());
							}else{

								$("#datePickerPeriod").datepicker({
							        dateFormat: 'yy',
							        changeMonth: false,
							        changeYear: true,
							        //showButtonPanel: true,

							        onClose: function(dateText, inst) {
							            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
							            //new Date(year, month, day)
							            $(this).val($.datepicker.formatDate('yy', new Date(year, 1, 1)));
										WSL.init_PageYearValues("#columns","#periodList"); // Initial load fast

							        }
								});

					            //new Date(year, month, day)
								$("#datePickerPeriod").datepicker('setDate', new Date(pickerDate,1,1));

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
			url : "server.php?method=getGraphDayPoints&type="+ getDay +"&date="+ date +"&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {

				// add a custom tick formatter, so that you don't have to include the entire date renderer library.
				$.jqplot.DateTickFormatter = function(format, val) {
				    // for some reason, format isn't being passed through properly, so just going to hard code for purpose of this jsfiddle
				    val = (new Date(val)).getTime();
				    format = '%H:%M';
				    return $.jsDate.strftime(val, format);
				};
				
				var graphOptions = {
					seriesDefaults: {
						rendererOptions: {smooth: true},showMarker:false,autoscale:true,
					},
					series : [],
					axesDefaults : {
						useSeriesColor: true,
						labelRenderer : $.jqplot.CanvasAxisLabelRenderer,
					},
					animate: true,
					legend : {
						show : true,
						location:"nw",
						renderer: $.jqplot.EnhancedLegendRenderer,
						rendererOptions: {
			                seriesToggle: 'normal',
			            },
					}, 
					axes : {}, 
					highlighter : {
						tooltipContentEditor: tooltipTodayContentEditor,show : true,tooltipLocation:'n',
					},
					cursor : {
						show : false
					}
				};
				seriesData=[]; 

				var json = [];
				if (result.dayData) {
					if (result.dayData.graph) {
						for (line in result.dayData.graph.points) {
							var json = [];
							for (values in result.dayData.graph.points[line]) {
								json.push([result.dayData.graph.points[line][values][0],result.dayData.graph.points[line][values][1]]);
							}
							seriesData.push(json);
						}
						graphOptions.legend.labels = result.dayData.graph.labels;

						for(axes in result.dayData.graph.axes){
							if(result.dayData.graph.axes[axes]['renderer']=='DateAxisRenderer'){
								result.dayData.graph.axes[axes]['renderer'] = $.jqplot.DateAxisRenderer;
							}
							if(result.dayData.graph.axes[axes]['tickRenderer']=='CanvasAxisTickRenderer'){
								result.dayData.graph.axes[axes]['tickRenderer'] = $.jqplot.CanvasAxisTickRenderer;
							}
							if(result.dayData.graph.axes[axes]['labelRenderer']=='CanvasAxisLabelRenderer'){
								result.dayData.graph.axes[axes]['labelRenderer'] = $.jqplot.CanvasAxisLabelRenderer;
							}
							if(result.dayData.graph.axes[axes]['formatter']=='DayDateTickFormatter'){
								result.dayData.graph.axes[axes]['labelRenderer'] = $.jqplot.DayDateTickFormatter;
							}
							
						}

						graphOptions.axes = result.dayData.graph.axes;
						//console.log('axes');
						//console.log(graphOptions.axes);
						graphOptions.axes.xaxis.min = result.dayData.timestamp.beginDate*1000;
						graphOptions.axes.xaxis.max = result.dayData.timestamp.endDate*1000;
						graphOptions.series = result.dayData.graph.series;
					}
					

					

					//console.log('series');
					//console.log(graphOptions.series);
					$('#graph' + tab + 'Content').empty();
	    			handle = $.jqplot('graph' + tab + 'Content',  seriesData	 , graphOptions);

    				delete dataDay1;
    				delete dataDay2;
    				delete dataDay3;
    				delete dataDay4;
    				delete dataDay5;
    				delete dataDay6;
    				delete dataDay7;
    				delete dataDay8;
    				delete graphOptions;

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


				// add a custom tick formatter, so that you don't have to include the entire date renderer library.
				$.jqplot.DateTickFormatter = function(format, val) {
				    // for some reason, format isn't being passed through properly, so just going to hard code for purpose of this jsfiddle
				    val = (new Date(val)).getTime();
				    format = '%b %#d';
				    return $.jsDate.strftime(val, format);
				};

				
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
							tickRenderer: $.jqplot.CanvasAxisTickRenderer,
							tickOptions : {
								angle:-20
							}
						},
						/*
						r/ray('label'=>'','renderer'=>'DateAxisRenderer',
								'tickRenderer'=>'CanvasAxisTickRenderer','labelRenderer'=>'CanvasAxisLabelRenderer',
								'tickInterval'=>3600,'tickOptions'=>array('formatter'=>'DefaultTickFormatter','angle'=>-45)
						*/
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

		$.ajax({
			url : "server.php?method=getProductionGraph&invtnum=" + invtnum,
			method : 'GET',
			dataType : 'json',
			success : function(result) {
				var dataDay1 = [];
				var dataDay2 = [];
				var dataDay3 = [];
				var dataDay4 = [];
				var dataTable= [];
				var ticksTable= [];
				if (result.dayData) {
					
					

					$("#main-middle").prepend('<div id="ProductionGraph"></div>');
					//console.log(result);
					$.ajax({
						url : 'js/templates/productionFigures.hb',
						success : function(source) {
							var template = Handlebars.compile(source);
							
							for (line in result.dayData.data) {
								var data = result.dayData.data[line];
								var item = {
								        "har": data[0],
								        "date": data[1],
								        "exp": data[2],
										"diff": data[3],
										"cumExp": data[4],
										"cumHar": data[5],
										"cumDiff": data[6]
								};
								//console.log(item);
								dataTable.push([item]);
							}

							var html = template({
								'data' : dataTable,
								'lang':result.lang
							});
							//console.log(dataTable);
							
							$('#ProductionGraph').after(html);

						},
						dataType : 'text',
					});
					
					ticksTable.push("0");
					var monthTable=[];
					for (line in result.dayData.data) {
						var object = result.dayData.data[line];
						//monthTable.push(result.dayData.ticks[line]);
						dataDay1.push(object[0] );
						dataDay2.push(object[2] );
						dataDay3.push(object[4] );
						dataDay4.push(object[5] );
					}
					ticksTable.push("13");
					
					var graphOptions = {
							series : [
							          {label:'Harvested(kWh)',yaxis:'yaxis', pointLabels: {show: false},
							        	  renderer: $.jqplot.BarRenderer,
											
									        rendererOptions: {
									            barPadding: 5,      // number of pixels between adjacent bars in the same
									                                // group (same category or bin).
									            barMargin: 5,      // number of pixels between adjacent groups of bars.
									            barDirection: 'vertical', // vertical or horizontal.
									            barWidth: 15,     // width of the bars.  null to calculate automatically.
									            shadowOffset: 2,    // offset from the bar edge to stroke the shadow.
									            shadowDepth: 5,     // nuber of strokes to make for the shadow.
									            shadowAlpha: 0.2,   // transparency of the shadow.
									        },
									        min:0,
							          },
							          {label:'Expected(kWh)',yaxis:'y2axis',pointLabels: {show: false},
							        	  renderer: $.jqplot.BarRenderer,
											
									        rendererOptions: {
									            barPadding: 5,      // number of pixels between adjacent bars in the same
									                                // group (same category or bin).
									            barMargin: 5,      // number of pixels between adjacent groups of bars.
									            barDirection: 'vertical', // vertical or horizontal.
									            barWidth: 15,     // width of the bars.  null to calculate automatically.
									            shadowOffset: 2,    // offset from the bar edge to stroke the shadow.
									            shadowDepth: 5,     // nuber of strokes to make for the shadow.
									            shadowAlpha: 0.2,   // transparency of the shadow.
									        },
									        min:0,
									  },
							          {label:'Cum. Expected(kWh)',yaxis:'y3axis',renderer:$.jqplot.LineRenderer, pointLabels: {show: false}},
							          {label:'Cum. Harvested(kWh)',yaxis:'y4axis',renderer:$.jqplot.LineRenderer, pointLabels: {show: false}},
						    ],

						    legend: {
						        show: true,
						        location: 'nw',     // compass direction, nw, n, ne, e, se, s, sw, w.
						        xoffset: 12,        // pixel offset of the legend box from the x (or x2) axis.
						        yoffset: 12,        // pixel offset of the legend box from the y (or y2) axis.
						    },

						
							axes : {
								xaxis: {
									
								       show: true,    // wether or not to renderer the axis.  Determined automatically.
								        pad: 1,       // a factor multiplied by the data range on the axis to give the
								                        // axis range so that data points don't fall on the edges of the axis.
								        ticks: [],      // a 1D [val1, val2, ...], or 2D [[val, label], [val, label], ...]
								                        // array of ticks to use.  Computed automatically.
								        numberTicks: ticksTable,
								        ticks: ticksTable,
								        renderer: $.jqplot.CategoryAxisRenderer,  // renderer to use to draw the axis,
								        labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
								        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
								        rendererOptions: {},    // options to pass to the renderer.  LinearAxisRenderer
								                                // has no options,
								        tickOptions: {
								            mark: 'outside',    // Where to put the tick mark on the axis
								                                // 'outside', 'inside' or 'cross',
								            showMark: true,
								            showGridline: true, // wether to draw a gridline (across the whole grid) at this tick,
								            markSize: 4,        // length the tick will extend beyond the grid in pixels.  For
								                                // 'cross', length will be added above and below the grid boundary,
								            show: true,         // wether to show the tick (mark and label),
								            showLabel: true,    // wether to show the text label at the tick,
								            formatString: '',   // format string to use with the axis tick formatter
								        },
								        showTicks: true,        // wether or not to show the tick labels,
								        showTickMarks: true,    // wether or not to show the tick marks
							        },
								yaxis :  {
									label:'Harvested(kWh)',
									min:0,
									labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
								},

								
								y2axis : {label:'Expected(kWh)',
									min:0,
									labelRenderer: $.jqplot.CanvasAxisLabelRenderer,

								},
								y3axis : {label:'Cum. Expected(kWh)',min:0,labelRenderer: $.jqplot.CanvasAxisLabelRenderer,},
								y4axis : {label:'Cum. Harvested(kWh)',min:0,labelRenderer: $.jqplot.CanvasAxisLabelRenderer,},
							},

							highlighter : {
								tooltipContentEditor: tooltipProductionContentEditor,
								show : true,
								tooltipLocation:'n'
							},
							cursor:{show:false},

						};
						
    				
    				var harvested = [];
    				var expected = [];
    				for (var i=0; i<dataDay1.length; i++) {
    				    // Iterates over numeric indexes from 0 to 5, as
						// everyone expects
    					harvested.push(dataDay1[i]);
    				}
    				var maxHarvested = Math.max.apply(Math, harvested);

    				for (var i=0; i<dataDay2.length; i++) {
    				    // Iterates over numeric indexes from 0 to 5, as
						// everyone expects
    					expected[i] = dataDay2[i];
    				}
    				var maxExpected = Math.max.apply(Math, expected);

    				maxAxesValue = Math.max(Math.round(maxHarvested/100)*100,Math.round(maxExpected/100)*100); 
    				var axesMargin = Math.round(maxAxesValue/100)*10;
    				graphOptions.axes.yaxis.max = maxAxesValue+axesMargin;
    				graphOptions.axes.y2axis.max = maxAxesValue+axesMargin;

    				maxAxesValue = Math.max(Math.round(dataDay3[11]/100)*100,Math.round(dataDay4[11]/100)*100);
    				var axesMargin = Math.round(maxAxesValue/100)*10;
    				graphOptions.axes.y3axis.max = maxAxesValue+axesMargin;
    				graphOptions.axes.y4axis.max = maxAxesValue+axesMargin;
    				//graphOptions.axes.xaxis = ticksTable;

					graphOptions.axes.xaxis.min = 0;
					graphOptions.axes.xaxis.max = 13;
    				
    				
    				$("#ProductionGraph").height(450);

    				handle = $.jqplot("ProductionGraph", [ dataDay1,dataDay2,dataDay3,dataDay4], graphOptions);

    				ajaxReady();
				}
			}
		});
	},

	init_details : function(divId,queryDate){
	
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

					(queryDate!="undefined") ? date = queryDate: date = date;


					// get the details graph....
					//WSL.createDetailsGraph(invtnum, divId,date);
					
					$(".mainTabContainer").hover(function() {
			    		$("#pickerFilterDiv").hide();
			    		$( "#datepicker" ).datepicker("hide");
					}, function() {
						$("#pickerFilterDiv").show();
					});

					$('#pickerPeriod').live("change",
							function(){
						WSL.createDetailsGraph(invtnum, divId,date);
							}
					);
					
					
					$('#datepicker').live("change",
							function(){
						WSL.createDetailsGraph(invtnum, divId,date);
							}
					);
					
					$('#pickerInv').live("change",
							function(){
						WSL.createDetailsGraph(invtnum, divId,date);
							}
					);
					
					
				
					
					$('#next').unbind('click');
					$('#previous').unbind('click');
					$('#pickerPeriod').unbind('click');
					
					
					$('#next').click(function () {
					    var picker = $("#datepicker");
					    var date=new Date(picker.datepicker('getDate'));
					    
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
					    picker.datepicker('setDate', date);
					    var splitDate = $('#datepicker').val().split('-');
					    WSL.createDetailsGraph(invtnum, divId, splitDate[0]+'-'+splitDate[1]+'-'+splitDate[2]);
					});
					
					$('#previous').click(function () {
					    var picker = $("#datepicker");
					    var date=new Date(picker.datepicker('getDate'));
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
					    picker.datepicker('setDate', date);
					    var splitDate = $('#datepicker').val().split('-');
					    WSL.createDetailsGraph(invtnum, divId, splitDate[0]+'-'+splitDate[1]+'-'+splitDate[2]);
					});
				    //picker.datepicker('setDate', date);
					var invtnum = $('#pickerInv').val();

				    WSL.createDetailsGraph(invtnum, divId,date);
							
				},
				dataType : 'text'
			});
			
	    });
	    
	},

	createDetailsGraph : function(invtnum, divId,date) {

		//var date = $('#datepicker').val();

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
								pointLabels: {show: false},
								},
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
    				delete handle;
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

	init_compare : function( invtnum,divId ) {
		ajaxStart();
		var dataTable= []; 

		
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
					
					WSL.createCompareGraph(invtnum,whichMonth,whichYear,compareMonth,compareYear,0);

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
					var compareTable = [];
					var whichTable = [];
					for (line in result.dayData.data.compare) {
						var object = result.dayData.data.compare[line];
						console.log(object);
						dataDay1.push([  object[0], object[2], object[3] ]);
						
							var item = {
							        "timestamp": object[0],
							        "har": object[2],
							        "date": object[1],
							        "displayKWH":object[3],
							};
							compareTable.push([item]);
						
					}
					for (line in result.dayData.data.which) {
						var object = result.dayData.data.which[line];
						dataDay2.push([  object[0], object[2], object[3] ]);
						var item = {
						        "timestamp": object[0],
						        "har": object[2],
						        "date": object[1],
						        "displayKWH":object[3],
						};
						whichTable.push([item]);
					
					}
					
					$("#content").append('<div id="compareGraph"></div>');
					$('#content').append('<div id="compareFigures"></div>');
					$("#compareGraph").height(350);
					$("#compareGraph").width(830);

					graphOptions.axes.x2axis.label = $("#whichMonth option:selected").text()+' '+$("#whichYear option:selected").text();
					graphOptions.axes.xaxis.label = $("#compareMonth option:selected").text()+' '+$("#compareYear option:selected").text();
					
					graphOptions.series[0].label = $("#whichMonth option:selected").text()+' '+$("#whichYear option:selected").text();
					graphOptions.series[1].label = $("#compareMonth option:selected").text()+' '+$("#compareYear option:selected").text();
					
					graphOptions.axes.x2axis.min = result.dayData.data.which[0][0];
					graphOptions.axes.xaxis.min = result.dayData.data.compare[0][0];
					
					graphOptions.axes.yaxis.min = 0;
					//console.log(dataDay2, dataDay1);
    				handle = $.jqplot("compareGraph", [ dataDay2, dataDay1], graphOptions);
    				handle.replot();
    				//console.log(result.lang);
    				$.ajax({
						url : 'js/templates/compareFigures.hb',
						success : function(source) {
							var template = Handlebars.compile(source);
							//console.log(compareTable);
							var html = template({
								'compare' :  compareTable,
								'which' :  whichTable,
								'diff' : result.dayData.data.diff,
								'lang': result.lang
							});
							
							
							
							$('#compareFigures').html(html);
							
						},
						dataType : 'text',
					});
					
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


WSL.api.getMisc = function(invtnum, success) {
	$.getJSON("server.php", {
		method : 'getMisc',
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
