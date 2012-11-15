<div id="todayPosts">
<div class="columns"><h3>Year</h3>
The values below are grouped by month of the selected year.
<br><br>
<div class="col posts">

</div>
<div class="col posts">
	<div class="column span-11 last">
    	<h3>{{lang.Inverter}} {{lang.Information}}</h3>
		<div class="post">		
			{{lang.events}}
			<div class="column span-1 first">{{lang.Inv}}</div>
			<div class="column span-3">{{lang.Time}}</div>
			<div class="column span-7 last">{{lang.Event}}</div>
	
			{{#each data.infoEvents}}
			<div class="column span-1 first">{{this.INV}}</div>
			<div class="column span-3" title="{{this.HumanTime}}">{{this.date}}</div>
			<div class="column span-7 last">{{this.Event}}</div>
			{{/each}}
		</div>
	</div>
</div>
<div class="cl"></div>

<div class="col posts">
	<div class="column span-11 last">
		<h3>{{lang.Notice}}</h3>
			<div class="post">		
				{{lang.events}}
				<div  class="column span" style="overflow-y: scroll;overflow-x: hidden;height:200px">
				<div class="column span-1 first">{{lang.Inv}}</div>
				<div class="column span-3">{{lang.Time}}</div>
				<div class="column span-6 last">{{lang.Event}}</div>
		
				{{#each data.noticeEvents}}
				<div class="column span-1 first">{{this.INV}}</div>
				<div class="column span-3" title="{{this.HumanTime}}">{{this.date}}</div>
				<div class="column span-6 last">{{this.Event}}</div>
				{{/each}}
			</div>
		</div>
	</div>
</div>
<div class="col posts">
	<div class="column span-11 last">
		<h3>{{lang.Alarm}}</h3>
			<div class="post">		
				{{lang.events}}
				<div  class="column span" style="overflow-y: scroll;overflow-x: hidden;height:200px">
				<div class="column span-1 first">{{lang.Inv}}</div>
				<div class="column span-3">{{lang.Time}}</div>
				<div class="column span-6 last">{{lang.Event}}</div>
		
				{{#each data.alarmEvents}}
				<div class="column span-1 first">{{this.INV}}</div>
				<div class="column span-3" title="{{this.HumanTime}}">{{this.date}}</div>
				<div class="column span-6 last">{{this.Event}}</div>
				{{/each}}
			</div>
		</div>
	</div></div>
<div class="cl"></div>

</div>
</div>