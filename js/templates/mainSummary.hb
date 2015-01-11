<div id="mainSummary" class="ui-widget ui-widget-content ui-corner-all" >

<div class="column span">
	<div class="span" style="text-align:center;">{{data.lang.power}}:</div>
	<div class="cl"></div>
	<div class="column span-5 verticalLine" style="font-size: 25px;text-align:center;">
		<span style="font-size:10px;text-align:center;position:relative;top:-5px;">{{data.lang.generated}}</span><br>
		<a href="summary.php" style="position:relative;top:-2px;" class="summaryLink"
		{{#unless_eq data.total.totalUsagekWh compare=null}}
		title="Today you used " 
		{{else}}
		title=""
		{{/unless_eq}}
		>{{data.total.production.KWH}}</a><br>
		<span style="font-size:10px;text-align:center;position:relative;top:-7px;">(kWh)</span>
		<br>
	</div>
	{{#unless_eq data.total.metering.returnKWH compare=null}}
	<div class="column span-5 verticalLine" style="font-size: 25px;text-align:center;">
		<span style="font-size:10px;text-align:center;position:relative;top:-5px;">{{data.lang.used}}</span><br>
		<a href="summary.php" style="position:relative;top:-2px;" class="summaryLink"
		{{#unless_eq data.total.totalUsagekWh compare=null}}
		title="Today you used " 
		{{else}}
		title=""
		{{/unless_eq}}
		>{{data.total.totalUsagekWh}}</a><br>
		<span style="font-size:10px;text-align:center;position:relative;top:-7px;">(kWh)</span>
		<br>
	</div>
	{{/unless_eq}}
</div>
{{#unless_eq data.total.metering.gasUsage compare=null}}
<div class="column span">
	<div class="span" style="text-align:center;">Gas:</div>
	<div class="cl"></div>
	<div class="column span-4 verticalLine" style="font-size: 25px;text-align:center;">
		<span style="font-size:10px;text-align:center;position:relative;top:-5px;">{{data.lang.used}}</span>
		<br>
		<a href="summary.php" style="position:relative;top:-2px;" class="summaryLink">{{data.total.metering.gasUsage}}</a><br>
		<span style="font-size:10px;text-align:center;position:relative;top:-7px;">(m3)</span>
	</div>
</div>
{{/unless_eq}}

<div class="column span">
	<div class="span" style="text-align:center;">{{data.lang.trees}}:</div>
	<div class="cl"></div>
	<div class="column span-4 verticalLine" style="font-size: 25px;text-align:center;">
		<span style="font-size:10px;text-align:center;position:relative;top:-5px;">{{data.lang.subscriptTrees}}</span>
		<br>
		<a href="summary.php" style="position:relative;top:-2px;" class="summaryLink">{{data.total.trees}}</a><br>
	</div>
</div>
<div class="column span">
	<div class="span" style="text-align:center;" title="{{timestampDateFormat data.total.weather.time format="DD-MM-YYYY H:mm:ss"}}">{{data.lang.weather}}:</div>
	<div class="cl"></div>
	<div class="column span-14 verticalLine">
		<canvas id="layer1" style="z-index: 0;"></canvas>
	</div>
</div>

<div class="cl"></div>