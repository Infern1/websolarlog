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
<form>
  <input type="hidden" name="s" value="save-smtp" />
  <fieldset>
    <legend>Outgoing mail server</legend>
    <label for="smtpServer">SMTP server:</label><input type="text" name="smtpServer" value="{{data.smtpServer}}" /><br />
    <label for="smtpUser">Username:</label><input type="text" name="smtpUser" value="{{data.smtpUser}}" />(optional)<br />
    <label for="smtpPassword">Password:</label><input type="text" name="smtpPassword" value="{{data.smtpPassword}}" />(optional)<br />
    <button type="button" id="btnSmtpSubmit">Save</button>
  </fieldset>
</form>
<p>
By pressing the button below we will use the saved settings to send an email.<br />
So be sure to save first then use this button.<br />
<form>
    <input type="hidden" name="s" value="send-testemail" />
    <button type="button" id="btnEmailTest">Send an test email</button>
</form>
</p>