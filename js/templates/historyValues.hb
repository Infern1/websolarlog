{{#each data.dayData.data.history}}
		<div class="column span-4 first">{{this.INV}}</div>
		<div class="column span-4" style="text-align:right;">{{this.GP}}</div>
		<div class="column span-11 last">{{this.date}}</div>
{{/each}}