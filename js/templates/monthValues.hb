<div id="todayPosts">
<div class="columns">
<h3>{{lang.month}}</h3>
The values below are grouped by month of the selected year.
<br>
<div id="pageMonthDateFilter">
</div>
<br><br>
<div class="col posts">
	<div class="column span-6 first">
		<h3>Max Grid Power</h3>
		<div class="post">
			<div id="monthPowerAcc" class="column span-9 last accordion">

			{{#each data.monthData.data.maxPower}}	
			<h3 style="margin:0px;">Inv. 1</h3><div>
    			<div class="column span-2 first">{{../lang.inv}}</div>
				<div class="column span-2" style="text-align:right;">{{../lang.watt}}</div>
				<div class="column span-3" style="text-align:center;">{{../lang.date}}</div>
    			{{#each this}}
		    		<div class="column span-2 first">{{this.INV}}</div>
		    		<div class="column span-2" style="text-align:right;">{{this.maxGP}}</div>
		    		<div class="column span-3" last" style="text-align:right;">{{this.date}}</div>
			    	{{/each}}
			   	</div>
		    {{/each}}
		    </div>
	    </div>
	</div>
</div>

<div class="col posts">
	<div class="column span-10 last">
		<h3>kWh</h3>
		<div class="post">
			<div id="monthPowerAcc" class="column span-9 last accordion">
			{{#each data.monthData.data.maxEnergy}}
	    		<h3 style="margin:0px;">Inv. 1</h3>
	    		<div>
    				<div class="column span-2 first">{{../lang.inv}}</div>
					<div class="column span-2" style="text-align:right;">{{../lang.kwh}}</div>
					<div class="column span-3" style="text-align:center;">{{../lang.date}}</div>
		
    				{{#each this}}
		    			<div class="column span-2 first">{{this.INV}}</div>
			    		<div class="column span-2" style="text-align:right;">{{this.KWH}}</div>
			    		<div class="column span-3" last" style="text-align:right;">{{this.date}}</div>
				    {{/each}}
			   	</div>
		    {{/each}}
			</div>
	    </div>
	</div>
</div>
<div class="cl"></div>
<div class="col posts">
	<div class="column span-8 first">
		<div class="post">
		<h3>The best/worst day</h3>
		<div id="monthPowerAcc" class="column span-9 last accordion">
		{{#each data.monthData.data.minMaxEnergy}}
			<h3 style="margin:0px;">Inv. 1</h3>
			<div>
				<div class="column span-2" style="text-align:right;">&nbsp;</div>
				<div class="column span-2" style="text-align:right;">{{../lang.watt}}</div>
				<div class="column span-3 last">{{../lang.date}}</div>
				
				<div class="column span-2">The Best:</div>
				<div class="column span-2" style="text-align:right;">{{this.maxEnergy.kWh}}</div>
				<div class="column span-3 last">{{this.maxEnergy.date}}</div>
				
    			<div class="column span-2">The Worst:</div>
				<div class="column span-2" style="text-align:right;">{{this.minEnergy.kWh}}</div>
				<div class="column span-3 last">{{this.minEnergy.date}}</div>
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