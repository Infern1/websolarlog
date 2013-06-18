<h1> page</h1>
<form>
<fieldset>
    <legend>Diagnostics Page:</legend>
We have ran some test for you, so you can check/debug your WebSolarLog installation<br>
<br>
Need help or do you have a question;<br>
<a href="https://groups.google.com/forum/#!forum/websolarlog" target="_blank">Visit our WebSolarLog support-group</a><br>
<br>
When you want to report a bug of have a feature request;<br>
<a href="http://tracker.websolarlog.com" target="_blank">http://tracker.websolarlog.com</a><br>
 
</fieldset>
</form>
<form>
<fieldset>
    <legend>WSL running state:</legend>
		<label>PID file:</label>{{#if data.test.pid.exists}}exists{{else}}doesn't exists{{/if}}<br />
		<label>WSL running state:</label>{{#if data.test.pid.WSLRunningState}}running{{else}}not running{{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.pid.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />
<div class="cl"></div>
{{#if data.test.WSLRunningState}}
<font style="color:#FFCC00">WebSolarLog is running.</font><br />Please run the following command on the prompt of your Linux system to start WebSolarLog:<br />
If you want to stop WebSolarLog run:<br>
<font style="color:#4B8902">sudo {{data.test.commands.stop}}</font><br>
If you want to restart(stop/start) WebSolarLog run:<br>
<font style="color:#4B8902">sudo {{data.test.commands.restart}}</font><br>
If you want to know the running state of WebSolarLog:<br>
<font style="color:#4B8902">sudo {{data.test.commands.status}}</font><br>
{{else}}
<font style="color:#FFCC00">WebSolarLog is not running.</font><br />Please run the following command on the prompt of your Linux system to start WebSolarLog:<br />
<font style="color:#4B8902">sudo {{data.test.commands.start}}</font><br>
or atleast check the running state with:<br>
<font style="color:#4B8902">sudo {{data.test.commands.status}}</font><br>
{{/if}}
<br>
</fieldset>
</form>
<form>
<fieldset>
    <legend>DB state:</legend>
    	<strong>Exists?</strong><br>
    	<label>SQLite DB file:</label>
    	{{#if data.test.sdb.exists}}
    	exists<br />
    	<font style="color:#4B8902">This is good.<br></font>
    	{{else}}
    	<font style="color:#FF0000">doesn't exists.<br></font>
    	{{/if}}
    	<strong>Writes?</strong><br>
		<label>DB changed(<10 sec):</label>{{#if data.test.sdb.dbChanged}}True(this is good){{else}}false(this is bad){{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.sdb.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<br>
		We check the DB file permissions:<br>
		{{#each data.test.dbRights.rights}}
		<div class="column span-18">
		<div class="column span-3 first"><strong>{{@key}}</strong></div>
		<div class="column span-3">{{this.[0]}}</div>
		<div class="column span-3">{{this.[1].[1]}}</div>
		<div class="column span-3">{{this.[2].[1]}}</div>
		<div class="column span-3 last">{{this.[3].[1]}}</div>
		</div>
		{{/each}}
		<div class="cl"></div>
		{{#if_gteq data.test.dbRights.rights.owner.[0] compare="6"}}
			<font style="color:#4B8902">The Owner needs atleast Read and Write (>=6) permissions.<br>It looks like the webserver user has sufficient rights on the database.</font>		
		{{else}}
			<font style="color:#FF0000">It looks like the webserver user has <strong>insufficient rights</strong> on the database</font>
		{{/if_gteq}}
		<div>
		<br />
</fieldset>
</form>
<form>
	<button type="button" id="btnCheckDb">Check the database</button>
</form>

<form>
<fieldset>
    <legend>Logging:</legend>
<div id="extensions">
    <div>
    If you experience any problems with WebSolarLog, please check the WebSolarLog logfiles.<br>
    <br>
    Below you find the logfile locations;<br>
			{{#data.test.logs}}
				<strong>{{this.name}}</strong>:<br>
				{{this.location}}<br>
				<a href="{{this.url}}" target="_blank">{{this.url}}</a><br>
			{{/data.test.logs}}
	</div>
</div>
</fieldset>
</form>

<form>
<fieldset>
    <legend>Check functions/extensions:</legend>
    Here we check if necessary functions and extensions are loaded and working.
<div id="extensions">
    <div>
    Check if extensions are loaded:<br>
			{{#each data.test.extensions}}
					- {{@key}}:
					{{#str_contains @key look_for="sqlite"}}
						{{#if this.status}}
							{{#if ../../../data.test.sqliteVersionMixed}}
								 <strong>Loaded</strong><br><font style="color:#FF0000">It looks like the SQLite DB plugins are mixed.<br>We need SQLite3 and your system got SQLite.</font>
							{{else}}
								 <strong>Loaded</strong><br><font style="color:#4B8902">This looks good.</font>
							{{/if}}
						{{else}}
							{{#if ../../../data.test.sqliteVersionMixed}}
								<strong> Not loaded</strong><br><font style="color:#FF0000">It looks like the SQLite DB plugins are mixed.<br>We need SQLite3 and your system got SQLite.</font>
							{{else}}
								 <strong>Not loaded</strong><br><font style="color:#4B8902">This looks good.</font>
							{{/if}}
						{{/if}}
					{{else}}
						{{#if this.status}}
							<strong>Loaded</strong><br><font style="color:#4B8902">This looks good.</font>
						{{else}}
							<strong>Not loaded</strong><br><font style="color:#FF0000">This looks NOT good.</font>
						{{/if}}
					{{/str_contains}}
				<br>
			{{/each}}
	</div>
</div>
<br />
<div id="encryptiontest">
    mcrypt_module_open function exists <b>{{#if data.test.mcrypt_module_open}}passed{{else}}failed{{/if}}</b><br />
    Encrypting test <b>{{#if data.test.encrypting}}passed{{else}}failed{{/if}}</b><br />
    <br>
</div>
<div id="sqlite">
	Because of a bug in SQLite3 we need atleast version 3.7.11.<br>
    <label>You are on version:</label>{{data.test.sqliteVersion}}<br>
    {{#if data.test.sqliteVersionCheck}}
    <font style="color:#4B8902">We are good on this.</font>
    {{else}}
    <font style="color:#FF0000">Your SQLite version is to old and you may experiance unexpected behaviour.<br>Please update your SQLite version to atleast 3.7.11</font>
    {{/if}}
    
</div>
</fieldset>
</form>