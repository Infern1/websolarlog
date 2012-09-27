<div class="post">

<a href="" title="Live" class="heading">Live:</a>
<div class="container" class="column span-6">
{{#each data.IndexValues.inverters}}
	<div class="column span-6">
	{{#each this.live}}
		<div class="column span-5 first">Inverter {{this.INV}}</div>
     	<div class="column span-3 first">Power AC (grid)</div>
     	<div class="column span-2 last">{{this.ITP}}</div>
     	<div class="column span-3 first">Power DC (panels)</div>
     	<div class="column span-2 last">{{this.GP}}W</div>
	{{/each}}
	<div class="column span-3 first">Today</div>
    <div class="column span-2 last">{{this.day}}</div>
    <div class="column span-3 first">This week</div>
    <div class="column span-2 last">{{this.week}}</div>
    <div class="column span-3 first">This month</div>
    <div class="column span-2 last">{{this.month}}</div>
    </div>
{{/each}}
</div>

<br>
	<span class="read-more"><a href="#" title="Read More">»&nbsp;More</a></span>
</div>