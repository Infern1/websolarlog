{{#each data.IndexValues.inverters}}
<div class="column span-2 first tooltip" title="fietsen">{{this.live.time}}</div>
<div class="column span-2 tooltip" title="{{this.live.INV}}">{{this.live.INV}}</div>
<div class="column span-2 tooltip" title="{{this.live.GP}} W = {{this.live.GV}} V * {{this.live.GA}} A">{{this.live.GP}}</div>
<div class="column span-3 tooltip" title="{{this.live.I1P}} W = {{this.live.I1V}} V * {{this.live.I1A}} A">{{this.live.I1P}} ({{this.live.I1Ratio}})</div>
<div class="column span-3 tooltip" title="{{this.live.I2P}} W = {{this.live.I2V}} V * {{this.live.I2A}} A">{{this.live.I2P}} ({{this.live.I2Ratio}})</div>
<div class="column span-2 last tooltip" title="{{this.live.IP}} W = {{this.live.I1P}} W + {{this.live.I2P}} W">{{this.live.IP}}</div>
{{/each}}