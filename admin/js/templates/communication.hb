<form>
  <input type="hidden" name="s" value="save-communication" />
  <fieldset>
    <legend>Communication</legend>
    <label for="comPort">port:</label><input type="text" name="comPort" value="{{data.comPort}}" /><br />
    <label for="comOptions">options:</label><input type="text" name="comOptions" value="{{data.comOptions}}" /><br />
    <label for="comDebug">debug:</label><input type="checkbox" name="comDebug" value="1" {{#if data.comDebug}}checked=checked{{/if}}/><br />
    <button type="button" id="btnCommunicationSubmit">Save</button>
  </fieldset>
</form> 