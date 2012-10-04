<div class="post">
	<div style="width: 180px; height: 150px; float:left;">
		<div id="gaugeGP" style="width: 200px; height: 150px; position: relative;"></div>
	</div>
	<div style="width: 180px; height: 150px; float:left;">
		<div id="gaugeIP" style="width: 200px; height: 150px; position: relative;"></div>
	</div>
	<div style="width: 180px; height: 150px; float:left;">
		<div id="gaugeEFF" style="width: 200px; height: 150px; position: relative;"></div>
	</div>	</div><div class="cl"></div>
	<h3>Live:</h3>
	<div class="container" >
		<div  class="column span-14">

				<div class="column span-2 first">
					time
				</div>
				<div class="column span-2 first">
					INV
				</div>
				<div class="column span-2 first">
					GP
				</div>
				<div class="column span-2 first">
					I1P
				</div>
				<div class="column span-2 first">
					I2P
				</div>
				<div class="column span-2 first">
					ITP
				</div>
		{{#each data.IndexValues.inverters}}
				<div class="column span-2 first">
					{{this.live.SDTE}}
				</div>
				<div class="column span-2 first">
					{{this.live.INV}}
				</div>
				<div class="column span-2 first">
					{{this.live.GP}}
				</div>
				<div class="column span-2 first">
					{{this.live.I1P}}
				</div>
				<div class="column span-2 first">
					{{this.live.I2P}}
				</div>
				<div class="column span-2 first">
					{{this.live.IP}}
				</div>
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