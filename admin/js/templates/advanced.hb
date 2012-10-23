<form>
  <input type="hidden" name="s" value="save-advanced" />
  <fieldset>
    <legend>Advanced settings</legend>
    <label for="co2kwh">co2-kwh:</label><input type="text" name="co2kwh" value="{{data.co2kwh}}"/><br />
    <label for="aurorapath">aurora path:</label><input type="text" name="aurorapath" value="{{data.aurorapath}}" /><br />
    <label for="smagetpath">sma-get path:</label><input type="text" name="smagetpath" value="{{data.smagetpath}}" /><br />
    <button type="button" id="btnAdvancedSubmit">Save</button>
  </fieldset>
</form> 