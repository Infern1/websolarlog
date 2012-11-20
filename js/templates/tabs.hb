<!-- Slider -->
<div id="tabs" class="tabs-bottom" style="height:375px">
	<ul>
		{{#data.tabs}}
		<li class="ui-corner-bottom"><a href="#tabs-{{this.position}}" id="tab-{{this.position}}" name="tab-{{this.graphName}}">{{this.translation}}</a></li>
		{{/data.tabs}}
	</ul>
	<div id="pickerFilter" style="height:20px;"></div>
	{{#data.tabs}}
		<div id="tabs-{{this.position}}" class="mainTabContainer" style="height:310px;"><div id="graph{{this.graphName}}"><div id="graph{{this.graphName}}Content" style="height:285px; width:890px;" ></div></div></div>
	{{/data.tabs}}
</div>
<!-- END Slider -->
