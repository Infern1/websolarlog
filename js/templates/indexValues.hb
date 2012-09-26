<div class="post">
	<a href="" title="Live" class="heading">Live:</a>

<div style="color:black">
</h2>Inveters:<h2>
{{#data.IndexValues}}
    <p>first print id:{{this.id}}</p>
    <p>first print id:{{this.live.GP}}</p>
{{/data.IndexValues}}
</div>

{{#data.IndexValues.dayINV}}
	<div class="container" style="width:auto;">
	{{#this}}
    	<div class="column span-14 first">Inverter {{this.INV}}</div>
     	<div class="column span-2 first">Power AC (panels)</div>
     	<div class="column span-12 last">{{this.ITp}}</div>
     	<div class="column span-2 first">Power DC (grid)</div>
     	<div class="column span-12 last">{{this.GP}}W</div>  
	{{/this}}
	</div>
{{/data.IndexValues.dayINV}}

<br>
		<span class="read-more"><a href="#" title="Read More">»&nbsp;More</a></span>
</div>