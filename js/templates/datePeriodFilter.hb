<div id="pickerFilterDiv">
{{lang.inv}}:<select id="pickerInv">
<option value="0">All</option>
{{#each data.inverters}}<option value="{{this.id}}">{{this.name}}</option>{{/each}}
</select>
{{lang.periode}}:
<input type="button" id="previous" value="{{lang.previous}}">
<select id="pickerPeriod">
{{#each data.options}}<option value="{{this.value}}">{{this.name}}</option>{{/each}}
</select>
<input type="button" id="next" value="{{lang.next}}">
{{lang.date}}:<input type="text" id="datepicker" style="position: relative; z-index: 100000;"/>
<input type="hidden" id="lastCall" />
</div>
