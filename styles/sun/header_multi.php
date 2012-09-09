<body>
	<table width="90%" border="0" cellspacing="0" cellpadding="0"
		align="center" height="90%">
		<tr bgcolor="#bcd3e5">
			<td class="cadretopleft" width="128"><img
				src="styles/<?php echo $user_style;?>/images/Sunballoon.jpg"
				width="128" height="80" alt="123Aurora">
			</td>
			<td class="cadretop" align="center"><b><?php echo "$TITLE";?>
			</b><br>
			<font size="-1"><?php echo "$SUBTITLE";?>
			</font>
			</td>
			<td class="cadretopright" width="128" align="right"><?php include("styles/selectlanguages.php");?>
			</td>
		</tr>
		<tr valign="top">
			<td height="100%" COLSPAN="3">
				<table width="100%" border="0" cellpadding="0" cellspacing="0"
					align="center" height="100%">
					<tr valign="top">
						<td width="128" class="cadrebotleft" bgcolor="#f6a754"
							height="98%">
							<div class="menu">
								<table width="100%" border="0" cellspacing="0" cellpadding="5"
									align="left">
									<tr>
										<td></td>
									</tr>
									<tr>
										<td><font class="menu"><img
												src="styles/<?php echo $user_style;?>/images/bullet.png"
												width="12" height="12"><a href="index.php"><?php echo "$lgMINDEX";?>
											</a>
										</font>
										</td>
									</tr>
									<?php
									for ($i=1;$i<=$NUMINV;$i++) {
										echo "<tr>
										<td><font class='menu'><img src='styles/$user_style/images/bullet.png' width='12' height='12'><a href='index_mono.php?invtnum=$i'>$lgINVT$i</a></font></td>
										</tr>";
									}
									?>
									<tr>
										<td><font class="menu"><img
												src="styles/<?php echo $user_style;?>/images/bullet.png"
												width="12" height="12"><a href="indexdetailed.php"><?php echo "$lgMDETAILED";?>
											</a>
										</font>
										</td>
									</tr>

									<tr>
										<td><font class="menu"><img
												src="styles/<?php echo $user_style;?>/images/bullet.png"
												width="12" height="12"><a href="indexproduction.php"><?php echo "$lgMPRODUCTION";?>
											</a>
										</font>
										</td>
									</tr>
									<tr>
										<td><font class="menu"><img
												src="styles/<?php echo $user_style;?>/images/bullet.png"
												width="12" height="12"><a href="indexcomparison.php"><?php echo "$lgMCOMPARISON";?>
											</a>
										</font>
										</td>
									</tr>
									<tr>
										<td><font class="menu"><img
												src="styles/<?php echo $user_style;?>/images/bullet.png"
												width="12" height="12"><a href="indexinfo.php"><?php echo "$lgMINFO";?>
											</a>
										</font>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td></td>
									</tr>
								</table>
							</div>
						</td>
						<td class="cadrebotright" bgcolor="#e7e7e7" height="98%">
							<table border="0" cellspacing="10" cellpadding="0" width="100%"
								height="100%" align="center">
								<tr valign="top">
									<td>
										<!-- #BeginEditable "mainbox" -->