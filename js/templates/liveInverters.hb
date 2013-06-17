{{#each data.inverters}}
<div class="column span-27  tooltip">
<div class="column span-1 first tooltip">
	{{#if_eq 'standby' compare=this.live.status}}
		<img src="images/bullet_{{this.live.status}}.png" title="{{../../this.lang.standby}}"/>
	{{/if_eq}}
	{{#if_eq 'online' compare=this.live.status}}
		<img src="images/bullet_{{this.live.status}}.png" title="{{../../this.lang.online}}"/>
	{{/if_eq}}
	{{#if_eq 'offline' compare=this.live.status}}
		<img src="images/bullet_{{this.live.status}}.png" title="{{../../this.lang.offline}}"/>
	{{/if_eq}}
	
</div>
<div class="column span-6  tooltip" style="overflow:hidden;" title="{{this.live.name}}">
	<img src="images/arrow_{{this.live.trend}}.gif" title="{{this.live.trend}}"/>&nbsp;{{this.live.name}}
</div>
<div class="column span-3 tooltip" title="{{this.live.INV}}">{{this.live.time}}</div>
<div class="column span-3 tooltip" title="{{this.live.GP}} W = {{this.live.GV}} V * {{this.live.GA}} A">{{this.live.GP}}</div>
<div class="column span-4 tooltip" title="{{this.live.I1P}} W = {{this.live.I1V}} V * {{this.live.I1A}} A">{{this.live.I1P}} ({{this.live.I1Ratio}})</div>
<div class="column span-4 tooltip" title="{{this.live.I2P}} W = {{this.live.I2V}} V * {{this.live.I2A}} A">{{this.live.I2P}} ({{this.live.I2Ratio}})</div>
<div class="column span-3 last tooltip" title="{{this.live.IP}} W = {{this.live.I1P}} W + {{this.live.I2P}} W">{{this.live.IP}}</div>
</div>
{{/each}}