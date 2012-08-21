$(function()
{
    init_menu();
});

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