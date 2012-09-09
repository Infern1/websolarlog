<body>
	<p align="center">
		<b><?php echo "$TITLE";?>
		</b>&nbsp;<font size="-1">(<?php echo "$SUBTITLE";?>)
		</font>
	</p>
	<hr size=1 width="100%">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><a href="index.php"><?php echo "$lgMINDEX";?>
			</a> | <?php
			for ($i=1;$i<=$NUMINV;$i++) {
				echo "<a href='index_mono.php?invtnum=$i'>$lgINVT$i</a> |";
			}
			?> <a href="indexdetailed.php"><?php echo "$lgMDETAILED";?>
			</a> | <a href="indexproduction.php"><?php echo "$lgMPRODUCTION";?>
			</a> | <a href="indexcomparison.php"><?php echo "$lgMCOMPARISON";?>
			</a> | <a href="indexinfo.php"><?php echo "$lgMINFO";?>
			</a>
			</td>
			<td align="right"><?php include("styles/selectlanguages.php");?>
			</td>
		</tr>
	</table>
	<!-- #BeginEditable "mainbox" -->