<br/>
<form id="frm_comm_edit">
  <input type="hidden" name="s" value="save-communication" />
  <input type="hidden" name="id" value="{{data.id}}" />
  <fieldset>
    <legend>Communication: {{data.name}}</legend>
    <label for="name">name:</label><input type="text" name="name" value="{{data.name}}" /><br />
    <label for="uri">uri:</label><input type="text" name="uri" value="{{data.uri}}" /><br />
    <label for="port">port:</label><input type="text" name="port" value="{{data.port}}" /><br />
    <label for="timeout">timeout:</label><input type="text" name="timeout" value="{{data.timeout}}" /><br />
    <label for="optional">optional:</label><input type="text" name="optional" value="{{data.optional}}" /><br />
    <button type="button" id="btnCommunicationSubmit">Save</button>
  </fieldset>
</form> 