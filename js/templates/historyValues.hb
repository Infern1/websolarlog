{{#each data.dayData.data.history}}
		<div class="column span-2 first">{{this.INV}}</div>
		<div class="column span-3">{{this.GP}} kWh</div>
		<div class="column span-4 last">{{this.date}}</div>
{{/each}}