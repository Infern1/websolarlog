<div id="todayPosts">
<div class="columns"><h3>Month</h3>
<div class="col posts">
	<div class="column span-6 first">
		<h3>Max Grid Power</h3>
		<div class="post">
				<div id="monthPowerAcc" class="column span-8 last">
				{{#each data.monthData.data.maxPower}}
				
    				<h3 style="margin:0px;">Inv. 1</h3><div>
    			<div class="column span-1 first">Inv.</div>
				<div class="column span-2" style="text-align:right;">Watt</div>
				<div class="column span-3" style="text-align:center;">Date</div>		
    				{{#each this}}
		    		<div class="column span-1 first">{{this.INV}}</div>
		    		<div class="column span-2" style="text-align:right;">{{this.maxGP}}</div>
		    		<div class="column span-3" last" style="text-align:right;">{{this.date}}</div>
			    	{{/each}}
			    	</div>
		    	{{/each}}
		    	</div>
	    	</div>
		</div>
	</div>

<div class="cl"></div>
</div>
</div>