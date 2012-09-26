<!-- Slider -->
<div id="slider">
	<ul>
		{{#data.sliders}}
		<li>
			<div id="graph{{this.graphName}}">
        		<div id="graph{{this.graphName}}Content" style="height: 250px;"></div>
        	</div>
		</li>
		{{/data.sliders}}
	</ul>
	<div id="slider-nav">
		<ul>
		{{#data.sliders}}
		<li><a title="{{this.graphName}}" href="#">{{this.graphName}}</a></li>
		{{/data.sliders}}
		</ul>
	</div>
</div>
<!-- END Slider -->