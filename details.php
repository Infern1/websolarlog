<?php
require_once("classes/classloader.php");
$template = $config->template;
$template = "green";
require_once("template/" . $template . "/header.php");
require_once("template/" . $template . "/index.php");
?>

	<script type="text/javascript">
    // Make sure the page is loaded
	$(function(){

				WSL.init_nextRelease("#columns"); // Initial load fast
				window.setInterval(function(){WSL.init_nextRelease("#columns");}, 300000); // every 3 seconds
	});
	
	// Add Filter
		WSL.api.getInverters(function(inverters){
    		$.ajax({
    			url : 'js/templates/details_filter.hb',
    			success : function(source) {
    				var template = Handlebars.compile(source);
    				var html = template({
    					'data' : inverters
    				});
    				$('#right-column').html(html);

    				var filterState = 'open';
    				$('#btnToggleFilter').bind('click', function() {
    					if (filterState == 'open') {
    						filterState = 'closed';
    						$(this).text('open');
    					    $('#filter').animate({width: 40}, 1000, function(){$('form', '#filter').hide();});
    					} else {
    						filterState = 'open';
    						$(this).text('close');
    						$('form', '#filter').show();
    						$('#filter').animate({width: 200}, 1000);
    					}
    				});

    				$('input[type="checkbox"]','#filter').bind('click', handleClick);
    			},
    			dataType : 'text'
    		});
		});

	function handleClick() {
		var id = $(this).attr('id');
	    var parts = id.split("_");

		if (parts.length == 2) {
			var type = parts[0]; // A or V or P

			alert('type=' + type + ' from Grid checked=' + $(this).is(':checked'));
		}
		if (parts.length == 3) {
			var type = parts[0]; // A or V or P
			var inverterId = parts[1]; // inverter id
			var stringId = parts[2]; // String id

			alert('type=' + type + ' inverterId=' + inverterId + ' stringId=' + stringId + ' checked=' + $(this).is(':checked'));
		}
	}
	WSL.init_details(1,"#details"); // Initial load fast
	</script>
</body>
</html>