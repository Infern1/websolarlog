<form>
  <input type="hidden" name="s" value="save-inverter" />
  <input type="hidden" name="id" value="{{data.inverter.id}}" />
  <fieldset>
    <legend>Inverter: {{data.inverter.name}}</legend>
    <label for="name">name:</label><input type="text" name="name" value="{{data.inverter.name}}" /><br />
    <label for="description">description:</label><input type="text" name="description" value="{{data.inverter.description}}" /><br />
    <label for="liveOnFrontend">Show livedata@frontend:</label><input type="checkbox" name="liveOnFrontend" value="1" {{#if data.inverter.liveOnFrontend}}checked=checked{{/if}}/><br />
    <label for="graphOnFrontend">Show history@graph:</label><input type="checkbox" name="graphOnFrontend" value="1" {{#if data.inverter.graphOnFrontend}}checked=checked{{/if}}/><br />
    
    <label for="initialkwh">initial kWh:</label><input type="text" name="initialkwh" value="{{data.inverter.initialkwh}}" /><br />
    <label for="expectedkwh">expected kWh:</label><input type="text" name="expectedkwh" value="{{data.inverter.expectedkwh}}" /><br />
    <label for="plantpower">plant power:</label><input type="text" name="plantpower" value="{{data.inverter.plantpower}}" readonly="true" /> Calculated by the panels<br />
    <label for="heading">heading:</label><input type="text" name="heading" value="{{data.inverter.heading}}" /><br />
    <label for="correctionFactor">correction factor:</label><input type="text" name="correctionFactor" value="{{data.inverter.correctionFactor}}" /><br />
    <label for="deviceApi">Device Api:</label>
        <select name="deviceApi">
            <option value="AURORA" {{#if_eq data.inverter.deviceApi compare="AURORA"}}selected=selected{{/if_eq}}>Aurora</option>
            <option value="SMA-RS485" {{#if_eq data.inverter.deviceApi compare="SMA-RS485"}}selected=selected{{/if_eq}}>SMA RS485</option>
            <option value="DutchSmartMeter" {{#if_eq data.inverter.deviceApi compare="DutchSmartMeter"}}selected=selected{{/if_eq}}>Dutch Smart Meter</option>
        </select>
        <br />
    <label for="comAddress">Device Type:</label>
            <select name="deviceType">
            <option value="production" {{#if_eq data.inverter.type compare="production"}}selected=selected{{/if_eq}}>Production</option>
            <option value="metering" {{#if_eq data.inverter.type compare="metering"}}selected=selected{{/if_eq}}>Metering</option>
        </select>
        <br />
    <label for="comAddress">RS485 address:</label><input type="text" name="comAddress" value="{{data.inverter.comAddress}}" /><br />
    <label for="comLog">Log comm:</label><input type="checkbox" name="comLog" value="1" {{#if data.inverter.comLog}}checked=checked{{/if}}/><br />
    <label for="syncTime">Synchronize time:</label><input type="checkbox" name="syncTime" value="1" {{#if data.inverter.syncTime}}checked=checked{{/if}}/><br />
    <label for="pvoutputEnabled">PVOutput Enabled:</label><input type="checkbox" name="pvoutputEnabled" value="1" {{#if data.inverter.pvoutputEnabled}}checked=checked{{/if}}/><br />
    <label for="pvoutputApikey">PVOutput Api key:</label><input type="text" name="pvoutputApikey" value="{{data.inverter.pvoutputApikey}}" /><br />
    <label for="pvoutputSystemId">PVOutput System id:</label><input type="text" name="pvoutputSystemId" value="{{data.inverter.pvoutputSystemId}}" /><br />
    <button type="button" id="btnInverterSubmit">Save</button>
  </fieldset>
</form> 
{{#data.inverter.panels}}
    <form>
      <input type="hidden" name="s" value="save-panel" />
      <input type="hidden" name="id" value="{{this.id}}" />
      <input type="hidden" name="inverterId" value="{{this.inverterId}}" />
      <fieldset>
        <legend>Panel: {{this.id}}</legend>
        <label for="description">description:</label><input type="text" name="description" value="{{this.description}}" /><br />
        <label for="roofOrientation">roof orientation:</label><input type="text" name="roofOrientation" value="{{this.roofOrientation}}" /><br />
        <label for="roofPitch">roof pitch:</label><input type="text" name="roofPitch" value="{{this.roofPitch}}" /><br />
        <label for="amount">amount:</label><input type="text" name="amount" value="{{this.amount}}" /><br />
        <label for="wp">wp one panel:</label><input type="text" name="wp" value="{{this.wp}}" /><br />
        <button type="button" id="btnPanelSubmit{{this.id}}" class="panel_submit">Save</button>
      </fieldset>
    </form>
{{/data.inverter.panels}}
<br />
<div id="new_panels"></div>
{{#if_gt data.inverter.id compare=0}}
    <button type="button" id="btnNewPanel">New panel</button><br/>
{{else}}
Panels can be added after saving the new inverter
{{/if_gt}}