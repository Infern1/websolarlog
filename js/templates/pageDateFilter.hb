{{lang.selectTheMonth}}:<input type="text" id="datePickerMonth" style="position: relative; z-index: 100000;"/>
<script>
$(function() {
$("#datePickerMonth").datepicker();
$("#datePickerMonth").datepicker("option","dateFormat","dd-mm-yy");
$("#datePickerMonth").datepicker('setDate', new Date());
});
</script>