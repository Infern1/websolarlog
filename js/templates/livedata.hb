<div class="table" style="width:220px;">
    <h2>MppOne</h2>
    <div class="tr">
        <div class="td" style="width:120px;">I1V</div><div class="td" id="I1V">{{data.valueI1V}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">I1A</div><div class="td" id="I1A">{{data.valueI1A}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">I1P</div><div class="td" id="I1P">{{data.valueI1P}}</div>
	</div>
    <h2>MppTwo</h2>
    <div class="tr">
        <div class="td" style="width:120px;">I2V</div><div class="td" id="I2V">{{data.valueI2V}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">I2A</div><div class="td" id="I2A">{{data.valueI2A}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">I2P</div><div class="td" id="I2P">{{data.valueI2P}}</div>
	</div>

    <h2>Grid&nbsp;&nbsp;&nbsp;</h2>
    <div class="tr">
        <div class="td" style="width:120px;">GV</div><div class="td" id="GV">{{data.valueGV}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">GA</div><div class="td" id="GA">{{data.valueGA}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">GP</div><div class="td" id="GP">{{data.valueGP}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">FRQ</div><div class="td" id="FRQ">{{data.valueFRQ}}</div>
	</div>
	
    <h2>Misc&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
    <div class="tr">
        <div class="td" style="width:120px;">SDTE</div><div class="td" id="SDTE">{{data.valueSDTE}}</div>
    </div>

    <div class="tr">
        <div class="td" style="width:120px;">INVT</div><div class="td" id="INVT">{{data.valueINVT}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">BOOT</div><div class="td" id="BOOT">{{data.valueBOOT}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">KHWT</div><div class="td" id="KHWT">{{data.valueKHWT}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">PMAXOTD</div><div class="td" id="PMAXOTD">{{data.valuePMAXOTD}}</div>
	</div>
    <div class="tr">
        <div class="td" style="width:120px;">PMAXOTDTIME</div><div class="td" id="PMAXOTDTIME">{{data.valuePMAXOTDTIME}}</div>
	</div>
	<div style="clear: both;" />
</div>
	<p align="center">
		<font size="-2">{{data.lgPMAX}}<br> <b id='PMAXOTD'>{{data.valuePMAXOTD}}</b> W @ <b id='PMAXOTDTIME'>{{data.valuePMAXOTDTIME}}</b> <br> <?php
		<a href='dashboard.php?invtnum={{data.invtnum}}'>{{data.lgDASHBOARD}}</a></font>
	</p>