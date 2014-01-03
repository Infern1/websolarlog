<form>
  <fieldset>
    <legend>Plugwise Stretch</legend>
    <div class="column span-28" id="syncPlugs">
    	<button id="btnSyncPlugs">syncPlugs</button>
    </div>
    <div class="column span-28" id="file{{id}}">
		<div class="column span-6 first" id="{{this.applianceID}}">Name</div>
		<div class="column span-6 last">current power usage</div>
		<div class="column span-4 last">power state</div>
		<div class="column span-4 last">Switch state</div>
    </div>
    {{#each data.plugs}}
    <div class="column span-28" id="file{{id}}">
    	<a id="anchor-{{this.applianceID}}">&nbsp;</a>
		<div class="column span-6 first editme" id="{{this.applianceID}}">{{this.name}}</div>
		<div class="column span-6 last" id="{{this.applianceID}}-W">{{this.currentPowerUsage}} W</div>
		
		{{#if_eq this.powerState compare="on"}}
		<div class="column span-4 last"><img src="images/lightbulb.png"></div>
		<div class="column span-4 last switchPlug" id="{{this.applianceID}}-off">Switch off</div>
		{{else}}
		<div class="column span-4 last"><img src="images/lightbulb_off.png"></div>
		<div class="column span-4 last switchPlug" id="{{this.applianceID}}-on">Switch on</div>
		{{/if_eq}}
		</div>
    </div>
    {{/each}}
  </fieldset>
</form> 
