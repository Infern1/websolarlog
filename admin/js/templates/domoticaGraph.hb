<form>
  <fieldset>
    <legend>Domotica Table</legend>

    <div id="domoticaTableContainer">
Daily data:<br>
<div class="column span-18 first">
<div class="column span-10" style="font-weight:700;">Name</div>
<div class="column span-7" style="font-weight:700;">Usage</div>
</div>
{{#each data.devices.today}}
<div class="column span-18 first">
<div class="column span-10">{{this.name}}</div>
<div class="column span-7">{{kWhValue value=this.KWHUsage}}</div>
</div>
{{/each}}
<br>
<hr>
<br>
Month data:<br>
<div class="column span-18 first">
<div class="column span-10" style="font-weight:700;">Name</div>
<div class="column span-7" style="font-weight:700;">Usage</div>
</div>
{{#each data.devices.month}}
<div class="column span-18 first">
<div class="column span-10">{{this.name}}</div>
<div class="column span-7">{{kWhValue value=this.KWHUsage}}</div>
</div>
{{/each}}
    </div>
  </fieldset>
</form> 
