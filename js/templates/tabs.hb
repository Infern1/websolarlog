<!-- Slider -->
<div id="tabs" class="tabs-bottom" style="height:375px">
	<ul>
		{{#data.tabs}}
		<li class="ui-corner-bottom"><a href="#tabs-{{this.position}}" id="tab-{{this.position}}" name="tab-{{this.graphName}}">{{this.translation}}</a></li>
		{{/data.tabs}}
	</ul>
	<div id="pickerFilter" style="height:20px;">
		<div id="pickerFilterDiv">
		{{lang.periode}}:<select id="pickerPeriod">{{#each data.options}}<option value="{{this.value}}">{{this.name}}</option>{{/each}}</select>
		{{lang.date}}:<input type="text" id="datepicker" style="position: relative; z-index: 100000;"/>
		<input type="hidden" id="lastCall" />
		</div>
	</div>
	{{#data.tabs}}
		<div id="tabs-{{this.position}}" class="mainTabContainer" style="height:310px;"><div id="graph{{this.graphName}}"><div id="graph{{this.graphName}}Content" style="height:285px; width:890px;" ></div></div></div>
	{{/data.tabs}}
</div>
<!-- END Slider -->
<script>$(function() {
$( "#datepicker" ).datepicker();$( "#datepicker" ).datepicker( "option", "dateFormat", "dd-mm-yy" );$("#datepicker").datepicker('setDate', new Date());});</script>