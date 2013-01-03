<div style="display:table-cell;">
<div id="columns" style="display:table-cell; width:100%;">
<div id="todayPosts">
<div class="columns">
<h3>Compare</h3>

<div class="column span-10">
<div class="column span-4 first">{{lang.month}}</div>
<div class="column span-5" style="text-align:right;">{{lang.expected}}</div>
<div class="cl"></div>
{{#each compare}}
{{#each this}}
<div class="column span-4 first">{{this.date}}</div>
<div class="column span-5" style="text-align:right;">{{this.displayKWH}}</div>
{{/each}}{{/each}}
</div>

<div class="column span-10">
<div class="column span-4 first">{{lang.month}}</div>
<div class="column span-5" style="text-align:right;">{{lang.harvested}}</div>
<div class="cl"></div>
{{#each which}}
{{#each this}}
<div class="column span-4 first">{{this.date}}</div>
<div class="column span-5" style="text-align:right;">{{this.displayKWH}}</div>
{{/each}}{{/each}}
</div>


<div class="column span-10">
<div class="column span-5" style="text-align:right;">{{lang.difference}}</div>
{{#each diff}}

<div class="column span-5" style="text-align:right;">{{this.diff}}</div>
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
