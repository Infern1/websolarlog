<p>By selecting the experimantal versions, you will be able to choose unstable releases.
So only use this if you know what you are doing.</p>
<b>Show experimental versions:<b>
<input type="checkbox" id="chkExperimental" value="1" {{#if experimental}}checked='checked'{{/if}}/>
<br/><br/>
<hr>
<b>Notify me on new trunk release:<b>
{{checkboxWithHidden 'chkNewTrunk' chkNewTrunk}}