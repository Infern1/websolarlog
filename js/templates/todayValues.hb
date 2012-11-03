<div id="todayPosts">
	<div class="columns">
		<div class="col posts">
			<h3>{{lang.today}}</h3>
			<div class="post">
				<div class="container">
					<div  class="column span-12">
						<div class="column span-8">
							<div class="column span-8"><h4>{{lang.maxGridPower}}</h4></div>
							<div class="column span-1 first">{{lang.inv}}</div>
							<div class="column span-3" style="text-align:right;">{{lang.kwh}}</div>
							<div class="column span-4 last">{{lang.date}}</div>
				    		{{#each data.maxPower}}
				    			{{#each this}}
					    			<div class="column span-1 first">{{this.INV}}</div>
					    			<div class="column span-3" style="text-align:right;">{{this.maxGP}}</div>
					    			<div class="column span-4 last">{{this.date}} </div>
				    			{{/each}}
				    		{{/each}}
					    </div>
					    	
						<div class="column span-8">
					    	<div class="column span-8"><h4>{{lang.TotalKWh}}</h4></div>
							<div class="column span-1 first">{{lang.inv}}</div>
							<div class="column span-3" style="text-align:right;">{{lang.kwh}}</div>
							<div class="column span-4 last">{{lang.date}}</div>
							{{#each data.maxEnergy}}
								{{#each this}}
					    			<div class="column span-1 first">{{this.INV}}</div>
					    			<div class="column span-3" style="text-align:right;">{{this.kWh}}</div>
					    			<div class="column span-4 last">{{this.date}}</div>
					    		{{/each}}
					    	{{/each}}
					    </div>
					</div>
				</div>								
			</div>
		</div>
		<div class="col projects">
			<h3>&nbsp;</h3>
			<div class="post"><div class="column span-11">
				<div class="column span-11"><h4></h4></div>

				<div class="cl"></div>	
				<div id="todayHistoryAcc">
	    				<h3 style="margin:0px;">{{lang.inv}}1 </h3>
	    					<div>
				<div class="column span-2 first">{{lang.inv}}</div>
				<div class="column span-2" style="text-align:right;">{{lang.watt}}</div>
				<div class="column span-4 last">{{lang.date}}</div>	
						<div id="history" class="column span-10" style="overflow:scroll;height:200px;overflow-x: hidden;overflow-y: scroll;">
				    		{{lang.loading}}
		    			</div>
		    			
		    		</div>
		    	</div>
	    	</div>
			<div class="cl"></div>									
		</div>							
	</div>
	<div class="cl"></div>
	</div>
</div>