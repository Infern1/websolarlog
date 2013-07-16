<form id="graphFormId">
  <fieldset style="width:560px;">
   <legend>{{capitalizer title=data.inverter.type}} graph: {{data.name}}</legend>
  		
			<div class="column span-28">
				<div class="column span-8 first">Location</div>
				<div class="column span-6">Axe</div>
				<div class="column span-8">Name</div>
				<div class="column span-4">..</div>
			 </div>
{{#data.axes}}
<div class="column span-28 axeContainer" id="{{this.name}}">
<form>
	<div class="column span-0 first" style="display:none;">{{this.id}}</div>
	<div class="column span-8 first name">{{this.name}}</div>
	<div class="column span-6 axe">{{this.axe}}</div>
	<div class="column span-8"><input type="text" style="width:160px"  class="label" value="{{this.json.label}}" /></div>
	<div class="column span-1"><img class="deleteAxe" id="{{this.id}}" src="../admin/images/bin_closed.png"/></div>
	<div class="column span-1"><img class="saveAxe" src="../admin/images/disk.png"/></div>
</form>
</div>
{{/data.axes}}
	    <div class="cl"></div>
		<strong>Graph layout</strong>:<br>
		<div class="column" style="width:554px;text-align:center;border:1px solid #ddd;">x2axis</div>
		<div class="column" style="width:556px;">
		<div class="column last" style="width:80px;border:1px solid #ddd;height:75px;">
		<div style="margin-left:auto;margin-right:auto;width:40px;">left<br>yaxis</div></div>
		<div class="column last" style="width:250px;border:1px solid #ddd;height:75px;">
		<div style="margin-left:auto;margin-right:auto;width:110px;"><br>graph lines section</div></div>
		<div class="column last" style="width:220px;border:1px solid #ddd;height:75px;">
		<div style="margin-left:auto;margin-right:auto;width:60px;">right<br>y(2-8)axis</div></div>
		<div class="column" style="width:554px;text-align:center;border:1px solid #ddd;">xaxis</div>
		</div>
		<div class="column span-28" id="file{{id}}">
			<div class="column span-30 first">
			<div class="column span-9 first">Serie</div>
			<div class="column span-5 first">@ axe</div>
			<div class="column span-7 first">visible in graph:</div>
			<div class="column span-7 first">default shown in graph:</div>
										
							
				<ul>    
	    			{{#data.series}}
						<div class="column span-9 first">{{this.json.label}}</div>
						<div class="column span-5 first">
						<select name="yaxis[]"> 
						    {{#../data.axesList}}
								 <option value="{{this.axe}}" {{#if_eq this.axe compare=../this.json.yaxis}}selected{{/if_eq}}>{{this.axe}}</option>
							{{/../data.axesList}}
							</select>
						</div>
						<div class="column span-7 first">
						
						{{checkboxWithHidden 'serieHidden[]' this.show}}
						</div>
						<div class="column span-7 first">
							{{checkboxWithHidden 'serieVisible[]' this.disabled}}	
							
						</div>
					{{/data.series}}
				</ul>
			</div>
		<div>
        <div class="span-20 first">
        	<button type="button" id="btnGraphSubmit">Save</button>
        </div>
  </fieldset>
</form> 