<form id="frm_comm_test">
  <fieldset>
    <legend>Test this communication on a device</legend>
    <select id="deviceSelect" name="device" style="width:148px;">       	
   		<option value="-1">Select an device</option>
       	{{#devices}}
       		<option value="{{this.id}}">{{this.name}}</option>
    	{{/devices}}
    </select>
  </fieldset>
</form>

<div id="device_info" style="display:none">
	<form>
		<input type="hidden" name="deviceId" id="device_id" />
		<input type="hidden" name="communicationId" value="{{communicationId}}" />
		<span style="width: 6em; display:inline-block;">name</span><input type="text" id="device_name" name="name" disabled="disabled"/><br />
		<span style="width: 6em; display:inline-block;">address</span><input type="text" id="device_address" name="address" disabled="disabled"/><br />
		<span style="width: 6em; display:inline-block;">device API</span><span id="device_api" /><br />
		
		<input type="button" id="btnTestCommunication" value="Start test" />
	</form>
</div>

<div id="test_results" />