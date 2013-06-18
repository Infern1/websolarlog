<h1>We have runned some tests to check your installation</h1>
<br />
<form>
<fieldset>
    <legend>WSL running state:</legend>
		<label>PID file:</label>{{#if data.test.pid.exists}}exists{{else}}doesn't exists{{/if}}<br />
		<label>WSL running state:</label>{{#if data.test.pid.WSLRunningState}}running{{else}}not running{{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.pid.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />
<div class="cl"></div>
{{#if data.test.startWSL}}
<font style="color:#FFCC00">WebSolarLog is not running.</font><br />Please run the following command on the prompt of your Linux system to start WebSolarLog:<br />
<font style="color:#FF0000">sudo {{data.test.startWSL}}</font>
{{/if}}
<br>
</fieldset>
</form>
<form>
<fieldset>
    <legend>DB state:</legend>
    	<strong>Exists?</strong><br>
    	<label>SQLite DB file:</label>{{#if data.test.sdb.exists}}exists{{else}}doesn't exists{{/if}}<br />
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
    If you experience any problems with WebSolarLog, please check your logfiles.<br>
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
    <legend>Which extensions are loaded/installed:</legend>
<div id="extensions">
    <div>
			{{#data.test.extensions}}
				{{this.name}}: {{this.status}}<br>
			{{/data.test.extensions}}
	</div>
</div>
<br />
<div id="encryptiontest">
    mcrypt_module_open function exists <b>{{#if data.test.mcrypt_module_open}}passed{{else}}failed{{/if}}</b><br />
    Encrypting test <b>{{#if data.test.encrypting}}passed{{else}}failed{{/if}}</b><br />
</div>
<div id="sqlite">
    <div>SQLlite version: {{data.test.sqlite_version}} (needs to be >= 3.7.11)</div>
</div>
</fieldset>
</form>