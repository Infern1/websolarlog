<p>By selecting the experimantal versions, you will be able to choose unstable releases.
So only use this if you know what you are doing.</p>
<br/>
<input type="checkbox" id="chkBeta" value="1" {{#if beta}}checked='checked'{{/if}}/>&nbsp;<b>Show Beta versions<b><br />
<span style="color:#ff0000">Not supported!</span>
<br/><br/>
<input type="checkbox" id="chkExperimental" value="1" {{#if experimental}}checked='checked'{{/if}}/>&nbsp;<b>Show Trunk versions<b><br />
<span style="color:#ff0000">Not supported!</span>
<br/><br/>
<hr>
{{checkboxWithHidden 'chkNewTrunk' chkNewTrunk}}&nbsp;<b>Notify me on new trunk releases:<b>