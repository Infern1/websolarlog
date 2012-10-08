<br /><br />
<h3>We found the following available versions:</h3>
<form>
    {{#data.versions}}
    <input type="radio" name="version" value="{{this.name}}">
        {{#if this.experimental}}<span style="color:red">{{/if}}{{this.name}}{{#if this.experimental}}</span>{{/if}}<br />
    </input>
    {{/data.versions}}
    <br />
    <p>When selecting the button below WebSolarLog will be updated to the version selected.</p>
    <br />
    <input type="hidden" name="s" value="updater-go" /> 
    <button type="button" id="btnUpdateSubmit">Update</button>
</form>
<div id="update-console"></div>