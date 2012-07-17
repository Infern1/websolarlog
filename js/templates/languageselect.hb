<form method="POST">
<select name='user_lang' onchange='this.form.submit()'>
{{#data.languages}}
    {{#ifCond ../data.currentlanguage this}}
      <option selected="selected">{{this}}</option>
    {{else}}
      <option>{{this}}</option>
    {{/ifCond}}
{{/data.languages}}
</select>
</form>