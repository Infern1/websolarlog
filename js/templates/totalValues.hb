<div class="widget">
	<div style="width: 200px; height: 150px; position: relative;">
		<div id="gaugeGP" style="width: 200px; height: 150px; position: relative;"></div>
	</div>
	<div style="height:100px;">
		<h3>Totals</h3>
		<div  class="column span-3">
			<div class="column span-2 first">Today</div>
	    	<div class="column span-1 last">{{data.IndexValues.summary.day}}</div>
	    	<div class="column span-2 first">Last 7 days</div>
	    	<div class="column span-1 last">{{data.IndexValues.summary.week}}</div>
	    	<div class="column span-2 first">Last 30 days</div>
	    	<div class="column span-1 last">{{data.IndexValues.summary.month}}</div>
    
	    </div>
	</div>
	<!--<span class="read-more"><a href="#" title="Read More">»&nbsp;More</a></span>-->
</div>