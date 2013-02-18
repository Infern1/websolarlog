<div style="display:table-cell;">
<div id="columns" style="display:table-cell; width:100%;">
<div id="todayPosts">
<div class="columns">
<h3>Compare</h3>
<div class="column span-17">
<div class="column span-4 first">{{lang.month}}</div>
<div class="column span-5" style="text-align:right;">{{lang.expected}}</div>
<div class="column span-5" style="text-align:right;">{{lang.CumExpected}}</div>
<div class="cl"></div>
{{#each compare}}

{{#each this}}<div class="trs">
<div class="column span-4 first" >{{this.date}}</div>
<div class="column span-5" style="text-align:right;" >{{this.harvested}}</div>
<div class="column span-5" style="text-align:right;">{{this.displayKWH}}</div>
<div class="cl"></div>
</div>{{/each}}{{/each}}
</div>

<div class="column span-16" style="border-left:solid 1px #ccc;">
<div class="column span-4 first">{{lang.month}}</div>
<div class="column span-5" style="text-align:right;">{{lang.harvested}}</div>
<div class="column span-5" style="text-align:right;">{{lang.CumHarvested}}</div>
<div class="cl"></div>
{{#each which}}
{{#each this}}<div class="trs">
<div class="column span-4 first">{{this.date}}</div>
<div class="column span-5" style="text-align:right;">{{this.harvested}}</div>
<div class="column span-5" style="text-align:right;">{{this.displayKWH}}</div>
<div class="cl"></div>
</div>
{{/each}}{{/each}}
</div>


<div class="column span-11" style="border-left:solid 1px #ccc;">
<div class="column span-5" style="text-align:right;">{{lang.CumDifference}}</div>
<div class="column span-5" style="text-align:right;">{{lang.difference}}</div>
<div class="cl"></div>
{{#each diff}}<div class="trs">
<div class="column span-5" style="text-align:right;">{{this.diff}}</div>
<div class="column span-5" style="text-align:right;">{{this.diffHar}}</div>
<div class="cl"></div>
</div>
{{/each}}
</div>
<div class="cl"></div>
</div>


</div>
<div class="cl"></div>
<h3></h3>
*{{difference}}={{lang.harvested}}-{{lang.expected}}
</div>
</div>
</div>
<script>$(function() {
$("div.trs:even").css('background-color','#ddd');
});</script>
<style>
</style>
