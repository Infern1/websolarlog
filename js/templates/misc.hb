<div id="todayPosts">
<div class="columns"><h3>Misc</h3>
<br><br>
<div class="col posts">
<h3>{{lang.logger}} {{lang.uptime}}:</h3>
{{data.serverUptime.day}} {{lang.days}},{{data.serverUptime.hour}} {{lang.hours}},{{data.serverUptime.min}} {{lang.mins}}<br>
<br>
</div>
<div class="col posts">
</div>
<div class="cl"></div>
<div class="col posts">
</div>
<div class="col posts">
	<div class="column span-22 last">
    	<h3>{{lang.Inverter}} {{lang.Information}}</h3>
		<div class="post">		
			{{lang.events}}
			<div class="column span-4 first">{{lang.Inv}}</div>
			<div class="column span-4">{{lang.Time}}</div>
			<div class="column span-11 last">{{lang.Event}}</div>
	
			{{#each data.infoEvents}}
			<div class="column span-4 first">{{this.INV}}</div>
			<div class="column span-4" title="{{this.HumanTime}}">{{this.date}}</div>
			<div class="column span-11 last">{{this.Event}}</div>
			{{/each}}
		</div>
	</div>
</div><div class="col posts">
	<div class="column span-22 last">
    	<h3>{{lang.Inverter}} {{lang.systemInfo}}</h3>
		<div class="post">		


<div  class="column span-22 accordion">
{{#each data.slimConfig.inverters}}

<h3>Inv. {{this.name}}</h3>
<div class="innerAccordionPeriod">
<div class="column span-20">
	
		<div class="column span-6"><b>{{../lang.system}}</b>:</div>
	</div>
	<div class="column span-20">
	
		<div class="column span-6">{{../lang.power}}</div>
		<div class="column span-4">{{../lang.expected}}</div>
		<div class="column span-4">{{../lang.location}}</div>
	</div>
	<div class="column span-20">
		<div class="column span-6">{{this.plantPower}} {{../lang.wp}}</div>
		<div class="column span-4">{{this.expectedKWH}} {{../lang.kWh}}</div>
		<div class="column span-4"><a href="http://maps.google.com/maps?q={{../data.slimConfig.lat}},+{{../data.slimConfig.long}}+%28...%29&iwloc=A&hl=en&z=15" target="_blank">{{../data.slimConfig.lat}}:{{../data.slimConfig.long}}</a></div>
	</div>
	<div class="column span-20">
		{{#each this.panels}}
		<div class="column span-19 prepend-1"><b>- {{../../lang.mpp}}{{this.id}}</b></div>
		<div class="column span-19 prepend-2">
			<div class="column span-8">{{../../lang.description}}</div>
			<div class="column span-5">{{../../lang.power}}</div>
			<div class="column span-4">{{../../lang.orientation}}/{{../../lang.pitch}}</div>
		</div>
		<div class="column span-19 prepend-2">
			<div class="column span-8">{{this.description}}</div>
			<div class="column span-5">{{this.amount}}*{{this.wp}}={{this.totalWp}} {{../../lang.wp}}</div>
			<div class="column span-4">{{this.roofOrientation}}°/{{this.roofPitch}}°</div>
		</div>
		{{/each}}
 	</div>
 </div>

{{/each}}
 	</div>
		</div>
	</div>
</div>
<div class="cl"></div>

<div class="col posts">
	<div class="column span-22 last">
		<h3>{{lang.Notice}}</h3>
			<div class="post">		
				{{lang.events}}
				<div  class="column span" style="overflow-y: scroll;overflow-x: hidden;height:200px">
				<div class="column span-4 first">{{lang.Inv}}</div>
				<div class="column span-4">{{lang.Time}}</div>
				<div class="column span-11 last">{{lang.Event}}</div>
		
				{{#each data.noticeEvents}}
				<div class="column span-4 first">{{this.INV}}</div>
				<div class="column span-4" title="{{this.HumanTime}}">{{this.date}}</div>
				<div class="column span-11 last">{{this.Event}}</div>
				{{/each}}
			</div>
		</div>
	</div>
</div>
<div class="col posts">
	<div class="column span-22 last">
		<h3>{{lang.Alarm}}</h3>
			<div class="post">		
				{{lang.events}}
				<div  class="column span" style="overflow-y: scroll;overflow-x: hidden;height:200px">
				<div class="column span-4 first">{{lang.Inv}}</div>
				<div class="column span-4">{{lang.Time}}</div>
				<div class="column span-11 last">{{lang.Event}}</div>
		
				{{#each data.alarmEvents}}
				<div class="column span-4 first">{{this.INV}}</div>
				<div class="column span-4" title="{{this.HumanTime}}">{{this.date}}</div>
				<div class="column span-11 last">{{this.Event}}</div>
				{{/each}}
			</div>
		</div>
	</div></div>
<div class="cl"></div>

</div>
</div>