<div id="mainSummary" class="ui-widget ui-widget-content ui-corner-all" >

<div class="column span">
<div class="span" style="text-align:center;"><span title="hover on the figures for more info">info</span></div>
<div class="cl"></div>
	<div class="column span-4 horizontalLine">
		<br>
		Power (kWh)<br>
		CO2 (kg)<br>
		Money ({{data.totals.moneySign}})<br>
	</div>
</div>

<div class="column span">
<div class="span" style="text-align:center;">Power:</div>
<div class="cl"></div>

{{#if_gt data.totals.metering.returnKWH compare=0}}
<div class="column span-4 horizontalLine">Usage:<br>
	<span class="tooltip" title="We generated {{data.totals.production.KWH}} of that we used {{data.totals.usedBeforeMeterKWH}} in the house, so we returned {{data.totals.metering.returnKWH}} to the grid">{{data.totals.totalUsagekWh}}</span><br>
	{{data.totals.totalUsageKWHCO2}}<br>
	<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.totalUsagekWh}} = {{data.totals.moneySign}} {{data.totals.totalUsageKWHCosts}}"> {{data.totals.totalUsageKWHCosts}}</span>
</div>
{{/if_gt}}

<div class="column span-4 horizontalLine">Generated:<br>
	{{data.totals.production.KWH}}<br>
	{{data.totals.production.CO2avoid}}<br>
	<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.production.KWH}} = {{data.totals.moneySign}} {{data.totals.production.costs}}"> {{data.totals.production.costs}}</span>
</div>

{{#if_gt data.totals.metering.returnKWH compare=0}}
<div class="column span-4 verticalLine">To grid:<br>
	<span title="We returned this to the grid, so somebody else can use it.">{{data.totals.metering.returnKWH}}</span><br>
	<span title="Somebody else is getting your carbon free power :D">{{data.totals.metering.returnCO2}}<br>
	<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.metering.returnKWH}} = {{data.totals.moneySign}} {{data.totals.metering.returnCosts}}"> {{data.totals.metering.returnCosts}}</span>
</div>
{{/if_gt}}
</div>

{{#if_gt data.totals.metering.returnKWH compare=0}}
<div class="column span">
	<div class="span" style="text-align:center;">Gas:</div>
	<div class="cl"></div>
	<div class="column span-4 verticalLine">
		used:<br>
		{{data.totals.metering.gasUsage}} m3<br>
		{{data.totals.metering.gasUsageCO2}}<br>
		<span title="m3 gas per degree day;
		(18 - {{data.totals.weather.avgTemp}} = {{data.totals.weather.degreeDays}} degree days)">m3/dd: {{data.totals.m3PerdegreeDays}}</span>
	</div>
</div>
{{/if_gt}}

<div class="column span">
<div class="span" style="text-align:center;">CO2:</div>
<div class="cl"></div>
	<div class="column span-4 verticalLine">
	House:<br>
		Trees:<span title="The household produces {{data.totals.householdCO2}} CO2 and a tree consumses {{data.totals.co2CompensationTree}} grams of CO2 a day so we need {{data.totals.totalUsageCO2Trees}} trees to compensate this">{{data.totals.householdTrees}}</span><br>
		<span title="Used Power is {{data.totals.totalUsageKWHCO2}} CO2 {{#if_gt data.totals.metering.gasUsageCO2 compare=0}}combined with {{data.totals.metering.gasUsageCO2}} CO2 for gas {{/if_gt}}gives a total of {{data.totals.householdCO2}} for the household">{{data.totals.householdCO2}}</span><br>
	</div>
</div>
{{#if_gt data.totals.weather.pressure compare=0}}
<div class="column span">
<div class="span" style="text-align:center;">Weather:</div>
<div class="cl"></div>
	<div class="column span-11 verticalLine">
		<canvas id="layer1" style="z-index: 0;"></canvas>
		</div>
</div>
{{/if_gt}}
<div class="cl"></div>