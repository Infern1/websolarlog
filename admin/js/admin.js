$(function()
{
    init_menu();
    init_KWHcalc();
    init_general(); // First admin item
});

/*
 * Init var/array
 */
var Perc=new Array(2,5,7,10,12,14,14,12,10,7,5,2);
var Month=new Array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');



/**
 *  Init PowerPreset value's
 */
function init_KWHcalc(){
    $("input[id$='KWH']").bind("keyup", function(){
    	object = this;
    	KWHcalc(object,Perc,Month);
    });
    $("#totalKWHProd").bind("keyup", function(){
    	object = this;
    	KWHcalc(object,Perc,Month);
    });
    
}

/**
 * Init menu buttons
 */
function init_menu()
{
    $("#btnGeneral").bind('click', function()
    {
        init_general();
    });
    $("#btnInverters").bind('click', function()
    {
        init_inverters();
    });
    $("#btnGrid").bind('click', function()
    {
        init_grid();
    });
    $("#btnEmail").bind('click', function()
    {
        init_mail();
    });
    $("#btnTestPage").bind('click', function()
    {
        init_testpage();
    });
}

function init_general() {
    $('#sidebar').html("");
    var content = $('#content');
    content.html('<div id="c_general"></div><div id="c_communication"></div>'); // Clear old data
    $.getJSON('admin-server.php?s=general', function(data) {
        $.ajax({
            url : 'js/templates/general.hb',
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
    $.getJSON('admin-server.php?s=communication', function(data) {
        $.ajax({
            url : 'js/templates/communication.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#c_communication', content).html(html);
            },
            dataType : 'text'
        });        
    });
}

function init_inverters() {
    $.getJSON('admin-server.php?s=inverters', function(data) {
        $.ajax({
            url : 'js/templates/inverter_sb.hb',
            success : function(source) {
                var template = Handlebars.compile(source);
                var html = template({
                    'data' : data
                });
                $('#content').html("<br /><h2>Choose an inverter on the right side --></h2>");
                $('#sidebar').html(html);
                
                $('.inverter_select').each(function(){
                    var button = $(this);
                    var inverterId = button.attr('id').split("_")[1];
                    
                    button.bind('click', function(){
                        $.getJSON('admin-server.php?s=inverter&id='+inverterId, function(inv_data) {
                            $.ajax({
                                url : 'js/templates/inverter.hb',
                                success : function(source) {
                                    var template = Handlebars.compile(source);
                                    var html = template({
                                        'data' : inv_data
                                    });
                                    $('#content').html(html);
                                },
                                dataType : 'text'
                            });        
                        });
                    });
                });
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
