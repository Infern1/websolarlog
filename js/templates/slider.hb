<!-- Slider -->
<div id="tabs" class="tabs-bottom" style="height:350px">
	<ul>
		{{#data.sliders}}
		<li><a href="#tabs-{{this.position}}" id="tab-{{this.position}}" name="tab-{{this.graphName}}">{{this.graphName}}</a></li>
		{{/data.sliders}}
	</ul>
	{{#data.sliders}}
		<div id="tabs-{{this.position}}" style="height:350px;">
        	<div id="graph{{this.graphName}}Content" style="height:300px; width:890px;" ></div>
       	</div>
		{{/data.sliders}}
</div>
<div id="logger"></div>
<!-- END Slider -->