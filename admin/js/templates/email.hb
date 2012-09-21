<form>
  <input type="hidden" name="s" value="save-email" />
  <fieldset>
    <legend>eMail</legend>
    <label for="emailTo">to:</label><input type="text" name="emailTo" value="{{data.emailTo}}" /><br />
    <label for="emailFrom">from:</label><input type="text" name="emailFrom" value="{{data.emailFrom}}" /><br />
    <label for="emailAlarms">Receive alarms:</label><input type="checkbox" name="emailAlarms" value="1" {{#if data.emailAlarms}}checked=checked{{/if}}/><br />
    <label for="emailEvents">Receive events:</label><input type="checkbox" name="emailEvents" value="1" {{#if data.emailEvents}}checked=checked{{/if}}/><br />
    <label for="emailReports">Receive reports:</label><input type="checkbox" name="emailReports" value="1" {{#if data.emailReports}}checked=checked{{/if}}/><br />
    <button type="button" id="btnEmailSubmit">Save</button>
  </fieldset>
</form> 