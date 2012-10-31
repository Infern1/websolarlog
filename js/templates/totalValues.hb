<div class="widget">
	<div style="height:180px;">
		<h3>Some Figures</h3>
		<div  class="column span-6">
			<div class="column span-2 first">By</div>
	    	<div class="column span-2" style="text-align:right;">Totals</div>
	    	<div class="column span-2 last" style="text-align:right;">Averages</div>
	    	
			<div class="column span-2 first"><a href="#">Today</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.IndexValues.summary.totalEnergyToday.0.KWH}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.IndexValues.summary.avgEnergyToday}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">Week</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.IndexValues.summary.totalEnergyWeek.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.IndexValues.summary.avgEnergyWeek}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">Month</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.IndexValues.summary.totalEnergyMonth.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.IndexValues.summary.avgEnergyMonth}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">Year</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.IndexValues.summary.totalEnergyYear.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.IndexValues.summary.avgEnergyYear}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">Overall</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.IndexValues.summary.totalEnergyOverall.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.IndexValues.summary.avgEnergyOverall}}&nbsp;</div>
	    </div>
	    All above figures are in kWh
	</div>
	<!--<span class="read-more"><a href="#" title="Read More">»&nbsp;More</a></span>-->
</div>