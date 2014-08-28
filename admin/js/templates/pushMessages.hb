
 <div class="column span-30" id="" name=""></div>
    <div class="column span-7" style="font-weight:800;">Date Time</div>
    <div class="column span-5" style="font-weight:800;">Title</div><br>
    <div class="column span-25">message</div>
    </div>
{{#data.messages}}
<div class="column span-7">{{timestampDateFormat this.time format="HH:mm:ss"}}</div>
<div class="column span-7">{{this.title}}</div><br>
<div class="column span-25">{{this.message}}
{{/data.messages}}
</div>