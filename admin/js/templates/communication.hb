<form>
  <fieldset>
    <legend>Communication</legend>
    <label for="port">port:</label><input type="text" name="port" value="{{data.comPort}}" /><br />
    <label for="options">options:</label><input type="text" name="options" value="{{data.comOptions}}" /><br />
    <label for="debug">debug:</label><input type="checkbox" name="debug" value="1" {{#if data.comDebug}}checked=checked{{/if}}/><br />
    <button type="button" id="btnCommunicationSubmit">Save</button>
  </fieldset>
</form> 