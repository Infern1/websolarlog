<div id="mainSummary" class="ui-widget ui-widget-content ui-corner-all" >
<div class="column span">
<div class="span" style="text-align:center;">Power:</div>
<div class="cl"></div>
{{#if_gt data.totals.metering.returnKWH compare=0}}
<div class="column span-5 verticalLine">Total usage:<br>
<span class="tooltip" title="We generated {{data.totals.production.KWH}} kWh of that we used {{data.totals.usedBeforeMeterKWH}} kWh in the house, so we returned {{data.totals.metering.returnKWH}} kWh to the grid">{{data.totals.totalUsagekWh}} kWh</span><br>
{{data.totals.totalUsageKWHCO2}} kg<br>
<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.totalUsagekWh}} = {{data.totals.moneySign}} {{data.totals.totalUsageKWHCosts}}">{{data.totals.moneySign}} {{data.totals.totalUsageKWHCosts}}</span></div>
{{/if_gt}}

<div class="column span-5 verticalLine">Generated:<br>
{{data.totals.production.KWH}} kWh<br>
{{data.totals.production.CO2avoid}} kg<br>
<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.production.KWH}} = {{data.totals.moneySign}} {{data.totals.production.costs}}">{{data.totals.moneySign}} {{data.totals.production.costs}}</span></div>

{{#if_gt data.totals.metering.returnKWH compare=0}}
<div class="column span-5 verticalLine">To grid:<br>
<span "effkWh">{{data.totals.metering.returnKWH}} kWh</span><br>
{{data.totals.metering.returnCO2}} kg<br>
<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.metering.returnKWH}} = {{data.totals.moneySign}} {{data.totals.metering.returnCosts}}">{{data.totals.moneySign}} {{data.totals.metering.returnCosts}}</span></div>
</div>
<div class="column span">
<div class="span" style="text-align:center;">Gas:</div>
<div class="cl"></div>
<div class="column span-4 verticalLine">
{{data.totals.metering.gasUsage}} m3<br>
{{data.totals.metering.gasUsageCO2}} kg<br>
<span title="m3 gas per degree day">m3/dd: {{data.totals.m3PerdegreeDays}}</span>
</div>
</div>
{{/if_gt}}

<div class="column span">
<div class="span" style="text-align:center;">CO2:</div>
<div class="cl"></div>
	<div class="column span-4 verticalLine">
		<span title="Used Power is {{data.totals.totalUsageKWHCO2}} kg CO2 combined with {{data.totals.metering.gasUsageCO2}} kg CO2 for gas gives a total of {{data.totals.householdCO2}} kg for the household">House:<br>{{data.totals.householdCO2}} kg</span><br>
		Trees:<span title="The household produces {{data.totals.householdCO2}} kg CO2 and a tree consumses {{data.totals.co2CompensationTree}} grams of CO2 a day so we need {{data.totals.totalUsageCO2Trees}} trees to compensate this">{{data.totals.householdTrees}}</span>
	</div>
</div>
{{#if_gt data.totals.weather.pressure compare=0}}
<div class="column span">
<div class="span" style="text-align:center;">Weather:</div>
<div class="cl"></div>
	<div class="column span-7 verticalLine">
		<span title="actual temp (avarage temp)">Temp:</span> {{data.totals.weather.currentTemp}}&deg;<span title="Avarage temperatue based on {{data.totals.weather.weatherSamples}} weather samples">({{data.totals.weather.avgTemp}}&deg;)</span><br>
		Degree Days: {{data.totals.weather.degreeDays}} 
		<span title="direction:{{data.totals.weather.windDirection}}, speed:{{data.totals.weather.wind_speed}}">Wind: {{data.totals.weather.windDirection}}, {{data.totals.weather.wind_speed}}m/s</span><br>
		<span title="level of clouding in percentage">clouds: {{data.totals.weather.clouds}}%</span>
	</div>
	<div class="column span-7 verticalLine">
		humidity: {{data.totals.weather.humidity}}%<br>
		pressure: {{data.totals.weather.pressure}}hPa<br>
		<span title="past 1 hour in mm, past 3 hours in mm">rain: {{data.totals.weather.rain1h}}mm, {{data.totals.weather.rain3h}} mm</span>
		
	</div>
</div>
{{/if_gt}}
<div class="cl"></div>