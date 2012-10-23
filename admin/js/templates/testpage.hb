<h1>We have runned some tests to check your installation</h1>
<br />
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
    Encrypting test <b>{{#if data.test.encrypting}}passed{{else}}failed{{/if}}</b>
</div>
<div id="sqlite">
    <div>SQLlite version: {{data.test.sqlite_version}}</div>
</div>