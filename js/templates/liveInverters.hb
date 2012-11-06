{{#each data.IndexValues.inverters}}
<div class="column span-2 first tooltip" title="fietsen">{{this.live.time}}</div>
<div class="column span-2 tooltip" title="{{this.live.IP}}">{{this.live.INV}}</div>
<div class="column span-2 tooltip" title="{{this.live.GP}} = {{this.live.GV}} * {{this.live.GA}}">{{this.live.GP}}</div>
<div class="column span-3 tooltip" title="{{this.live.I1P}} = {{this.live.I1V}} * {{this.live.I1A}}">{{this.live.I1P}} ({{this.live.I1Ratio}})</div>
<div class="column span-3 tooltip" title="{{this.live.I2P}} = {{this.live.I2V}} * {{this.live.I2A}}">{{this.live.I2P}} ({{this.live.I2Ratio}})</div>
<div class="column span-2 last tooltip" title="{{this.live.IP}} = {{this.live.I1P}} + {{this.live.I2P}}">{{this.live.IP}}</div>
{{/each}}