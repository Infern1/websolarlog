 <div class="column span-36">
   Here you see the history records that needs to be sent to PVoutput.<br>
   Depending on the history interval of this device, you will see 1 record for every 4-15 min.<br>
   <div class="column span-36"> 
    <div class="column span-7" style="font-weight:800;">Record timestamp</div>
	<div class="column span-7" style="font-weight:800;">Sendable record</div>
		<div class="column span-7" style="font-weight:800;">recieved state</div>

	<div class="column span-7" style="font-weight:800;">last try to sent</div>
	    
{{#each data}}
	{{#each this}}
		<div class="column span-7">{{timestampDateFormat this.time format="DD-MM-YYYY  hh:mm:ss"}}</div>
		 
		 {{#if_eq this.pvoutputSend compare=1}}
		 	<div class="column span-7">true</div>
		 	
		 	{{#if_eq this.pvoutput compare=1}}
		 		<div class="column span-7">recieved by PVo</div>
		 	{{/if_eq}}
		 	
		 	{{#if_eq this.pvoutput compare=2}}
		 		<div class="column span-7">not recieved by PVo</div>
		 	{{/if_eq}}
		 
		 <div class="column span-7">
		 	{{timestampDateFormat this.pvoutputSendTime format="DD-MM-YYYY  hh:mm:ss"}}
		 </div>
		 {{else}}
		 
		 	<div class="column span-7">.</div>
		 	
		 	{{#if_eq this.pvoutput compare=0}}
		 		<div class="column span-7">We don't want to send it.</div>
		 	{{/if_eq}}
		  <div class="column span-7">
		 	.
		 </div>		 	
		 {{/if_eq}}


		 <div class="column span-1">{{PvOutputDataRow this}}</div>
	{{/each}}
{{/each}}
</div></div>