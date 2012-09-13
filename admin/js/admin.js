$(function()
{
    init_menu();
    init_KWHcalc();
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
    $("#btnMail").bind('click', function()
    {
        init_mail();
    });
    $("#btnTestPage").bind('click', function()
    {
        init_testpage();
    });
}

function init_general() {
    alert("general");
}

function init_inverters() {
    alert("inverters");
    
}

function init_grid() {
    alert("grid");
    
}

function init_mail() {
    alert("mail");
    
}

function init_testpage() {
    alert("test page");
    
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
