<div class="post">
	<h3>Live:</h3>
	<div class="container" >
		<div  class="column span-14">
		{{#each data.IndexValues.inverters}}
			{{#each this.live}}
				<div class="column span-2 first">
					{{this.field}}
				</div>
			{{/each}}
		{{/each}}
		{{#each data.IndexValues.inverters}}
			{{#each this.live}}
				<div class="column span-2 first">
					{{this.value}}
				</div>
			{{/each}}
		{{/each}}
		<br><br>
		
	</div><div class="cl"></div>
	<br>
	<div  class="column span-6">
		{{#each data.IndexValues.inverters}}
			<div class="column span-5 first">Totals:</div>
			<div class="column span-3 first">Today</div>
		    <div class="column span-2 last">{{this.day}}</div>
		    <div class="column span-3 first">Last 7 days</div>
		    <div class="column span-2 last">{{this.week}}</div>
		    <div class="column span-3 first">Last 30 days</div>
		    <div class="column span-2 last">{{this.month}}</div>
		    </div>
		{{/each}}
		</div>
	</div>
</div>