<div class="widget">
	<div style="height:190px;">
		<h3>{{lang.someFigures}}</h3>
		<div  class="column span-13">
			<div class="column span-3 first">{{lang.by}}</div>
	    	<div class="column span-3" style="text-align:right;">{{lang.total}}</div>
	    	<div class="column span-3 last" style="text-align:right;">{{lang.AvgDay}}</div>
	    	<div class="column span-3 last" style="text-align:right;">{{lang.KWHKWP}}</div>
	    	
			<div class="column span-3 first"><a href="#">{{lang.today}}</a></div>
	    	<div class="column span-3" style="text-align:right;">{{data.summary.totalEnergyToday.0.KWH}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.avgEnergyToday}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyBeansTodayKWHKWP}}&nbsp;</div>
	    	
	    	<div class="column span-3 first"><a href="#">{{lang.week}}</a></div>
	    	<div class="column span-3" style="text-align:right;">{{data.summary.totalEnergyWeek.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.avgEnergyWeek}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyBeansWeekKWHKWP}}&nbsp;</div>
	    	
	    	<div class="column span-3 first"><a href="#">{{lang.month}}</a></div>
	    	<div class="column span-3" style="text-align:right;">{{data.summary.totalEnergyMonth.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.avgEnergyMonth}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyBeansMonthKWHKWP}}&nbsp;</div>
	    	
	    	<div class="column span-3 first"><a href="#">{{lang.year}}</a></div>
	    	<div class="column span-3" style="text-align:right;">{{data.summary.totalEnergyYear.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.avgEnergyYear}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyBeansYearKWHKWP}}&nbsp;</div>
	    	
	    	<div class="column span-3 first"><a href="#">{{lang.overall}}</a></div>
	    	<div class="column span-3" style="text-align:right;">{{data.summary.totalEnergyOverall.0.sumkWh}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.avgEnergyOverall}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyBeansOverallKWHKWP}}&nbsp;</div>
	    	
	    	<div class="column span-3 first"><a href="#">{{lang.overallTotal}}</a></div>
	    	<div class="column span-3" style="text-align:right;">{{data.summary.totalEnergyOverallTotal}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyOverallTotal}}&nbsp;</div>
	    	<div class="column span-3 last" style="text-align:right;">{{data.summary.totalEnergyBeansOverallTotalKWHKWP}}&nbsp;</div>
	    </div>
	    <hr>
	    {{lang.allFiguresAreInKWH}}<br>
	    {{lang.overallTotalText}}
	</div>
	<!--<span class="read-more"><a href="#" title="Read More">»&nbsp;{{lang.more}}</a></span>-->
</div>