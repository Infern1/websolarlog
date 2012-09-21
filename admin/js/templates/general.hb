<form>
  <input type="hidden" name="s" value="save-general" />
  <fieldset>
    <legend>Webpage</legend>
    <label for="title">title:</label><input type="text" name="title" value="{{data.title}}"/><br />
    <label for="subtitle">subtitle:</label><input type="text" name="subtitle" value="{{data.subtitle}}" /><br />
    <label for="location">location:</label><input type="text" name="location" value="{{data.location}}" /><br />
    <label for="latitude">latitude:</label><input type="text" name="latitude" value="{{data.latitude}}" /><br />
    <label for="longitude">longitude:</label><input type="text" name="longitude" value="{{data.longitude}}" /><br />
    <button type="button" id="btnGeneralSubmit">Save</button>
  </fieldset>
</form> 