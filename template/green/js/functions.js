function init_carousel() {
	$('.field, textarea').focus(function() {
        if(this.title==this.value) {
            this.value = '';
        }
    }).blur(function(){
        if(this.value=='') {
            this.value = this.title;
        }
    });

    $("#slider").jcarousel({
    	scroll: 1,
        auto: 3,       
        wrap: 'both',       
        initCallback: mycarousel_initCallback,        
        buttonNextHTML: null,
        buttonPrevHTML: null,
        itemVisibleInCallback: {
            onAfterAnimation: function(c, o, i, s) {
                $('#slider-nav li').removeClass('active');
                $('#slider-nav li:eq('+ (i-1) +')').addClass('active');
            }
        }        
    });

    if ($.browser.msie && $.browser.version == 6) {
        DD_belatedPNG.fix('#logo a, .socials, .socials a, .middle, .bottom, #navigation a, #footer-middle, #main-top, #footer-bottom, #slider-nav');
    }

};

function mycarousel_initCallback(carousel) {
    $('#slider-nav li').bind('click', function() {      
    	$('#slider-nav li').removeClass('active');
    	$(this).addClass('active');	     		      
        carousel.scroll($.jcarousel.intval($(this).index() + 1));         
        return false;
    });
}