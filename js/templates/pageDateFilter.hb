devices:<select id="invtnum">
	{{#each data.devices}}
	<option value="{{this.id}}">{{this.name}}</option>
	{{/each}}
</select>
{{lang.selectPeriod}}:<input type="text" id="datePickerPeriod" style="position: relative; z-index: -1000;">