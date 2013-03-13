<form>
  <input type="hidden" name="s" value="save-general" />
  <fieldset>
    <legend>Webpage</legend>
    <label for="title">title:</label><input type="text" name="title" value="{{data.title}}"/><br />
    <label for="subtitle">subtitle:</label><input type="text" name="subtitle" value="{{data.subtitle}}" /><br />
    <label for="url">url:</label><input type="text" name="url" value="{{data.url}}" /><br />
    <label for="location">location:</label><input type="text" name="location" value="{{data.location}}" /><br />
    <label for="latitude">latitude:</label><input type="text" name="latitude" value="{{data.latitude}}" /><br />
    <label for="longitude">longitude:</label><input type="text" name="longitude" value="{{data.longitude}}" /><a href="#" id="btnSetLatLong">}Set coordinates</a><br />
    
    <label for="gaugeMax">Gauge Max Type:</label>
    <select name="gaugeMaxType">
      <option value="panels" {{#if_eq data.gaugeMaxType compare="none"}}selected=selected{{/if_eq}}>Static(panels)</option>
      <option value="average" {{#if_eq data.gaugeMaxType compare="ssl"}}selected=selected{{/if_eq}}>Dynamic(avg. power)</option>
    </select><br />
    
    <label for="timezone">timezone: </label>
    <select name="timezone">
    	{{#each data.timezones}}
    		<option value="{{this}}" {{#if_eq ../data.timezone compare=this}}selected=selected{{/if_eq}}>{{this}}</option>
    	{{/each}}
    </select><br />
    <button type="button" id="btnGeneralSubmit">Save</button>
  </fieldset>
</form> 