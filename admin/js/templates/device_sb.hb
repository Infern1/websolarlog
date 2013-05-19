{{#data.inverters}}
<button type="button" id="btnInverter_{{this.id}}" class="inverter_select" style="width:100%;">{{this.name}}</button><br/>
{{/data.inverters}}
<br />
<button type="button" id="btnInverter_-1" class="inverter_select" style="width:100%;">New devices</button><br/><br>
WSL supports:<br>
<br>
Solar inverters:<br>
<div class="span span-16">
<ul>
	<li>PowerOne (Aurora RS485)</li>
	<li>Diehl (Ethernet)</li>
	<li>SMA (SMA-get RS485)</li>
	<li>SMA (SMA-spot BlueTooth)</li>
</ul>
<br>
Metering devices:<br>
<ul>
	<li>Dutch Smart Meter (P1 serial)</li>
</ul>
<br>
Online services:<br>
<ul>
	<li>OpenWeatherMap(www connection required)</li>
</ul></div>