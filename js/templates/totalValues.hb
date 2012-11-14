<div class="widget">
	<div style="height:180px;">
		<h3>{{lang.someFigures}}</h3>
		<div  class="column span-6">
			<div class="column span-2 first">{{lang.by}}</div>
	    	<div class="column span-2" style="text-align:right;">{{lang.total}}</div>
	    	<div class="column span-2 last" style="text-align:right;">{{lang.averages}}</div>
	    	
			<div class="column span-2 first"><a href="#">{{lang.today}}</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.summary.totalEnergyToday.0.KWH}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.summary.avgEnergyToday}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">{{lang.week}}</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.summary.totalEnergyWeek.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.summary.avgEnergyWeek}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">{{lang.month}}</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.summary.totalEnergyMonth.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.summary.avgEnergyMonth}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">{{lang.year}}</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.summary.totalEnergyYear.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.summary.avgEnergyYear}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">{{lang.overall}}</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.summary.totalEnergyOverall.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.summary.avgEnergyOverall}}&nbsp;</div>
	    	
	    	<div class="column span-2 first"><a href="#">{{lang.overallTotal}}</a></div>
	    	<div class="column span-2" style="text-align:right;">{{data.summary.totalEnergyOverallTotal}}&nbsp;</div>
	    	<div class="column span-2 last" style="text-align:right;">{{data.summary.totalEnergyOverallTotal}}&nbsp;</div>
	    </div>
	    {{lang.allFiguresAreInKWH}}<br>
	    {{lang.overallTotalText}}
	</div>
	<!--<span class="read-more"><a href="#" title="Read More">»&nbsp;{{lang.more}}</a></span>-->
</div>