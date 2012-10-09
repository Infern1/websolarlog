<div id="todayPosts">
<div class="columns"><h3>Month</h3>
<div class="col posts">
	<div class="column span-10 first">
	
		<h3>Max Grid Power</h3>
		<div class="post">
				<div class="column span-2 first">Inv.</div>
				<div class="column span-3">Watt</div>
				<div class="column span-3">Date</div>
				{{#each data.monthData.data.maxPower}}
					{{#each this}}
		    		<div class="column span-2 first">{{this.INV}}</div>
		    		<div class="column span-3">{{this.maxGP}} W</div>
		    		<div class="column span-3" last>{{this.date}}&nbsp;</div>
			    	{{/each}}
		    	{{/each}}
	    	</div>
		</div>
	</div>
	
<div class="col posts">
	<div class="column span-10 last">
		<h3>Max kWh</h3>
		<div class="post">
				<div class="column span-2 first">Inv.</div>
				<div class="column span-3">Watt</div>
				<div class="column span-3">Date</div>
				{{#each data.monthData.data.maxEnergy}}
					{{#each this}}
		    		<div class="column span-2 first">{{this.INV}}</div>
		    		<div class="column span-3">{{this.kWh}} W</div>
		    		<div class="column span-3" last>{{this.date}}&nbsp;</div>
			    	{{/each}}
		    	{{/each}}
	    	</div>
		</div>

	</div>
	<div class="cl"></div>
</div>
</div>