devices:<select id="invtnum">
	{{#each data.slimConfig.devices}}
	<option value="{{this.id}}">{{this.name}}</option>
	{{/each}}
</select>
{{lang.selectPeriod}}:<input type="text" id="datePickerPeriod" style="position: relative; z-index: 100000;">