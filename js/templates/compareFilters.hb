<div id="compareFilter">
    <table>
    	<tr>
    		<td>Inverter:</td>
	    	<td>
	    		<select>
	    			{{#each data.inverters}}
	    			{{/each}}
	    		</select>
	    	</td>
	    	</tr><tr>
    		<td>Compare:</td>
    		<td>
	    		<select id="whichMonth">
	    			{{#each data.dayData.month}}
	    			<option value="{{this.number}}">{{this.name}}</option>
	    			{{/each}}
	    		</select>/
	    		<select id="whichYear">
	    			{{#each data.dayData.year}}
	    			<option value="{{this.date}}">{{this.date}}</option>
	    			{{/each}}
	    		</select>
	    	</td>
    		<td colspan="2">To:</td>
	    	<td>
	    		<select id="compareMonth">
	    			{{#each data.dayData.month}}
	    			<option value="{{this.number}}">{{this.name}}</option>
	    			{{/each}}
	    		</select>/
	    		<select id="compareYear">
	    			<option value="0">expected</option>
					{{#each data.dayData.year}}
	    			<option value="{{this.date}}">{{this.date}}</option>
	    			{{/each}}
	    		</select>
	    	</td>
		</tr>
    </table>
</div>