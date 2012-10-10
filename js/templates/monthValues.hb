<div id="todayPosts">
<div class="columns"><h3>Month</h3>
<div class="col posts">
	<div class="column span-6 first">
		<h3>Max Grid Power</h3>
		<div class="post">
				<div class="column span-1 first">Inv.</div>
				<div class="column span-2" style="text-align:right;">Watt</div>
				<div class="column span-3" style="text-align:center;">Date</div>
				{{#each data.monthData.data.maxPower}}
					{{#each this}}
		    		<div class="column span-1 first">{{this.INV}}</div>
		    		<div class="column span-2" style="text-align:right;">{{this.maxGP}}</div>
		    		<div class="column span-3" last" style="text-align:right;">{{this.date}}</div>
			    	{{/each}}
		    	{{/each}}
	    	</div>
		</div>
	</div>
	
<div class="col posts">
	<div class="column span-6 last">
		<h3>Max kWh</h3>
		<div class="post">
				<div class="column span-1 first">Inv.</div>
				<div class="column span-2" style="text-align:right;">Watt</div>
				<div class="column span-3" style="text-align:center;">Date</div>
				{{#each data.monthData.data.maxEnergy}}
					{{#each this}}
		    		<div class="column span-1 first">{{this.INV}}</div>
		    		<div class="column span-2" style="text-align:right;">{{this.kWh}}</div>
		    		<div class="column span-3" last" style="text-align:right;">{{this.date}}</div>
			    	{{/each}}
		    	{{/each}}
	    	</div>
		</div>

	</div>
	<div class="cl"></div>
</div>
</div>