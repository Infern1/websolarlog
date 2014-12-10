{{#each data}}
{{#if_eq this.type compare="production"}}
<div class="column span-27  liveToolTip">
<div class="column span-1 first liveToolTip">
	{{#if_eq 'standby' compare=this.data.status}}
		<img src="images/bullet_{{this.data.status}}.png" title="{{../../lang.standby}}"/>
	{{/if_eq}}
	{{#if_eq 'online' compare=this.data.status}}
		<img src="images/bullet_{{this.data.status}}.png" title="{{../../lang.online}}"/>
	{{/if_eq}}
	{{#if_eq 'offline' compare=this.data.status}}
		<img src="images/bullet_{{this.data.status}}.png" title="{{../../lang.offline}}"/>
	{{/if_eq}}
	
</div>
<div class="column span-6  liveToolTip" style="overflow:hidden;" title="{{this.name}}">
	<img src="images/arrow_{{this.data.trendImage}}.gif" title="{{this.data.trend}}"/>&nbsp;{{this.name}}
</div>
<div class="column span-3">{{timestampDateFormat this.data.time format="HH:mm:ss"}}</div>
<div class="column span-3" title="{{this.data.totalDeviceACP}} W = {{this.data.GV}} V * {{this.data.GA}} A">{{this.data.GPTotal}}</div>
<div class="column span-4" title="{{this.data.I1P}} W = {{this.data.I1V}} V * {{this.data.I1A}} A">{{this.data.I1P}} ({{this.data.I1Ratio}})</div>
<div class="column span-4" title="{{this.data.I2P}} W = {{this.data.I2V}} V * {{this.data.I2A}} A">{{this.data.I2P}} ({{this.data.I2Ratio}})</div>
<div class="column span-3 last" title="{{this.data.IPTotal}} W = {{this.data.I1P}} W + {{this.data.I2P}} W  {{#if_gt this.data.I3P compare=0}} + {{this.data.I3P}} W {{/if_gt}}
">{{this.data.IPTotal}}</div>
</div>
{{/if_eq}}
{{/each}}

<script language="text/javascript">
$(".liveTooltip").tooltip( "destroy" );
$( ".liveTooltip" ).tooltip( "open" );
</script>
