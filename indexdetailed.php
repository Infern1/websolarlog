<?php
include("styles/globalheader.php");
if(!empty($_POST['invtnum'])) {
    $invtnum=$_POST['invtnum'];
} else {
    $invtnum=1;
}

$dir = 'data/invt'.$invtnum.'/csv/';
$output = scandir($dir);
$output = array_filter($output, "tricsv");
sort($output);
$contalogs=count($output);

$ollog=$output[0];
$lstlog=$output[$contalogs-1];

$startdate=(substr($ollog,0,4)).",".(substr($ollog,4,2))."-1,".(substr($ollog,6,2));
$stopdate=(substr($lstlog,0,4)).",".(substr($lstlog,4,2))."-1,".(substr($lstlog,6,2));
$prefilldate=(substr($lstlog,6,2))."/".(substr($lstlog,4,2))."/".(substr($lstlog,0,4));
?>
<script>
  $(function() {
    $( "#datepickid" ).datepicker({ dateFormat: 'dd/mm/yy' ,minDate: new Date(<?php echo $startdate;?>), maxDate: new Date(<?php echo $stopdate;?>)});
    });
</script>
<table border="0" cellspacing="0" cellpadding="5" width="40%" align="left">
	<tr>
		<td><?php if ($NUMINV>1) {
		    echo "<form method='POST' action='indexdetailed.php'>
		    <select name='invtnum' onchange='this.form.submit()'>";
		    for ($i=1;$i<=$NUMINV;$i++) {
		        if ($invtnum==$i) {
		            echo "<option SELECTED value=$i>";
		        } else {
		            echo "<option value=$i>";
		        }
		        echo "$lgINVT$i</option>";
		    }
		    echo "</select></form>";
		} ?></td>
		<td>
			<form method="POST" action="detailed.php" name="chooseDateForm" id="chooseDateForm" action="#">
				<?php echo "$lgCHOOSEDATE";?>
				:&nbsp; <input name="date1" id="datepickid" value="<?php echo $prefilldate;?>" size="7" maxlength="10" />
		
		</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkpower" /> <?php echo "$lgPOWERINSTANT";?></td>
		<td><input type="checkbox" name="checkavgpower" /> <?php echo "$lgPOWERAVG";?> <img src="images/info10.png" width="10" height="10" border="0" title="<?php echo "$lgPOWERAVGINFO";?>" /></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkI1P" /> <?php echo "$lgPOWER1";?></td>
		<td><input type="checkbox" name="checkI2P" /> <?php echo "$lgPOWER2";?></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkI1V" /> <?php echo "$lgVOLTAGE1";?></td>
		<td><input type="checkbox" name="checkI2V" /> <?php echo "$lgVOLTAGE2";?></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkI1A" /> <?php echo "$lgCURRENT1";?></td>
		<td><input type="checkbox" name="checkI2A" /> <?php echo "$lgCURRENT2";?></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkGV" /> <?php echo "$lgGRIDVOLTAGE";?></td>
		<td><input type="checkbox" name="checkGA" /> <?php echo "$lgGRIDCURRENT";?></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkFRQ" /> <?php echo "$lgFREQ";?></td>
		<td><input type="checkbox" name="checkEFF" /> <?php echo "$lgEFFICIENCYINVT";?></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="checkINVT" /> <?php echo "$lgINVERTERTEMP";?></td>
		<td><input type="checkbox" name="checkBOOT" /> <?php echo "$lgBOOSTERTEMP";?></td>
	</tr>
	<tr>
		<td align=center colspan=3><br>&nbsp;<input type="submit" value="   <?php echo $lgOK;?>   " /></td>
	</tr>
	<tr>
		<td align=center colspan=3><input type="hidden" name="invtnum" value="<?php echo "$invtnum";?>" /></td>
	</tr>
	</form>
</table>

<?php include("styles/".$user_style."/footer.php"); ?>