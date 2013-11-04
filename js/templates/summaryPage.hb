<div id="summaryPage" >
	<div class="column span">
		<div class="span-48">
			<div class="span-14 first" style="border:1px solid #ccc;">	
				<strong>CO2 related:</strong><br>
				Power CO2: {{data.totals.totalUsageKWHCO2}} kg<br>
				Gas CO2:{{data.totals.metering.gasUsageCO2}} kg<br>
				<span title="Used Power is {{data.totals.totalUsageKWHCO2}} CO2 {{#if_gt data.totals.metering.gasUsageCO2 compare=0}}combined with {{data.totals.metering.gasUsageCO2}} CO2 for gas {{/if_gt}}gives a total of {{data.totals.householdCO2}} for the household">{{data.totals.householdCO2}}</span><br>
			</div>
			<div class="span-14" style="border:1px solid #ccc;">
				<strong>Solar data:</strong><br>
				{{data.totals.production.KWH}} kWh generated<br>
				{{data.totals.production.CO2avoid}} kg CO2 avoided<br>
				{{data.totals.moneySign}} <span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.production.KWH}} = {{data.totals.moneySign}} {{data.totals.production.costs}}"> {{data.totals.production.costs}}</span>
			</div>
			<div class="span-14 last" style="border:1px solid #ccc;">
				{{#if_gt data.totals.weather.pressure compare=0}}
				<strong>Weather data:</strong><br>
				<canvas id="layer1" style="z-index: 0;"></canvas><br>
				Degree days:{{data.totals.weather.degreeDays}}
				{{else}}
				No weather data available.
				{{/if_gt}}<br>
			</div>
			
			
			<!-- ##################################-->
			<!-- ############ new line ############-->
			<!-- ##################################-->
			<div class="cl"></div>
			
			
			<div class="span-14 first" style="border:1px solid #ccc;">
				<strong>trees</strong><br>
				Total household CO2 usage equals the daily consumption of <span title="The household produces {{data.totals.householdCO2}} CO2 and a tree consumses {{data.totals.co2CompensationTree}} grams of CO2 a day so we need {{data.totals.totalUsageCO2Trees}} trees to compensate this"><strong>{{data.totals.householdTrees}}</strong></span> trees.
			</div>
			
			<div class="span-14" style="border:1px solid #ccc;">
				<strong>Household data:</strong><br>
				<span title="We generated {{data.totals.production.KWH}} of that we used {{data.totals.usedBeforeMeterKWH}} in the house, so we returned {{data.totals.metering.returnKWH}} to the grid">{{data.totals.totalUsagekWh}} kWh Used by appliances</span><br>
				<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.totalUsagekWh}} = {{data.totals.moneySign}} {{data.totals.totalUsageKWHCosts}}">{{data.totals.moneySign}} {{data.totals.totalUsageKWHCosts}}</span><br>
				{{#if_gt data.totals.metering.returnKWH compare=0}}
    			<strong>Gas:</strong><br>
				{{data.totals.metering.gasUsage}} m3 usage<br>
				<span title="m3 gas per degree day;(18 - {{data.totals.weather.avgTemp}} = {{data.totals.weather.degreeDays}} degree days)">m3/dd: {{data.totals.m3PerdegreeDays}}</span>
				{{/if_gt}}
			</div>
			<div class="span-14 last" style="border:1px solid #ccc;">
				{{#if_gt data.totals.metering.returnKWH compare=0}}
				<strong>Grid data:</strong><br>
				
				<span title="{{data.totals.moneySign}} {{data.totals.costkwh}} * {{data.totals.metering.returnKWH}} = {{data.totals.moneySign}} {{data.totals.metering.returnCosts}}"><strong>{{data.totals.metering.returnKWH}}</strong> kWh returned to your neighbors</span><br>
				The returned kWh's equals <span title="Somebody else is getting your carbon free power :D"><strong>{{data.totals.metering.returnCO2}}</strong> kg CO2 and <span title="We returned this to the grid, so somebody else can use it."> and {{data.totals.moneySign}} {{data.totals.metering.returnCosts}}</span>
				{{/if_gt}}
			</div>
			<div class="cl"></div>
		</div>
	</div>
</div>
