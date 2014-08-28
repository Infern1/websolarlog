
 <div class="column span-30" id="" name="">
    <div class="column span-7" style="font-weight:800;">Date Time</div>
    <div class="column span-7" style="font-weight:800;">Title</div>
<div class="column span-4" style="font-weight:800;">?</div>
<div class="column span-4" style="font-weight:800;">?</div>
<br>
   <div class="column span-25">
    <div class="column span-1">-</div>
    <div class="column span-23">Message</div>
</div>
</div>
<hr>
 <div class="column span-30" id="" name="">
{{#data.messages}}
<div class="column span-7">{{timestampDateFormat this.time format="DD-MM-YYYY HH:mm:ss"}}</div>
<div class="column span-7">{{this.title}}</div>
<div class="column span-4">
{{#if_eq this.active compare=1}}
     <!-- time >= :timeHalfYearAgo -->
    {{#if_gt this.halfYearAgo compare=this.time}}
        to old
    {{else}}
        displayed
    {{/if_gt}}
{{else}}
    hidden
{{/if_eq}}
</div>
</div>
<br>
<div class="column span-25">
    <div class="column span-1">-</div>
    <div class="column span-23">{{this.message}}</div>
</div>
<hr>
{{/data.messages}}
</div>