<form>
  <input type="hidden" name="s" value="XXXXX" />
  <fieldset>
    <legend>Dropbox</legend>
    {{#if_eq data.available compare=false}}
    We need to connect Dropbox with SunCounter before we can store your data in the cloud.<br>
    Click the link below and follow the steps:<br> 
   <a href="admin-server.php?s=attachDropbox" id="linkAttachDropbox" target="_blank">Attach SunCounter to your Dropbox.</a>
    {{/if_eq}}
    {{#if_eq data.authTokenAvailable compare=true}}
    Something is wrong...
    {{/if_eq}}
    <div id="backups"></div>
  </fieldset>
</form>