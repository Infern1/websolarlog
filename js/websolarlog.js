// Create an handle bar ifCond equals statement
Handlebars.registerHelper('ifCond', function(v1, v2, options) {
	if (v1 == v2) {
		return options.fn(this);
	} else {
		return options.inverse(this);
	}
});

// calculate the JS parse time //
beforeLoad = (new Date()).getTime();
window.onload = pageLoadingTime;
function pageLoadingTime() {
	afterLoad = (new Date()).getTime();
	secondes = (afterLoad - beforeLoad) / 1000;
	document.getElementById("JSloadingtime").innerHTML = secondes;
}
var dataToday = [];
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
	createGraphToday : function(invtnum, divId) {
	    var graphOptions = {
                series:[{showMarker:false, fill: true}],
                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer
                },
                axes:{
                  xaxis:{
                    label:'',
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                    renderer: $.jqplot.DateAxisRenderer,
                    tickInterval: '3600', // 1 hour
                    tickOptions : {
                        angle: -30,
                        formatString: '%H:%M'
                        }
                  },
                  yaxis:{
                    label:'Avg. Power(W)',
                    min: 0,
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                  }
                },
                highlighter: {
                    show: true,
                    sizeAdjust: 7.5
                  },
                  cursor: {
                    show: false
                  }
        };
	    
	    var graphToday = null;
	    $.ajax({
            url: "server.php?method=getTodayValues&invtnum=" + invtnum,
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                for (line in result.data) {
                  var object = result.data[line];
                  dataToday.push([object[0], object[1]]);
                }
                graphOptions.axes.xaxis.min = result.data[0][0];
                if (graphToday != null) {
                    graphToday.destroy();
                }
                graphToday = $.jqplot(divId, [dataToday], graphOptions);

                mytitle = $('<div class="my-jqplot-title" style="position:absolute;text-align:center;padding-top: 1px;width:100%">Total energy today: ' + result.kwht + ' kWh</div>').insertAfter('#graphToday .jqplot-grid-canvas');
                //$('#titleToday').html();
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