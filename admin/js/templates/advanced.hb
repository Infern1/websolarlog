<form>
  <input type="hidden" name="s" value="save-advanced" />
  <fieldset>
    <legend>Advanced settings</legend>
    <label for="co2kwh">co2-kwh:</label><input type="text" name="co2kwh" value="{{data.co2kwh}}"/><br />
    <label for="aurorapath">aurora path:</label><input type="text" name="aurorapath" value="{{data.aurorapath}}" /><br />
    <label for="smagetpath">sma-get path:</label><input type="text" name="smagetpath" value="{{data.smagetpath}}" /><br />
    <label for="debugmode">Debug mode:</label><input type="checkbox" name="debugmode" value="1" {{#if data.debugmode}}checked=checked{{/if}}/><br />
    <label for="googleAnalytics">Google Analytics:</label><input type="text" name="googleAnalytics" value="{{data.googleAnalytics}}" />(XX-00000000-0)<br />
    <button type="button" id="btnAdvancedSubmit">Save</button>
  </fieldset>
</form> 