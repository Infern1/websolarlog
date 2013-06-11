/* Handlebars Helpers - Dan Harper (http://github.com/danharper) */

/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

/**
 *  Following lines make Handlebars helper function to work with all
 *  three such as Direct web, RequireJS AMD and Node JS.
 *  This concepts derived from UMD.
 *  @courtesy - https://github.com/umdjs/umd/blob/master/returnExports.js
 */

(function (root, factory) {
    if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like enviroments that support module.exports,
        // like Node.
        module.exports = factory(require('handlebars'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['handlebars'], factory);
    } else {
        // Browser globals (root is window)
        root.returnExports = factory(root.Handlebars);
    }
}(this, function (Handlebars) {
    /**
     * If Equals
     * if_eq this compare=that
     */
    Handlebars.registerHelper('if_eq', function(context, options) {
        if (context == options.hash.compare)
            return options.fn(this);
        return options.inverse(this);
    });

    /**
     * Unless Equals
     * unless_eq this compare=that
     */
    Handlebars.registerHelper('unless_eq', function(context, options) {
        if (context == options.hash.compare)
            return options.inverse(this);
        return options.fn(this);
    });


    /**
     * If Greater Than
     * if_gt this compare=that
     */
    Handlebars.registerHelper('if_gt', function(context, options) {
    	if(options.hash.compare){
    		options.hash.compare= options.hash.compare.replace(",",".");
    	}
    	
        if (context > options.hash.compare)
            return options.fn(this);
        return options.inverse(this);
    });

    /**
     * Unless Greater Than
     * unless_gt this compare=that
     */
    Handlebars.registerHelper('unless_gt', function(context, options) {
        if (context > options.hash.compare)
            return options.inverse(this);
        return options.fn(this);
    });


    /**
     * If Less Than
     * if_lt this compare=that
     */
    Handlebars.registerHelper('if_lt', function(context, options) {
    	options.hash.compare= options.hash.compare.replace(",",".");
        if (context < options.hash.compare)
            return options.fn(this);
        return options.inverse(this);
    });

    /**
     * Unless Less Than
     * unless_lt this compare=that
     */
    Handlebars.registerHelper('unless_lt', function(context, options) {
        if (context < options.hash.compare)
            return options.inverse(this);
        return options.fn(this);
    });


    /**
     * If Greater Than or Equal To
     * if_gteq this compare=that
     */
    Handlebars.registerHelper('if_gteq', function(context, options) {
        if (context >= options.hash.compare)
            return options.fn(this);
        return options.inverse(this);
    });

    /**
     * Unless Greater Than or Equal To
     * unless_gteq this compare=that
     */
    Handlebars.registerHelper('unless_gteq', function(context, options) {
        if (context >= options.hash.compare)
            return options.inverse(this);
        return options.fn(this);
    });


    /**
     * If Less Than or Equal To
     * if_lteq this compare=that
     */
    Handlebars.registerHelper('if_lteq', function(context, options) {
        if (context <= options.hash.compare)
            return options.fn(this);
        return options.inverse(this);
    });

    /**
     * Unless Less Than or Equal To
     * unless_lteq this compare=that
     */
    Handlebars.registerHelper('unless_lteq', function(context, options) {
        if (context <= options.hash.compare)
            return options.inverse(this);
        return options.fn(this);
    });

    /**
     * Convert new line (\n\r) to <br>
     * from http://phpjs.org/functions/nl2br:480
     */
    Handlebars.registerHelper('nl2br', function(text) {
        var nl2br = (text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
        return new Handlebars.SafeString(nl2br);
    });

	//  format an ISO date using Moment.js
	//  http://momentjs.com/
	//  moment syntax example: moment(Date("2011-07-18T15:50:52")).format("MMMM YYYY")
	//  usage: {{timestampDateFormat creation_date format="MMMM YYYY"}}
	Handlebars.registerHelper('timestampDateFormat', function(context, block) {
		var context = context * 1000;
		if (window.moment && context && moment(context).isValid()) {
			var f = block.hash.format || "MMM Do, YYYY";
			return moment(context).format(f);
		}else{
			return context;   //  moment plugin is not available, context does not have a truthy value, or context is not a valid date
		}
	});
	
	
	Handlebars.registerHelper("checkboxWithHidden", function(name, state, value) {
		if(state==1 || state==true || state == 'checked'){
			var checked = 'checked';
		}else{
			var checked = '';
		}
		if (typeof value === 'object' || value == '') {
			value = 1;
		}
		return new Handlebars.SafeString("<input type=\"hidden\" name=\""+name+"\" value=\"0\"/><input type=\"checkbox\" "+checked+" name=\""+name+"\" value=\""+value+"\"/>");
	});
    
	
	Handlebars.registerHelper("infoTooltip", function(context,block) {
		return new Handlebars.SafeString("<img src=\"images/information.png\" class=\"tooltip\" title=\""+context.hash.title+"\"/>");
	});
    
	Handlebars.registerHelper("updaterVersionsList", function(context,block) {
		var list = '<div class="span span-30">';
		$.each( context, function( key, type ) {
			list += '<ul style="list-style-type: none;"><li>'+key.capitalize()+"";
			$.each( type, function( key, value ) {
				list += '<ul><li style="list-style-type: none;"><input type="radio" name="version" value="'+value.name+'*'+value.revision+'"><span style="color:'+value.displayColor+'">&nbsp;&nbsp;'+value.displayName+'</span></input>';
				list += '<ul><li>Release date(revision):</li>';
				list += '<li style="list-style-type: none;">'+moment(value.timestamp*1000).format('DD-MM-YYYY HH:mm:ss')+'('+value.revision+')</li>';
				list += '<li>Description:</li><li style="list-style-type: none;">'+value.description+'</li></ul></ul>';
			});
			list += '</li></ul></ul>';
		});
		
		list += '</div>';
    	return new Handlebars.SafeString(list);
    });
}));