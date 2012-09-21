<form>
  <input type="hidden" name="id" value="data.inverter.id" />
  <fieldset>
    <legend>Inverter: {{data.inverter.name}}</legend>
    <label for="name">name:</label><input type="text" name="name" value="{{data.inverter.name}}" /><br />
    <label for="description">description:</label><input type="text" name="description" value="{{data.inverter.description}}" /><br />
    <label for="initialkwh">initial kWh:</label><input type="text" name="initialkwh" value="{{data.inverter.initialkwh}}" /><br />
    <label for="expectedkwh">expected kWh:</label><input type="text" name="expectedkwh" value="{{data.inverter.expectedkwh}}" /><br />
    <label for="plantpower">plant power:</label><input type="text" name="plantpower" value="{{data.inverter.plantpower}}" /><br />
    <label for="heading">heading:</label><input type="text" name="heading" value="{{data.inverter.heading}}" /><br />
    <label for="correctionFactor">correction factor:</label><input type="text" name="correctionFactor" value="{{data.inverter.correctionFactor}}" /><br />
    <label for="comAddress">RS485 address:</label><input type="text" name="comAddress" value="{{data.inverter.comAddress}}" /><br />
    <label for="comLog">Log comm:</label><input type="checkbox" name="comLog" value="1" {{#if data.comLog}}checked=checked{{/if}}/><br />
    <button type="button" id="btnInverterSubmit">Save</button>
  </fieldset>
</form> 

{{#data.inverter.panels}}
<form>
  <input type="hidden" name="id" value="{{this.id}}" />
  <input type="hidden" name="inverterId" value="data.inverter.id" />
  <fieldset>
    <legend>Panel: {{this.id}}</legend>
    <label for="description">description:</label><input type="text" name="pnl1Description" value="{{this.description}}" /><br />
    <label for="roofOrientation">roof orientation:</label><input type="text" name="pnl1RoofOrientation" value="{{this.roofOrientation}}" /><br />
    <label for="roofPitch">roof pitch:</label><input type="text" name="pnl1RoofPitch" value="{{this.roofPitch}}" /><br />
    <button type="button" id="btnPanel1Submit">Save</button>
  </fieldset>
</form> 
{{/data.inverter.panels}}
<br />
<button type="button" id="btnPanel_-1" class="panel_select">New panel</button><br/>