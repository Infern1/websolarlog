<div id="mainSummary" class="ui-widget ui-widget-content ui-corner-all" >

<div class="column span">
<div class="span" style="text-align:center;"><span title="hover on the figures for more info">info</span></div>
<div class="cl"></div>
	<div class="column span-4 horizontalLine">
		<br>
		Power (kWh)<br>
		CO2 (kg)<br>
		Money<br>
	</div>
</div>

<div class="column span">
<div class="span" style="text-align:center;">Power:</div>
<div class="cl"></div>

{{#if_gt data.totals.metering.return compare=0}}
<div class="column span-4 horizontalLine">Usage:<br>
	<span class="tooltip" title="We generated {{data.totals.production.}}  of that we used {{data.totals.usedBeforeMeter}}  in the house, so we returned {{data.totals.metering.return}}  to the grid">{{data.totals.totalUsage}} </span><br>
	{{data.totals.totalUsageCO2}}<br>
	<span title="{{data.totals.moneySign}} {{data.totals.cost}} * {{data.totals.totalUsage}} = {{data.totals.moneySign}} {{data.totals.totalUsageCosts}}">{{data.totals.moneySign}} {{data.totals.totalUsageCosts}}</span>
</div>
{{/if_gt}}

<div class="column span-4 horizontalLine">Generated:<br>
	{{data.totals.production.KWH}} <br>
	{{data.totals.production.CO2avoid}}<br>
	<span title="{{data.totals.moneySign}} {{data.totals.cost}} * {{data.totals.production.}} = {{data.totals.moneySign}} {{data.totals.production.costs}}">{{data.totals.production.costs}}</span>
</div>

{{#if_gt data.totals.metering.return compare=0}}
<div class="column span-4 verticalLine">To grid:<br>
	<span title="We returned this to the grid, so somebody else can use it.">{{data.totals.metering.return}} </span><br>
	<span title="Somebody else is getting your carbon free power :D">{{data.totals.metering.returnCO2}}<br>
	<span title="{{data.totals.moneySign}} {{data.totals.cost}} * {{data.totals.metering.return}} = {{data.totals.moneySign}} {{data.totals.metering.returnCosts}}">{{data.totals.metering.returnCosts}}</span>
</div>
{{/if_gt}}
</div>

{{#if_gt data.totals.metering.return compare=0}}
<div class="column span">
	<div class="span" style="text-align:center;">Gas:</div>
	<div class="cl"></div>
	<div class="column span-4 verticalLine">
		used:<br>
		{{data.totals.metering.gasUsage}} m3<br>
		{{data.totals.metering.gasUsageCO2}}<br>
		<span title="m3 gas per degree day">m3/dd: {{data.totals.m3PerdegreeDays}}</span>
	</div>
</div>
{{/if_gt}}

<div class="column span">
<div class="span" style="text-align:center;">CO2:</div>
<div class="cl"></div>
	<div class="column span-4 verticalLine">
	House:<br>
		Trees:<span title="The household produces {{data.totals.householdCO2}} CO2 and a tree consumses {{data.totals.co2CompensationTree}} grams of CO2 a day so we need {{data.totals.totalUsageCO2Trees}} trees to compensate this">{{data.totals.householdTrees}}</span><br>
		<span title="Used Power is {{data.totals.totalUsageCO2}} CO2 combined with {{data.totals.metering.gasUsageCO2}} CO2 for gas gives a total of {{data.totals.householdCO2}} for the household">{{data.totals.householdCO2}}</span><br>
	</div>
</div>
{{#if_gt data.totals.weather.pressure compare=0}}
<div class="column span">
<div class="span" style="text-align:center;">Weather:</div>
<div class="cl"></div>
	<div class="column span-7 horizontalLine">
		<span title="Actual temp ">Temp: {{data.totals.weather.currentTemp}}&deg;</span> <span title="Avarage temperatue based on {{data.totals.weather.weatherSamples}} weather samples">({{data.totals.weather.avgTemp}}&deg;)</span><br>
		<span title="A degree day is a measure of heating or cooling. It could be used with energy calculations on heating and cooling. (18 - {{data.totals.weather.avgTemp}} = {{data.totals.weather.degreeDays}} degree day)">Degree Days: {{data.totals.weather.degreeDays}}</span> 
		<span title="direction:{{data.totals.weather.windDirection}}, speed:{{data.totals.weather.wind_speed}}">Wind: {{data.totals.weather.windDirection}}, {{data.totals.weather.wind_speed}}m/s</span><br>
		<span title="level of clouding in percentage">clouds: {{data.totals.weather.clouds}}%</span>
	</div>
	<div class="column span-7 verticalLine">
		humidity: {{data.totals.weather.humidity}}%<br>
		pressure: {{data.totals.weather.pressure}}hPa<br>
		<span title="past 1 hour in mm, past 3 hours in mm">rain: {{data.totals.weather.rain1h}}mm, {{data.totals.weather.rain3h}} mm</span>
		<br><Br>
	</div>
</div>
{{/if_gt}}
<div class="cl"></div>