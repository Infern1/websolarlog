$(function()
{
    $.getJSON('admin-server.php?s=isLogin', function(data) {
        if (data.result === true) {
            init_menu();
            var hash = document.URL.split('#');// split on #
            // go further if there is a split and more than 1 element in the array
            if(hash.length>1){
            	var shortcut = hash[1];
            	if(shortcut.indexOf('?')){
            		var shortcut = shortcut.split('?'); // remove querystring params
            		var shortcut = shortcut[0];
            	}
            }
            // check if there is a function
            if(shortcut){
            	// call the #xxxxxx function // example: '/admin/#backup' load the backup page.
            	runFunction('init_'+shortcut);
            }else{
            	// else always load the general function
            	init_general(); // First admin item
            }
        } else {
            $.ajax({
                url : 'js/templates/login.hb',
                dataType : 'text',
                success : function(source) {
                    var template = Handlebars.compile(source);
                    var html = template({
                        'data' : data
                    });
                    $('#content').html(html);
                    $('#loginForm').bind('submit', function (){
                        var data = $(this).serialize();
                        $.post('admin-server.php', data, function(logindata){
                            if (logindata.result == true) {
                                init_menu();
                                init_general(); // First admin item
                                $.pnotify({
                                    title: 'Login',
                                    text: 'Succesfully logged in.',
                                    type: 'success'
                                });
                            } else {
                                $.pnotify({
                                    title: 'Login',
                                    text: 'Failed to log in. Please retry!',
                                    type: 'error'
                                });
                            }
                        });
                        return false; // prevent form submit
                    });
                }
            });
        }
    });
});
/*
 * Init var/array
 */


function runFunction(name, arguments){
    var fn = window[name];
    if(typeof fn !== 'function')
        return;

    fn.apply(window, arguments);
}


var originalPerc=new Array(2,5,7,10,12,14,14,12,10,7,5,2);
var Perc=[];
var Month=new Array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');

/**
 *  Init PowerPreset value's
 */
function init_KWHcalc(inv_data){
    var data = [];
    data['inverterId'] = $('input[name="id"]').val();
    data['month_perc'] = [];
    var newPerc = [];
    
    // Merge month with percentages from live stream if not null or empty
    $.each(Month, function(){
        var perc = inv_data.inverter["expected"+this.toUpperCase()];
        var month_perc = [];
        month_perc['month'] = this;
        month_perc['perc'] = perc;
        if (typeof (perc) != 'undefined' && perc != null && perc != "" ) {
            newPerc.push(perc);            
        }
        data['month_perc'].push(month_perc);
    });
    
    // Replace Perc if we have received an valid value for all months!
    if (newPerc.length==12) {
        Perc = newPerc;        
    } else {
        Perc = originalPerc;
    }
    
    $.ajax({
        url : 'js/templates/expectation.hb',
        dataType : 'text',
        success : function(source) {
            var template = Handlebars.compile(source);
            var html = template({
                'data' : data
            });

            $('#content').append(html);
            $("input[id$='KWH']").bind("keyup", function(){
                object = this;
                KWHcalc(object,Perc,Month);
            });
            $("#totalKWHProd").bind("keyup", function(){
                object = this;
                KWHcalc(object,Perc,Month);
                $('input[name="expectedkwh"]').val($("#totalKWHProd").val());
            });
            
            $("#totalKWHProd").val($('input[name="expectedkwh"]').val()).trigger("keyup");
            
            $('#btnExpectationSubmit').bind('click', function(){
                var inverterId = $('input[name="id"]').val();
                var data = $(this).parent().serialize();
                $.post('admin-server.php', data, function(){
                    init_inverters(inverterId);                        
                    $.pnotify({
                        title: 'Saved',
                        text: 'You\'re changes have been saved.',
                    });
                });
            });
            
            $('#btnExpectationDefault').bind('click', function(){
                Perc = originalPerc;
                $("#totalKWHProd").val($('input[name="expectedkwh"]').val()).trigger("keyup");
            });
            
        }
    });
}

/**
 * Init menu buttons
 */
function init_menu() {
    $("#btnAdvanced").bind('click', function() { init_advanced();});
    $("#btnGeneral").bind('click', function() { init_general();});
    $("#btnInverters").bind('click', function() { init_inverters(); });
    $("#btnGrid").bind('click', function() { init_grid();});
    $("#btnEmail").bind('click', function() { init_mail(); });
    $("#btnTestPage").bind('click', function() { init_testpage(); });
    $("#btnTariff").bind('click', function() { init_tariff(); });
    $("#btnUpdate").bind('click', function() { init_updatepage(); });
    $("#btnBackup").bind('click', function() { init_backup(); });
}


function init_tariff(){
    $('#sidebar').html("");
    var content = $('#content');
    content.html('<div id="c_general"></div><div id="c_communication"></div><div id="c_security"></div>'); // Clear old data
    $.getJSON('admin-server.php?s=get-tariffs', function(data) {
        $.ajax({
            url : 'js/templates/tariffs.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#c_general', content).html(html);
                
            },
            dataType : 'text'
        });        
    });
	
}

function init_backup() {
    $('#sidebar').html("");
    
    $.getJSON('admin-server.php?s=dropbox', function(data) {
    	if (data.available){


    	    $.getJSON('admin-server.php?s=dropboxGetFiles', function(data) {
    	        $.ajax({
    	            url : 'js/templates/dropboxFiles.hb',
    	            success : function(source) { 
    	                var template = Handlebars.compile(source);
    	                var html = template({
    	                    'data' : data
    	                });
    	                $('#content').html(html);

    		            $('.deleteFile').bind('click', function(){deleteFiles(this);});
	                    $('#makeBackup').bind('click', function(){makeBackup();});
	                    $('#dropboxSync').bind('click', function(){dropboxSync();}); 
    	            },
    	            dataType : 'text'
    	        });
    	    });
    	    
    	    
    	                         
    	}else{
            $.ajax({
                url : 'js/templates/dropbox.hb',
                success : function(source) {
                    var template = Handlebars.compile(source);
                    var html = template({
                        'data' : data
                    });
                    $('#content').html(html);
                },
                dataType : 'text'
            });
    	}
    });
}

function dropboxSync(notice){
    if($('#requestActive').val() == 0){
    	$('#requestActive').val(1);
		var SyncNotice = $.pnotify({
	        title: 'Dropbox',
	        text: 'Syncing files....',
	        nonblock: true,
	        hide: false,
	        closer: false,
	        sticker: false
	    });
	    $.getJSON('admin-server.php?s=dropboxSyncFiles', function(data) {
	        $.ajax({
	        	 async: true,
	        	url : 'js/templates/dropboxFiles.hb',
	            success : function(source) { 
	                var template = Handlebars.compile(source);
	                var html = template({
	                    'data' : data
	                });
	                $('#content').html(html);
		            $('.deleteFile').bind('click', function(){deleteFiles(this);});
	                $('#makeBackup').bind('click', function(){makeBackup();});
	                $('#dropboxSync').bind('click', function(){dropboxSync();});
	                
	                
	                $('#requestActive').val(0);
		   	       
		   	       var SyncNotice = $.pnotify({
		   	 	        title: 'Dropbox',
		   	 	        text: 'Sync ready.',
		   	 	        nonblock: true,
		   	 	        hide: true,
		   	 	        closer: true,
		   	 	        sticker: false,
		   	 	        type:'success'
		   	 	    });
	            },
	            dataType : 'text'
	        }).done(function() { SyncNotice.pnotify_remove(); });
	    });
    }else{
		var SyncNotice = $.pnotify({
	        title: 'Dropbox',
	        text: 'There is a request running.',
	        nonblock: true,
	        hide: true,
	        closer: true,
	        sticker: false,
	        type:'error'
	    });
    }
}

function makeBackup(){
    if($('#requestActive').val() == 0){
    	$('#requestActive').val(1);
	    var BackupNotice = $.pnotify({
	        title: 'Dropbox',text: 'Backup is running....',nonblock: true,hide: false,closer: false,sticker: false
	    });
	    var data = '';
	    $.post('admin-server.php?s=dropboxMakeBackup',data , function(){
	    	$.getJSON('admin-server.php?s=dropboxGetFiles', data, function(data){
	    		
	            $.ajax({
	           	url : 'js/templates/dropboxFiles.hb',
	               success : function(source) { 
	                   var template = Handlebars.compile(source);
	                   var html = template({
	                       'data' : data
	                   });
	                   $('#content').html(html);
	   	               $('.deleteFile').bind('click', function(){deleteFiles(this);});
	   	               $('#makeBackup').bind('click', function(){makeBackup();});
	   	               $('#dropboxSync').bind('click', function(){dropboxSync();});
	   	               if (BackupNotice.pnotify_remove) BackupNotice.pnotify_remove();
			   	       var SyncNotice = $.pnotify({
			   	 	        title: 'Dropbox',
			   	 	        text: 'Backup ready.',
			   	 	        nonblock: true,
			   	 	        hide: true,
			   	 	        closer: true,
			   	 	        sticker: false,
			   	 	        type:'success'
			   	 	    });
	   	               $('#requestActive').val(0)
	               },
	               dataType : 'text'
	           });
	        });
	    }); 
    }else{
		var SyncNotice = $.pnotify({
	        title: 'Dropbox',
	        text: 'There is a request running.',
	        nonblock: true,
	        hide: true,
	        closer: true,
	        sticker: false,
	        type:'error'
	    });
    }
}

function deleteFiles(vars){

		var id = vars.id.replace("delete","");
		var fullPath = $("#path"+id).html();

		$("#file"+id).hide("blind", { direction: "vertical" }, 1000);
        var SyncNotice = $.pnotify({
            title: 'Dropbox',
            text: 'Deleting file....',
            nonblock: true,
            hide: false,
            closer: false,
            sticker: false
        });
        $.post('admin-server.php?s=dropboxDeleteFile',{path: fullPath} , function(){
            if (SyncNotice.pnotify_remove) SyncNotice.pnotify_remove();
        });
}

function init_advanced() {
    $('#sidebar').html("");
    
    $.getJSON('admin-server.php?s=advanced', function(data) {
        $.ajax({
            url : 'js/templates/advanced.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                
                $('#content').html(html);
                
                $('#btnAdvancedSubmit').bind('click', function(){
                	var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(){
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });
                    });
                });   
                
                $('#sendTweet').bind('click', function(){
                 	$.pnotify({
                 		title: 'Twitter',
                 		text: 'Sending Tweet'
                 	});
                	 $.getJSON('admin-server.php?s=sendTweet', function(data) {
	                	 if(data.tweetSend==1){
	                      	$.pnotify({
	                     		title: 'Twitter',
	                     		text: 'Tweet send! Check your Twitter :)'
	                     	});
	                	 }
	                	 if(data.tweetSend==0){
		                      $.pnotify({
		                     	title: 'Twitter',
		                     	text: 'Something went wrong:<br>'+data.message+'',
		                     	type: 'error'
		                    });
	                	 }
                     });
                });
            },
            dataType : 'text'
        });        
    });
}


function init_general() {
    $('#sidebar').html("");
    var content = $('#content');
    content.html('<div id="c_general"></div><div id="c_communication"></div><div id="c_security"></div>'); // Clear old data
    $.getJSON('admin-server.php?s=general', function(data) {
        $.ajax({
            url : 'js/templates/general.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#c_general', content).html(html);
                
                $('#btnSetLatLong').bind('click',function(){
                	$('#content').append('<div id="mapsDialog"></div>');
					var lat = $("input[name=latitude]").val();
					var long = $("input[name=longitude]").val();
	               	 $.ajax({
	         	 		url : 'js/templates/gmaps.hb',
	         	 		success : function(source) {
	         	 			var template = Handlebars.compile(source);
	                        var html = template({
	                        	'lat' : lat,
	                            'long': long
	                        });
	         	 			$('#mapsDialog').html(html);
	         	 			$('#btnGeneralMapsOk').bind('click', function(){
	         	 				$("input[name=latitude]").val($('#mapsLat').val());
	        					$("input[name=longitude]").val($('#mapsLong').val());
	        					$("#btnGeneralSubmit").trigger("click");
	        					$("#dialog-modal").dialog('close'); 
	                        });
	         	 		},
	         	 		dataType : 'text'
	         	 	});
                });
                $('#btnGeneralSubmit').bind('click', function(){
                    var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(){
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });
                    });
                });
            },
            dataType : 'text'
        });        
    });
    $.getJSON('admin-server.php?s=communication', function(data) {
        $.ajax({
            url : 'js/templates/communication.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#c_communication', content).html(html);
                
                $('#btnCommunicationSubmit').bind('click', function(){
                    var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(){
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });
                    });
                });
            },
            dataType : 'text'
        });
        // We don't want to first show the below block, so load it after the communication data
        $.ajax({
    		url : 'js/templates/security.hb',
    		success : function(source) {
    			var template = Handlebars.compile(source);
    			var html = template();
    			$('#c_security', content).html(html);
    			
    			$('#btnSecuritySubmit').bind('click', function(){
    				var data = $(this).parent().parent().serialize();
    				$.post('admin-server.php', data, function(result){
    					$.pnotify({
    						title: result.title,
    						text: result.text
    					});						
    				});
    			});
    		},
    		dataType : 'text'
    	});  
    });
	      
}

function init_inverters(selected_inverterId) {
    $.getJSON('admin-server.php?s=inverters', function(data) {
        $.ajax({
            url : 'js/templates/inverter_sb.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#sidebar').html(html);
                
                if (selected_inverterId) {
                    load_inverter(selected_inverterId);
                } else {
                    $('#content').html("<br /><h2>Choose or create an inverter on the right side --></h2>");                    
                }
                
                $('.inverter_select').each(function(){
                    var button = $(this);
                    var inverterId = button.attr('id').split("_")[1];
                    
                    button.bind('click', function() {
                        load_inverter(inverterId);
                    }); 
                });
            },
            dataType : 'text'
        });        
    });
}

function load_inverter(inverterId) {
    $.getJSON('admin-server.php?s=inverter&id='+inverterId, function(inv_data) {
        $.ajax({
            url : 'js/templates/inverter.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : inv_data
                });
                
                $('#content').html(html);
                
                $('#btnInverterSubmit').bind('click', function(){
                	$('#btnInverterSubmit').attr("disabled", "disabled");
                    var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(result){
                        init_inverters(result.id);                        
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });
                        $('#btnInverterSubmit').removeAttr("disabled");
                    });
                });
                
                var handle_panel_submit = function() {
                    var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(){
                        load_inverter(inverterId);
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });
                    });                                         
                };
                
                $('.panel_submit').bind('click', handle_panel_submit);
                
                $('#btnNewPanel').bind('click', function(){
                    $.getJSON('admin-server.php?s=panel&id=-1&inverterId='+inverterId, function(data) {
                        $.ajax({
                            url : 'js/templates/panel.hb',
                            success : function(source) {
                                var template = Handlebars.compile(source);
                                var html = template({
                                    'data' : data
                                });
                                $('#new_panels').html(html);
                                $('.panel_submit').unbind('click');
                                $('.panel_submit').bind('click', handle_panel_submit);
                            },
                            dataType : 'text'
                        }); 
                    });
                });
                
                init_KWHcalc(inv_data);
                
            },
            dataType : 'text'
        });        
    });
}

function init_grid() {
    alert("grid");
    
}

function init_mail() {
    $('#sidebar').html("");
    var content = $('#content');
    content.html('<div id="c_mail"></div><div id="c_smtp"></div>');
    $.getJSON('admin-server.php?s=email', function(data) {
        $.ajax({
            url : 'js/templates/email.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#c_mail', content).html(html);
                
                $('#btnEmailSubmit').bind('click', function(){
                    var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(){
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });
                    });
                });

                $('#btnSmtpSubmit').bind('click', function(){
                    var data = $(this).parent().parent().serialize();
                    $.post('admin-server.php', data, function(){
                        $.pnotify({
                            title: 'Saved',
                            text: 'You\'re changes have been saved.'
                        });                        
                    });
                });
                
                $('#btnEmailTest').bind('click', function() {
                    var senddata = $(this).parent().serialize();
                    $.post('admin-server.php', senddata, function(data){
                        if (data.result == true) {
                            $.pnotify({
                                title: 'Succes',
                                text: 'Email was send, check your inbox.',
                                type: 'success'                                    
                            });                                  
                        } else {
                            $.pnotify({
                                title: 'Error',
                                text: 'Email was not send, check the settings.<br />' + data.message,
                                type: 'error'
                            });      
                        }
                    });
                });
            },
            dataType : 'text'
        });        
    });
}

function init_testpage() {
    $('#sidebar').html("");
    $.getJSON('admin-server.php?s=test', function(data) {
        $.ajax({
            url : 'js/templates/testpage.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#content').html(html);
            },
            dataType : 'text'
        });        
    });
        
}

function init_updatepage(experimental) {
    if (typeof experimental === 'undefined' ) {
        experimental = false;
    }
    $.getJSON('admin-server.php?s=updater-getversions&experimental=' + experimental, function(data) {
        if (data.result === false) {
            $.ajax({
                url : 'js/templates/updater-problems.hb',
                success : function(source) {
                    var template = Handlebars.compile(source);
                    var html = template({ 'data' : data });
                    $('#content').html(html);
                },
                dataType : 'text'
            });
        } else {
            $.ajax({
                url : 'js/templates/updater-experimental.hb',
                success : function(source) {
                    var template = Handlebars.compile(source);
                    var html = template({'experimental' : experimental });
                    $('#sidebar').html(html);
                    
                    $('#chkExperimental').bind('click', function(){
                        var checked = $(this).is(':checked');
                        init_updatepage(checked);
                    });
                },
                dataType : 'text'
            });
            
            $.ajax({
                url : 'js/templates/updater-versions.hb',
                success : function(source) {
                    var template = Handlebars.compile(source);
                    var html = template({ 'data' : data });
                    $('#content').html(html);
                    
                    $('#btnUpdateSubmit').bind('click', function(){
                        var button = $(this);
                        button.attr('disabled', true);
                        var updateNotice = $.pnotify({
                            title: 'Update',
                            text: 'Busy with updating, please wait for this message to dissapear.',
                            nonblock: true,
                            hide: false,
                            closer: false,
                            sticker: false
                        });
                        
                        startUpdaterMonitor(updateNotice, button);

                        var data = $(this).parent().serialize();
                        $.post('admin-server.php', data, function(updateresult){
                            console.log(updateresult.result);
                            if (updateresult.result === true) {
                                $.pnotify({
                                    title: 'Update',
                                    text: 'The update is ready.',
                                    type: 'success'
                                });
                            } else {
                                $.pnotify({
                                    title: 'Update',
                                    text: 'The update failed. <br />' + updateresult.error,
                                    type: 'error'
                                });
                            }
                            if (updateNotice.pnotify_remove) updateNotice.pnotify_remove();
                            button.attr('disabled', false);
                        });
                    });
                },
                dataType : 'text'
            });
        }
    });
}

function startUpdaterMonitor(updateNotice, button) {
	$('#content').append('<div id="updaterMonitor"></div>');
	
	
	$.ajax({
         url : 'js/templates/updater-status.hb',
         success : function(source) {
             var template = Handlebars.compile(source);
             var refreshIntervalId = 0;
             
             var monitor = function() {
            	 $.getJSON("../tmp/update.json", function(data){
            		 var html = template({'data' : data });
            		 $('#updaterMonitor').html(html);
            		 
            		 if (data.state != 'busy') {
            			 clearInterval(refreshIntervalId);
            			 if (updateNotice.pnotify_remove) updateNotice.pnotify_remove();
            			 button.attr('disabled', false);
            		 }
            	 });
             };
             
             refreshIntervalId = setInterval(monitor, 2000); // 2 seconds
         },
         dataType : 'text'
     });
	
	
}


function KWHcalc(object, Perc, Month){
	if ($("#totalKWHProd").val()>=12){
		if($(object).attr('id') == 'totalKWHProd'){
			var totalProd = $("#totalKWHProd").val();
			$(Perc).each(function(index) {
					var kwh = totalProd * Perc[index]/100;
					$("#"+Month[index]+"PER").val(Math.round(Perc[index]*10)/10);
					$("#"+Month[index]+"KWH").val(Math.round(kwh*1)/1);
					setPercentBar(Month[index],kwh,totalProd);
			});
		}else{
			sumKWHtotal();
			var totalProd = $("#totalKWHProd").val();
			$(Perc).each(function(index) {
					var kwh = $("#"+Month[index]+"KWH").val();
					var monthPER= Math.round(((kwh/totalProd)*100)*100)/100;
					$("#"+Month[index]+"PER").val(monthPER);
					setPercentBar(Month[index],kwh,totalProd);
			});
		}
	}
}

function sumKWHtotal(){
	var sum = $("input[id$='KWH']").sum();
	$("#totalKWHProd").val(sum);
}

function setPercentBar(month,KWH,KWHTotal) {
	var monthBarHeight = $("p[class=monthBAR]").height();
	var monthPER= Math.round(((KWH/KWHTotal)*100)*100)/100;
	var top = monthBarHeight-monthBarHeight/100*monthPER*4;
	var height =monthBarHeight/100*monthPER*4;

	if (height > monthBarHeight){height = monthBarHeight;top = 0;}
	
	$("img[id="+month+"BAR]").css('top', top);
	$("img[id="+month+"BAR]").css('height',height);
}