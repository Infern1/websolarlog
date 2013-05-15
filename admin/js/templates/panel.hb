<form>
  <input type="hidden" name="s" value="save-panel" />
  <input type="hidden" name="id" value="{{data.panel.id}}" />
  <input type="hidden" name="inverterId" value="{{data.panel.inverterId}}" />
  <fieldset>
    <legend>New Panel/String</legend>
    <label for="description">description:</label><input type="text" name="description" value="{{data.panel.description}}" />SolarPanel 265Wp 123-32/23+<br />
    <label for="roofOrientation">roof orientation:</label><input type="text" name="roofOrientation" value="{{data.panel.roofOrientation}}" />0 = north, 90 = east, 180 = south, 270 = west<br />
    <label for="roofPitch">roof pitch:</label><input type="text" name="roofPitch" value="{{data.panel.roofPitch}}" />0 = horizontal, 90 = vertical<br />
    <label for="amount">Panels in string:</label><input type="text" name="amount" value="{{data.panel.amount}}" />20<br />
    <label for="wp">Watt-peak of one panel:</label><input type="text" name="wp" value="{{data.panel.wp}}" />265<br />
    <button type="button" id="btnPanelSubmit-1" class="panel_submit">Save</button>
  </fieldset>
</form>
<br />