<div id="todayPosts">
<div class="columns">
	<div class="col posts">
		<h3>Today</h3>
		<div class="post">
			<div class="container" >
				<div  class="column span-12">
					<div class="column span-8">
							<div class="column span-8"><h4>Max Grid Power</h4></div>
							<div class="column span-1 first">Inv.</div>
							<div class="column span-3">kWh</div>
							<div class="column span-4 last">Date</div>
				    		{{#each data.dayData.data.maxPower}}
				    			{{#each this}}
					    			<div class="column span-1 first">{{this.INV}}</div>
					    			<div class="column span-3">{{this.maxGP}} W</div>
					    			<div class="column span-4 last">{{this.date}} </div>
				    			{{/each}}
				    		{{/each}}
				    </div>
				    	
						<div class="column span-8">
					    	<div class="column span-8"><h4>Total kWh</h4></div>
							<div class="column span-1 first">Inv.</div>
							<div class="column span-3">kWh</div>
							<div class="column span-4 last">Date</div>
							{{#each data.dayData.data.maxEnergy}}
								{{#each this}}
					    			<div class="column span-1 first">{{this.INV}}</div>
					    			<div class="column span-3">{{this.kWh}} kWh</div>
					    			<div class="column span-4 last">{{this.date}}</div>
					    		{{/each}}
					    	{{/each}}
					    </div>
					    <div>
					</div>
				</div>
			</div>								
		</div>
	</div>
	<div class="col projects">
		<h3>&nbsp;</h3>
		<div class="post"><div class="column span-10">
			<div class="column span-10"><h4>History values</h4></div>
			<div class="column span-2 first">Inv.</div>
			<div class="column span-3">kWh</div>
			<div class="column span-4 last">Date</div>
			<div class="cl"></div>	
			<div id="history" class="column span-10" style="overflow:scroll;height:200px;overflow-x: hidden;overflow-y: scroll;">
	    	..
	    	</div>
	    	</div>
			<div class="cl"></div>									
		</div>							
	</div>
	<div class="cl"></div>
</div>
</div>