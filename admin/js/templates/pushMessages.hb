
 <div class="column span-30" id="" name="">
    <div class="column span-7" style="font-weight:800;">Date Time</div>
    <div class="column span-5" style="font-weight:800;">Title</div><br>
    <div class="column span-25" style="font-weight:800;">- message</div>
</div>
<hr>
 <div class="column span-30" id="" name="">
{{#data.messages}}
<div class="column span-7">{{timestampDateFormat this.time format="DD-MM-YYYY HH:mm:ss"}}</div>
<div class="column span-7">{{this.title}}</div><br>
<div class="column span-25">- <div class="column span-23">{{this.message}}</div></div>
<hr>
{{/data.messages}}
</div>
