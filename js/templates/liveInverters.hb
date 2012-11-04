{{#each data.IndexValues.inverters}}
<div class="column span-2 first tooltip" title="fietsen">{{this.live.time}}</div>
<div class="column span-2 tooltip">{{this.live.INV}}&nbsp;</div>
<div class="column span-2 tooltip">{{this.live.GP}}</div>
<div class="column span-3 tooltip">{{this.live.I1P}} ({{this.live.I1Ratio}})</div>
<div class="column span-3 tooltip">{{this.live.I2P}} ({{this.live.I2Ratio}})</div>
<div class="column span-2 last tooltip" title="{{this.live.IP}}">{{this.live.IP}}</div>
{{/each}}

<script>


</script>