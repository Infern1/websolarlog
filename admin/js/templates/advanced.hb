<form>
  <input type="hidden" name="s" value="save-advanced" />
  <fieldset>
    <legend>Advanced settings</legend>
    <label for="co2kwh">CO2 avoided per kWh:</label><input type="text" name="co2kwh" value="{{data.co2kwh}}"/>in grams<br />
    <hr>
    Below you need to set the path of the software you use to communicate with the device.<br>See the examples for help.<br />
    <label for="aurorapath">Aurora PowerOne(RS485):</label><input type="text" name="aurorapath" value="{{data.aurorapath}}" size="50"/><br />
    <label for="smagetpath">sma-get(RS485):</label><input type="text" name="smagetpath" value="{{data.smagetpath}}"  size="50"/><br />
    <label for="smaspotpath">sma-spot(BT):</label><input type="text" name="smaspotpath" value="{{data.smaspotpath}}"  size="50"/><br />
    <label for="smaspotWSLpath">sma-spot >2.0.6(BT):</label><input type="text" name="smaspotWSLpath" value="{{data.smaspotWSLpath}}"  size="50"/><br />
    <label for="smartmeterpath">smartmeter:</label><input type="text" name="smartmeterpath" value="{{data.smartmeterpath}}"  size="50"/><br />
    <div id="examples">
    <h3>examples</h3>
    <div>
    <strong>Smartmeter:</strong><br />
    python3 /var/www/websolarlog/scripts/P1.py /dev/ttyUSB0<br />(executor script serialDevice)<br />
    <br />
    <strong>SMAspot:</strong><br />
    /home/root/smaspot/./smaspot<br />(script)<br />you need to configure the inverter in SMAspot<br /><br />
    <strong>Smartmeter through PHP:</strong><br />
    php /var/www/wsl/utils/wslP1.php /dev/ttyUSB0<br />(executor script serialDevice)<br />
    <br />
    <strong>Aurora PowerOne:</strong><br />
    /home/root/aurora-1.8.3/aurora -Y5 -l5 -M10 /dev/ttyUSB0<br />(executor parameters serialDevice)<br />
    <br />
    </div></div>
    <br />
    <hr>
    activate debugging:<br />
    <label for="debugmode">Debug mode:</label>{{checkboxWithHidden 'debugmode' data.debugmode}}<br />
    <hr>
    configure stat-tools:<br />
    <label for="googleAnalytics">Google Analytics:</label><input type="text" name="googleAnalytics" value="{{data.googleAnalytics}}" />(XX-00000000-0)<br />
    <label for="piwikServerUrl">Piwik server url:</label><input type="text" name="piwikServerUrl" value="{{data.piwikServerUrl}}" /><br />
    <label for="piwikSiteId">Piwik site id:</label><input type="text" name="piwikSiteId" value="{{data.piwikSiteId}}" /><br />
    <button type="button" id="btnAdvancedSubmit">Save</button>
  </fieldset>
</form> 
 
<script>
  $(function() {
$("#examples").accordion({ 
        event: "click",
        active: false,
        collapsible: true,
        autoHeight: false

    });
  });
  </script>
	