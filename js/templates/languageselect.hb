<form method="POST">
<select name='user_lang' onchange='this.form.submit()'>
{{#data.languages}}
    {{#if_eq ../data.currentlanguage compare=this}}
      <option selected="selected">{{this}}</option>
    {{else}}
      <option>{{this}}</option>
    {{/if_eq}}
{{/data.languages}}
</select>
</form>