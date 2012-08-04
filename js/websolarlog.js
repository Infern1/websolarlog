// Create an handle bar ifCond equals statement
Handlebars.registerHelper('ifCond', function(v1, v2, options)
{
    if (v1 == v2) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});

// calculate the JS parse time //
beforeLoad = (new Date()).getTime();
window.onload = pageLoadingTime;
function pageLoadingTime()
{
    afterLoad = (new Date()).getTime();
    secondes = (afterLoad - beforeLoad) / 1000;
    document.getElementById("JSloadingtime").innerHTML = secondes;
}

// WSL class
var WSL = {
    api : {},
    init_events : function(invtnum, divId)
    {
        // Retrieve the error events
        WSL.api.getEvents(invtnum, function(data)
        {
            $.ajax({
                url : 'js/templates/events.hb',
                success : function(source)
                {
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
    init_liveData : function(invtnum, divId)
    {
        // Retrieve the error events
        WSL.api.getLiveData(invtnum, function(data)
        {
            if (data.liveData.success) {
                $.ajax({
                    url : 'js/templates/livedata.hb',
                    success : function(source)
                    {
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
    init_plantInfo : function(invtnum, divId)
    {
        // Retrieve the error events
        WSL.api.getPlantInfo(invtnum, function(data)
        {
            if (data.plantInfo.success) {
                $.ajax({
                    url : 'js/templates/plantinfo.hb',
                    success : function(source)
                    {
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
    init_menu : function(divId)
    {
        WSL.api.getMenu(function(data)
        {
            $.ajax({
                url : 'js/templates/menu.hb',
                success : function(source)
                {
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
    init_languages : function(divId)
    {
        // initialize languages selector on the given div
        WSL.api.getLanguages(function(data)
        {
            $.ajax({
                url : 'js/templates/languageselect.hb',
                success : function(source)
                {
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
    createGraphToday : function(divId, invtnum)
    {
        var data=[];
        var alreadyFetched = [];
        
        var options = {
                title : "Today",
                axes : {
                    xaxis : {
                        label : "X Axis",
                        renderer: $.jqplot.DateAxisRenderer,
                        pad : 0,
                        tickInterval: '40', 
                        tickOptions : {
                            formatString: '%H:%M'
                            }
                    },
                    yaxis : {
                        label : "Gem.Vermogen (W)"
                    }
                },
                cursor:{
                    show: true,
                    zoom:true,
                    showTooltip:false
              } 
        };
        
        // then fetch the data with jQuery
        function onDataReceived(series) {
            // let's add it to our current data
            if (!alreadyFetched[series.label]) {
                alreadyFetched[series.label] = true;
                data.push(series.data);
            }
        
            // and plot all we got
            $.jqplot(divId, data, options);
        }
        
        // find the URL in the link right next to us
        var dataurl = "server.php?method=getTodayValues&invtnum=" + invtnum;
        
        $.ajax({
            url: dataurl,
            method: 'GET',
            dataType: 'json',
            success: onDataReceived
        }); 
        

    }

};

// api class
WSL.api.programdayfeed = function(invtnum, success)
{
    $.getJSON("programs/programdayfeed.php", {
        invtnum : invtnum
    }, success);
};

WSL.api.getEvents = function(invtnum, success)
{
    $.getJSON("server.php", {
        method : 'getEvents',
        'invtnum' : invtnum,
    }, success);
};

WSL.api.getLiveData = function(invtnum, success)
{
    $.getJSON("server.php", {
        method : 'getLiveData',
        'invtnum' : invtnum,
    }, success);
};

WSL.api.getPlantInfo = function(invtnum, success)
{
    $.getJSON("server.php", {
        method : 'getPlantInfo',
        'invtnum' : invtnum,
    }, success);
};

WSL.api.getLanguages = function(success)
{
    $.getJSON("server.php", {
        method : 'getLanguages'
    }, success);
};

WSL.api.getMenu = function(success)
{
    $.getJSON("server.php", {
        method : 'getMenu'
    }, success);
};