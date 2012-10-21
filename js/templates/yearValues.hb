<div id="todayPosts">
<div class="columns"><h3>Year</h3>
The values below are grouped by month of the selected year.
<br><br>
<div class="col posts">
	<div class="column span-10 first">
	
		<h3>Max Grid Power</h3>
		<div class="post">
				<div class="column span-2 first">Inv.</div>
				<div class="column span-3">Watt</div>
				<div class="column span-3">Date</div>
				{{#each data.yearData.data.maxPower}}
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
				{{#each data.yearData.data.maxEnergy}}
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
	<div class="col posts">
	<div class="column span-8 first">
	
		
		<div class="post">
		<h3>The bestday,The worst day</h3>
		<div class="column span-2 first">Inv.</div>
		<div class="column span-3">Watt</div>
		<div class="column span-3 last">Date</div>
		

		{{#each data.yearData.data.minMaxEnergy}}
		<div class="column span-8">The Best Day:</div>
			
				<div class="column span-2 first">{{this.maxEnergy.INV}}</div>
				<div class="column span-3">{{this.maxEnergy.maxkWh}}</div>
				<div class="column span-3 last">{{this.maxEnergy.date}}</div>
			

		    <br><br>
		    <div class="column span-8">The Worst Day:</div>
			
				<div class="column span-2 first">{{this.minEnergy.INV}}</div>
				<div class="column span-3">{{this.minEnergy.minkWh}}</div>
				<div class="column span-3 last">{{this.minEnergy.date}}</div>
		    
	    {{/each}}
	    </div>
	</div>
</div>
	
<div class="col posts">
	<div class="column span-10 last">
		<h3>Total Energy</h3>
		<div class="post">
				<div class="column span-2 first">Inv.</div>
				<div class="column span-3">Watt</div>
				<div class="column span-3">Date</div>
				{{#each data.yearData.data.energy}}
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