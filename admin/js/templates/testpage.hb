<h1>We have runned some tests to check your installation</h1>
<div id="sqlite">
    <div>SQLlite installed? {{data.test.sqlite}} {{data.test.sqlite_version}}</div>
    </div>
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