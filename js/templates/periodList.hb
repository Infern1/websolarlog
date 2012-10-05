Jquery Accordion per month?
<ul>
<li style="float:left;width:60px;">kwh</li><li>time</li>
{{#data.dayData.data.Month}}
	<li style="float:left;width:60px;">{{this.kwh}}</li><li>{{this.time}}</li>
{{/data.dayData.data.Month}}
</ul>