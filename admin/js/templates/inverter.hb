<form>
  <input type="hidden" name="s" value="save-inverter" />   
  <input type="hidden" name="id" value="{{data.inverter.id}}" />   
  <fieldset>
    <legend>Inverter: {{data.inverter.name}}</legend>
    Name and description of inverter:<br>
    <label for="name">name:</label><input type="text" name="name" value="{{data.inverter.name}}" />   <br />   
    <label for="description">description:</label><input type="text" name="description" value="{{data.inverter.description}}" />   <br />       
    <label for="liveOnFrontend">Show livedata@frontend:</label>
    {{checkboxWithHidden 'liveOnFrontend' data.inverter.liveOnFrontend}}<br />   
    <label for="graphOnFrontend">Show history@graph:</label>
    {{checkboxWithHidden 'graphOnFrontend' data.inverter.graphOnFrontend}}<br />   
    <hr>
    kWh the inverter already generated without WSL and the installation date of the inverter;<br>
    <label for="initialkwh">initial kWh:</label><input type="text" name="initialkwh" value="{{data.inverter.initialkwh}}" />    kWh<br />       
    <label for="producesSince">produces since:</label><input type="text" name="producesSince" value="{{data.inverter.producesSince}}" />   (31-12-2000)<br />   
    <hr>
    What is the expected production in kWh for this inverter. The plant power is calculated by the panels below:<br>
    <label for="expectedkwh">expected kWh:</label><input type="text" name="expectedkwh" value="{{data.inverter.expectedkwh}}" />    kWh<br />   
    <label for="plantpower">plant power:</label><input type="text" name="plantpower" value="{{data.inverter.plantpower}}" readonly="true" />    Calculated by the panels<br />   
    <hr>
    <label for="heading">heading:</label><input type="text" name="heading" value="{{data.inverter.heading}}" />   <br />   
    <label for="correctionFactor">correction factor:</label><input type="text" name="correctionFactor" value="{{data.inverter.correctionFactor}}" />   <br />   
	<hr>
	Select the program you use to communicate with the device,what type of device it is and if needed the address of the device:<br>
    <label for="deviceApi">Device Api:</label>
        <select name="deviceApi">
            <option value="AURORA" {{#if_eq data.inverter.deviceApi compare="AURORA"}}selected=selected{{/if_eq}}>Aurora</option>
            <option value="SMA-RS485" {{#if_eq data.inverter.deviceApi compare="SMA-RS485"}}selected=selected{{/if_eq}}>SMA RS485</option>
            <option value="SMA-BT" {{#if_eq data.inverter.deviceApi compare="SMA-BT"}}selected=selected{{/if_eq}}>SMA-spot BlueTooth</option>
            <option value="Diehl-ethernet" {{#if_eq data.inverter.deviceApi compare="Diehl-ethernet"}}selected=selected{{/if_eq}}>Diehl Ethernet</option>
            <option value="DutchSmartMeter" {{#if_eq data.inverter.deviceApi compare="DutchSmartMeter"}}selected=selected{{/if_eq}}>Dutch Smart Meter</option>
            <option value="DutchSmartMeterRemote" {{#if_eq data.inverter.deviceApi compare="DutchSmartMeterRemote"}}selected=selected{{/if_eq}}>Dutch Smart Meter Remote</option>
            <option value="Open-Weather-Map" {{#if_eq data.inverter.deviceApi compare="Open-Weather-Map"}}selected=selected{{/if_eq}}>Open Weather Map</option>
        </select>
    <br />   
    <label for="deviceType">Device Type:</label>
            <select name="deviceType">
            <option value="production" {{#if_eq data.inverter.type compare="production"}}selected=selected{{/if_eq}}>Production</option>
            <option value="metering" {{#if_eq data.inverter.type compare="metering"}}selected=selected{{/if_eq}}>Metering</option>
            <option value="weather" {{#if_eq data.inverter.type compare="weather"}}selected=selected{{/if_eq}}>Weather</option>
        </select>
        <br />   
    <label for="comAddress">(RS485/IP) address:</label><input type="text" name="comAddress" value="{{data.inverter.comAddress}}" />1-255 for RS485 or a IP<br />   
    <hr>
    <label for="comLog">Log comm:</label>
    {{checkboxWithHidden 'comLog' data.inverter.comLog}}<br />
    
    <label for="syncTime">Synchronize time:</label>
    {{checkboxWithHidden 'syncTime' data.inverter.syncTime}}<br>   
    <hr><a name="pvoutput"/>
    PVoutput config for this inverter;<br>
    
    <label for="pvoutputEnabled">PVOutput Enabled:</label>
    {{checkboxWithHidden 'pvoutputEnabled' data.inverter.pvoutputEnabled}}<br />   
    <label for="pvoutputApikey">PVOutput Api key:</label><input type="text" name="pvoutputApikey" value="{{data.inverter.pvoutputApikey}}" />   See your PVoutput settings page.<br />   
    <label for="pvoutputSystemId">PVOutput System id:</label><input type="text" name="pvoutputSystemId" value="{{data.inverter.pvoutputSystemId}}" />   See your PVoutput settings page.<br />
    <label for="pvoutputWSLTeamMember">WSL Team Member:</label>
    {{#if data.inverter.pvoutputWSLTeamMember}}
    wel lid
    {{else}}
    <a href="index.php#social">niet lid</a>
    {{/if}}
    <br />
    
    <button type="button" id="btnInverterSubmit">Save</button>
  </fieldset>
</form> 

<br />
<form>
      <fieldset>
    <legend>System Panels</legend>
    Create panels for the strings you have on your rooftop.<br>
{{#data.inverter.panels}}
    <form>
      <input type="hidden" name="s" value="save-panel" />   
      <input type="hidden" name="id" value="{{this.id}}" />   
      <input type="hidden" name="inverterId" value="{{this.inverterId}}" />   
      <fieldset>
    <legend>Panel/String: {{this.id}}</legend>
        <label for="description">description:</label><input type="text" name="description" value="{{this.description}}" />   SolarPanel 265Wp 123-32/23+<br />   
        <label for="roofOrientation">roof orientation:</label><input type="text" name="roofOrientation" value="{{this.roofOrientation}}" />0=north,90=east,180=south,270=west<br />   
        <label for="roofPitch">roof pitch:</label><input type="text" name="roofPitch" value="{{this.roofPitch}}" />   0 = horizontal, 90 = vertical<br />   
        <label for="amount">Panels in string:</label><input type="text" name="amount" value="{{this.amount}}" />   20<br />   
        <label for="wp">Watt-peak of one panel:</label><input type="text" name="wp" value="{{this.wp}}" />   265<br />   
        <button type="button" id="btnPanelSubmit{{this.id}}" class="panel_submit">Save</button>
      </fieldset>
    </form>
{{/data.inverter.panels}}
<div id="new_panels"></div>
<br>
    Example:<br>
    When your system has 2 strings, each 10 panels of 195Wp, 1 string on orientation West(270) and 1 string on orientation East(90) both with a roof pitch of 45<br>
    You make 2 panels and fillout the field with the information you now of what is your best guess.<br>
    
{{#if_gt data.inverter.id compare=0}}
    <button type="button" id="btnNewPanel">New panel</button><br/>   
{{else}}
Panels can be added after saving the new inverter
{{/if_gt}}
      </fieldset>
    </form>