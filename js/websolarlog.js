// Create an handle bar ifCond equals statement
Handlebars.registerHelper('ifCond', function(v1, v2, options)
{
    if (v1 == v2) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});

// WSL class
var WSL = {
    api : {},
    init_events : function(invtnum, divId) {
        // Retrieve the error events 
        WSL.api.getEvents(invtnum, function(data)
        {
            $.ajax({
                url : 'js/templates/events.hb',
                success : function(source)
                {
                    var template = Handlebars.compile(source);
                    var html = template({ 'data' : data });
                    $(divId).html(html);
                },
                dataType : 'text'
            });
        });
    },
    init_liveData : function(invtnum, divId) {
        // Retrieve the error events 
        WSL.api.getLiveData(invtnum, function(data)
        {
            if (data.liveData.success) {
                $.ajax({
                    url : 'js/templates/livedata.hb',
                    success : function(source)
                    {
                        var template = Handlebars.compile(source);
                        var html = template({ 'data' : data.liveData });
                        $(divId).html(html);
                    },
                    dataType : 'text'
                });                
            } else {
                alert(data.liveData.message);
            }
        });
    },
    init_plantinfo : function(invtnum, divId) {
        // Retrieve the error events 
        WSL.api.getPlantInfo(invtnum, function(data)
        {
            $.ajax({
                url : 'js/templates/plantinfo.hb',
                success : function(source)
                {
                    var template = Handlebars.compile(source);
                    var html = template({ 'data' : data });
                    $(divId).html(html);
                },
                dataType : 'text'
            });
        });
    },
    init_menu : function(divId) {
        WSL.api.getMenu(function(data)
        {
            $.ajax({
                url : 'js/templates/menu.hb',
                success : function(source)
                {
                    var template = Handlebars.compile(source);
                    var html = template({ 'data' : data });
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
    }
};

// api class
WSL.api.programdayfeed = function(invtnum, success) {
    $.getJSON("programs/programdayfeed.php", {invtnum : invtnum}, success);
};

WSL.api.getEvents = function(invtnum, success) {
    $.getJSON("server.php", { method : 'getEvents', 'invtnum' : invtnum, }, success);
};

WSL.api.getLiveData = function(invtnum, success) {
    $.getJSON("server.php", { method : 'getLiveData', 'invtnum' : invtnum, }, success);
};

WSL.api.getPlantInfo = function(invtnum, success) {
    $.getJSON("server.php", { method : 'getPlantInfo', 'invtnum' : invtnum, }, success);
};

WSL.api.getLanguages = function(success) {
    $.getJSON("server.php", { method : 'getLanguages' }, success);
};

WSL.api.getMenu = function(success) {
    $.getJSON("server.php", {method : 'getMenu'}, success);
};