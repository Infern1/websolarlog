<html>
<head>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script>
function test() {
	$.ajax({
	            type: "DELETE",
	            url: "api.php/Panel/1",
	            success: function(response){
	                    alert(response.status);
	            }
	});
	
}
</script>
</head>
<body>
<button type="button" onclick="test();">test</button>
</body>
</html>