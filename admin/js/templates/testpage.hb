<h1>We have runned some tests to check your installation</h1>
<br />
<form>
<fieldset>
    <legend>WSL running state:</legend>
		<label>PID file:</label>{{#if data.test.pid.exists}}exists{{else}}doesn't exists{{/if}}<br />
		<label>WSL running state:</label>{{#if data.test.pid.WSLRunningState}}running{{else}}not running{{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.pid.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.pid.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />

		<label>SQLite DB file:</label>{{#if data.test.sdb.exists}}exists{{else}}doesn't exists{{/if}}<br />
		<label>Database Changed last 10 sec.:</label>{{#if data.test.sdb.dbChanged}}true{{else}}false{{/if}}<br />
		<label>Currenttime:</label>{{timestampDateFormat data.test.sdb.currentTime format="DD-MM-YYYY HH:mm:ss "}}<br />
		<label>last change:</label>{{timestampDateFormat data.test.sdb.ctime format="DD-MM-YYYY HH:mm:ss "}}<br />

</fieldset>
</form>
<div id="extensions">
    <div>Which extensions are loaded/installed?
    	<ul>
			{{#data.test.extensions}}
				<li>{{this.name}}: {{this.status}}</li>
			{{/data.test.extensions}}
		</ul>
	</div>
</div>
<br />
<div id="encryptiontest">
    mcrypt_module_open function exists <b>{{#if data.test.mcrypt_module_open}}passed{{else}}failed{{/if}}</b><br />
    Encrypting test <b>{{#if data.test.encrypting}}passed{{else}}failed{{/if}}</b><br />
</div>
<div id="sqlite">
    <div>SQLlite version: {{data.test.sqlite_version}}</div>
</div>