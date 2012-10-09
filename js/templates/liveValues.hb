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
		<div  class="column span-16">
				<div class="column span-2 first">
					time
				</div>
				<div class="column span-2 first">
					Inverter
				</div>
				<div class="column span-2 first">
					GridPower
				</div>
				<div class="column span-3 first">
					I1 Power (Ratio)
				</div>
				<div class="column span-3 first">
					I2 Power (Ratio)
				</div>
				<div class="column span-2 first">
					I Total Power
				</div>
				
		{{#each data.IndexValues.inverters}}
				<div class="column span-2 first">
					{{this.live.Time}}
				</div>
				<div class="column span-2 first">
					{{this.live.INV}}&nbsp;
				</div>
				<div class="column span-2 first">
					{{this.live.GP}}
				</div>
				<div class="column span-3 first">
					{{this.live.I1P}} ({{this.live.I1Ratio}})
				</div>
				<div class="column span-3 first">
					{{this.live.I2P}} ({{this.live.I2Ratio}})
				</div>
				<div class="column span-2 first">
					{{this.live.IP}}
				</div>
		{{/each}}
		<br><br>
	</div><div class="cl"></div>
	<br>
</div>