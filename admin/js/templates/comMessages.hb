{{#data.message}}
<fieldset style="width:75em;border:1px solid #ff0000;padding:5px;margin:5px;">
<legend style="background-color:#fff;color:#ff0000;border:1px solid #ff0000;">{{this.title}}</legend>
<div>
<div style="float:right;width:4em;" id="hideMessage"><button class="hideMessage" id="hideMessage_{{this.id}}">hide</button></div>
{{this.message}}
</div>
</fieldset>
{{/data.message}}