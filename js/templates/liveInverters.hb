{{#each data.IndexValues.inverters}}
<div class="column span-2 first" title="fietsen">{{this.live.time}}</div>
<div class="column span-2">{{this.live.INV}}&nbsp;</div>
<div class="column span-2">{{this.live.GP}}</div>
<div class="column span-3">{{this.live.I1P}} ({{this.live.I1Ratio}})</div>
<div class="column span-3">{{this.live.I2P}} ({{this.live.I2Ratio}})</div>
<div class="column span-2 last">{{this.live.IP}}</div>
{{/each}}

<script>


</script>