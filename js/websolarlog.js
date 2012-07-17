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

WSL.api.getLanguages = function(success) {
    $.getJSON("server.php", { method : 'getLanguages' }, success);
};

WSL.api.getMenu = function(success) {
    $.getJSON("server.php", {method : 'getMenu'}, success);
};