<!-- Slider -->
<div id="tabs" class="tabs-bottom" style="height:350px">
	<ul>
		{{#data.tabs}}
		<li class="ui-corner-bottom"><a href="#tabs-{{this.position}}" id="tab-{{this.position}}" name="tab-{{this.graphName}}">{{this.translation}}</a></li>
		{{/data.tabs}}
	</ul>
	{{#data.tabs}}
		<div id="tabs-{{this.position}}" style="height:350px;">
			<div id="graph{{this.graphName}}">
        		<div id="graph{{this.graphName}}Content" style="height:285px; width:890px;" ></div>
        	</div>
       	</div>
	{{/data.tabs}}
</div>
<!-- END Slider -->