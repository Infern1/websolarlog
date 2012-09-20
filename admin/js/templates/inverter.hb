<form>
  <input type="hidden" name="id" value="data.inverter.id" />
  <fieldset>
    <legend>Inverter: {{data.inverter.name}}</legend>
    <label for="name">name:</label><input type="text" name="name" value="{{data.inverter.name}}" /><br />
    <label for="description">description:</label><input type="text" name="description" value="{{data.inverter.description}}" /><br />
    <label for="initialkwh">Initial kWh:</label><input type="text" name="initialkwh" value="{{data.inverter.initialkwh}}" /><br />
    <label for="power">power:</label><input type="text" name="power" value="{{data.inverter.power}}" /><br />
    <label for="heading">heading:</label><input type="text" name="heading" value="{{data.inverter.heading}}" /><br />
    <label for="correctionFactor">correction factor:</label><input type="text" name="correctionFactor" value="{{data.inverter.correctionFactor}}" /><br />
    <label for="comAddress">Comm.address:</label><input type="text" name="comAddress" value="{{data.inverter.comAddress}}" /><br />
    <label for="comLog">Log comm:</label><input type="checkbox" name="comLog" value="1" {{#if data.comLog}}checked=checked{{/if}}/><br />
    <button type="button" id="btnInverterSubmit">Save</button>
  </fieldset>
</form> 
<form>
  <input type="hidden" name="id" value="data.inverter.id" />
  <fieldset>
    <legend>Panel 1:</legend>
    <label for="pnl1Description">description:</label><input type="text" name="pnl1Description" value="{{data.inverter.pnl1Description}}" /><br />
    <label for="pnl1RoofOrientation">roof orientation:</label><input type="text" name="pnl1RoofOrientation" value="{{data.inverter.pnl1RoofOrientation}}" /><br />
    <label for="pnl1RoofPitch">>roof pitch:</label><input type="text" name="pnl1RoofPitch" value="{{data.inverter.pnl1RoofPitch}}" /><br />
    <button type="button" id="btnPanel1Submit">Save</button>
  </fieldset>
</form> 
<form>
  <input type="hidden" name="id" value="data.inverter.id" />
  <fieldset>
    <legend>Panel 2:</legend>
    <label for="pnl2Description">description:</label><input type="text" name="pnl2Description" value="{{data.inverter.pnl2Description}}" /><br />
    <label for="pnl2RoofOrientation">roof orientation:</label><input type="text" name="pnl2RoofOrientation" value="{{data.inverter.pnl2RoofOrientation}}" /><br />
    <label for="pnl2RoofPitch">>roof pitch:</label><input type="text" name="pnl2RoofPitch" value="{{data.inverter.pnl2RoofPitch}}" /><br />
    <button type="button" id="btnPanel2Submit">Save</button>
  </fieldset>
</form> 