{{#each data.inverters}}
<div class="column span-27  liveToolTip">
<div class="column span-1 first liveToolTip">
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
<div class="column span-6  liveliveToolTip" style="overflow:hidden;" title="{{this.live.name}}">
	<img src="images/arrow_{{this.live.trend}}.gif" title="{{this.live.trend}}"/>&nbsp;{{this.live.name}}
</div>
<div class="column span-3 liveToolTip" title="{{this.live.time}}">{{this.live.time}}</div>
<div class="column span-3 liveToolTip" title="{{this.live.totalDeviceACP}} W = {{this.live.GV}} V * {{this.live.GA}} A">{{this.live.totalDeviceACP}}</div>
<div class="column span-4 liveToolTip" title="{{this.live.I1P}} W = {{this.live.I1V}} V * {{this.live.I1A}} A">{{this.live.I1P}} ({{this.live.I1Ratio}})</div>
<div class="column span-4 liveToolTip" title="{{this.live.I2P}} W = {{this.live.I2V}} V * {{this.live.I2A}} A">{{this.live.I2P}} ({{this.live.I2Ratio}})</div>
<div class="column span-3 last liveToolTip" title="{{this.live.IP}} W = {{this.live.I1P}} W + {{this.live.I2P}} W  {{#if_gt this.live.I3P compare=0}} + {{this.live.I3P}} W {{/if_gt}}
">{{this.live.totalDeviceIP}}</div>
</div>
{{/each}}