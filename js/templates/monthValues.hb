<div id="todayPosts">
<div class="columns">
<a name="{{lang.Month}}">
<h3><a href="#{{lang.Month}}">{{lang.Month}}</a> <a href="#top">^</a></h3>
{{lang.valuesGroupedByDayMonthText}}
<div id="pageMonthDateFilter">
</div><br>
<div class="col posts">
	<div class="column span-22 first">
		<h3>Max Grid Power</h3>
		<div class="post">
			<div id="monthPowerAcc" class="column span-22 last accordion">
			{{#each data.monthData.data.maxPower}}	
			<h3>Inv. {{this.INV}}</h3>
				<div class="innerAccordionPeriod">
	    			<div class="column span-5 first">{{../lang.inv}}</div>
					<div class="column span-4" style="text-align:right;">{{../lang.watt}}</div>
					<div class="column span-8" style="text-align:center;">{{../lang.date}}</div>
    				{{#each this}}
			    		<div class="column span-5 first">{{this.INV}}</div>
		    			<div class="column span-4" style="text-align:right;">{{this.maxGP}}</div>
		    			<div class="column span-8" last" style="text-align:right;">{{this.date}}</div>
			   		{{/each}}
				</div>
		    {{/each}}
		    </div>
	    </div>
	</div>
</div>

<div class="col posts">
	<div class="column span-22 last">
		<h3>kWh</h3>
		<div class="post">
			<div id="monthPowerAcc" class="column span-22 last accordion">
			{{#each data.monthData.data.maxEnergy}}
	    		<h3 style="margin:0px;">Inv. 1</h3>
	    		<div class="innerAccordionPeriod">
    				<div class="column span-5 first">{{../lang.inv}}</div>
					<div class="column span-4" style="text-align:right;">{{../lang.kwh}}</div>
					<div class="column span-8" style="text-align:center;">{{../lang.date}}</div>
		
    				{{#each this}}
		    			<div class="column span-5 first">{{this.INV}}</div>
			    		<div class="column span-4" style="text-align:right;">{{this.KWH}}</div>
			    		<div class="column span-8" last" style="text-align:right;">{{this.date}}</div>
				    {{/each}}
			   	</div>
		    {{/each}}
			</div>
	    </div>
	</div>
</div>
<div class="cl"></div>
<div class="col posts">
	<div class="column span-22 first">
		<div class="post">
		<h3>The best/worst day</h3>
		<div id="monthPowerAcc" class="column span-22 last accordion">
		{{#each data.monthData.data.minMaxEnergy}}
			<h3 style="margin:0px;">Inv. 1</h3>
			<div class="innerAccordionPeriod">
				<div class="column span-3" style="text-align:right;">&nbsp;</div>
				<div class="column span-3" style="text-align:right;">{{../lang.watt}}</div>
				<div class="column span-9 last">{{../lang.date}}</div>
				
				<div class="column span-4">The Best:</div>
				<div class="column span-3" style="text-align:right;">{{this.maxEnergy.kWh}}</div>
				<div class="column span-9 last">{{this.maxEnergy.date}}</div>
				
    			<div class="column span-4">The Worst:</div>
				<div class="column span-3" style="text-align:right;">{{this.minEnergy.kWh}}</div>
				<div class="column span-9 last">{{this.minEnergy.date}}</div>
			</div>
	    {{/each}}
	    </div>
		</div>
	</div>
</div>	
<div class="col posts">
	<div class="column span-22 last">
		<h3>..</h3>
		<div class="post">
...
	    </div>
	</div>
</div>
	
<div class="cl"></div>

</div>
</div>
</div>