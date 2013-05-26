<h1>We have runned some tests to check your installation</h1>
<br />
<form>
<fieldset>
    <legend>WSL running state:</legend>
		<label>PID file:</label>{{#if data.test.pid.exists}}exists{{else}}doesn't exists{{/if}}<br />
		<label>WSL running state:</label>{{#if data.test.pid.WSLRunningState}}running{{else}}not running{{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.pid.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />
<br>
{{#if data.test.startWSL}}
<font style="color:#FFCC00">WebSolarLog is not running.<br />Please run the following command on the prompt of your Linux system:<br /></font>
<font style="color:#FF0000">sudo {{data.test.startWSL}}</font>
{{/if}}
<br>
<hr>
<br>
		<label>SQLite DB file:</label>{{#if data.test.sdb.exists}}exists{{else}}doesn't exists{{/if}}<br />
		<label>DB changed(<10 sec):</label>{{#if data.test.sdb.dbChanged}}True(this is good){{else}}false(this is bad){{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.sdb.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />

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