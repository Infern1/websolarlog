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

<form>
  <input type="hidden" name="s" value="save-advanced-social" />
  <fieldset>
    <legend>Social settings</legend>
    <label for="Twitter">Twitter:</label><input type="text" name="Twitter" value="{{data.social.TwitterDisplayName}}" disabled="disabled"/>
    <span id="attachTwitter">
	{{#if_eq data.social.TwitterAttached compare="0"}}
	<a href="admin-server.php?s=attachTwitter" target="_blank">Attach SunCounter to you Twitter</a>
	{{/if_eq}}
	{{#if_eq data.social.TwitterAttached compare="1"}}
	<br>
	<div class="column span-6" >
		<a href="https://www.twitter.com/{{data.social.TwitterDisplayName}}" target="_blank" title="Go to my Twitter page"><img src="images/link.png" class="icon">We are connected</a>
	</div>
	<div class="column span-6">
		<a href="#" id="sendTweet"><img src="images/email_go.png" class="icon">Send a test Tweet</a>
	</div>
	
	{{/if_eq}}
	
	</div>


    </span>
    <br />
    <!--<button type="button" id="btnAdvancedSubmit">Save</button>-->
  </fieldset>
</form> 