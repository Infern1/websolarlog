<form>
  <input type="hidden" name="s" value="save-panel" />
  <input type="hidden" name="id" value="{{data.panel.id}}" />
  <input type="hidden" name="inverterId" value="{{data.panel.inverterId}}" />
  <fieldset>
    <legend>New Panel</legend>
    <label for="description">description:</label><input type="text" name="description" value="{{data.panel.description}}" /><br />
    <label for="roofOrientation">roof orientation:</label><input type="text" name="roofOrientation" value="{{data.panel.roofOrientation}}" /><br />
    <label for="roofPitch">roof pitch:</label><input type="text" name="roofPitch" value="{{data.panel.roofPitch}}" /><br />
    <button type="button" id="btnPanelSubmit-1" class="panel_submit">Save</button>
  </fieldset>
</form>
<br />