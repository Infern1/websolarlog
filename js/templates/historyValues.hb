{{#each data.dayData.data.history}}
		<div class="column span-5 first">{{this.INV}}</div>
		<div class="column span-4" style="text-align:right;">{{this.GP}}</div>
		<div class="column span-8 last">{{timestampDateFormat this.date format="HH:mm:ss"}}</div>
{{/each}}