<div id="todayPosts">
<div class="columns">
<a name="{{lang.Year}}">
<h3><a href="#{{lang.Year}}">{{lang.Year}}</a> <a href="#top">^</a></h3>
{{lang.valuesGroupedByMonthYearText}}
<div id="pageYearDateFilter">
</div><br>
<div class="col posts">
	<div class="column span-22 first">
		<h3>Max Grid Power</h3>
		<div class="post">
			<div id="yearPowerAcc" class="column span-22 last accordion">
			{{#each data.yearData.data.maxPower}}	
			<h3 style="margin:0px;">Inv. 1</h3><div class="innerAccordionPeriod">
    			<div class="column span-4 first">{{../lang.inv}}</div>
				<div class="column span-3" style="text-align:right;">{{../lang.watt}}</div>
				<div class="column span-9" style="text-align:center;">{{../lang.date}}</div>
    			{{#each this}}
		    		<div class="column span-3 first">{{this.INV}}</div>
		    		<div class="column span-3" style="text-align:right;">{{this.maxGP}}</div>
		    		<div class="column span-9" last" style="text-align:right;"><a href="month.php?date={{this.date}}">{{this.date}}</a></div>
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
			<div id="yearPowerAcc" class="column span-22 last accordion">
			{{#each data.yearData.data.maxEnergy}}
	    		<h3 style="margin:0px;">Inv. 1</h3>
	    		<div>
    				<div class="column span-4 first">{{../lang.inv}}</div>
					<div class="column span-3" style="text-align:right;">{{../lang.kwh}}</div>
					<div class="column span-9" style="text-align:center;">{{../lang.date}}</div>
		
    				{{#each this}}
		    			<div class="column span-4 first">{{this.INV}}</div>
			    		<div class="column span-3" style="text-align:right;">{{this.KWH}}</div>
			    		<div class="column span-9" last" style="text-align:right;">{{this.date}}</div>
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
		<div id="yearPowerAcc" class="column span-22 last accordion">
		{{#each data.yearData.data.minMaxEnergy}}
			<h3 style="margin:0px;">Inv. 1</h3>
			<div>
				<div class="column span-4" style="text-align:right;">&nbsp;</div>
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
	<div class="column span-10 last">
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