<body>
<!-- Wrapper -->
<div id="wrapper">
	<div class="shell">
		<!-- Header -->
		<div id="header">
			<!-- Logo -->
			<h1 id="logo"><a href="./" title="Dashboard"><?php echo $config->title; ?></a></h1>
			<h2 id="subLogo"><a href="./" title="Dashboard"><?php echo $config->subtitle; ?></a></h2>
			<div class="socials">
				<a title="pvoutput" class="pvoutput" href="#">in</a>
				<a title="twitter" class="twitter" href="#">twitter</a>
				<a title="flickr" class="flickr" href="#">flickr</a>
				<a title="Facebook" class="facebook" href="#">Facebook</a>
				<div class="cl"></div>
			</div>
		</div>
		<!-- END Header -->
		<!-- Navigation -->
		<div id="navigation">
			<ul>
				<li class="nav-start"><a title="Dashboard" href="index.php"><?php echo _("Dashboard");?></a></li>
				<li><a title="Today" href="today.php"><?php echo _("Day");?></a></li>
				<li><a title="Month" href="month.php"><?php echo _("Month");?></a></li>
				<li><a title="Year" href="year.php"><?php echo _("Year");?></a></li>
				<li><a title="Details" href="details.php"><?php echo _("Details");?></a></li>
				<li><a title="Compare" href="compare.php"><?php echo _("Compare");?></a></li>
				<li><a title="Production" href="production.php"><?php echo _("Production");?></a></li>
				<li class="nav-end"><a title="Misc" href="misc.php"><?php echo _("Other");?></a></li>
			</ul>
			<div class="cl"></div>
		</div>
		<!-- END Navigation -->
		<!-- Main -->
		<div id="main">
			<div id="main-top"></div>
			<div id="main-middle">
				<!-- Content -->
				<div id="content"></div>
				<!-- END Content -->
				<!-- Sidebar -->
				<div id="sidebar"></div>
				<!-- END Sidebar -->
				<div class="cl"></div>
				<div style="display: table;">
        			<div style="display:table-cell;"><div id="right-column"></div></div>
    				<div id="columns" style="display:table-cell; width:100%;"></div>
				</div>
			</div>
			<div id="main-bottom"></div>
		</div>
		<!-- END Main -->
		<!-- Footer -->
		<div id="footer">
			<div id="footer-middle">
				<div style="display: inline-block;">
					<a href="https://sourceforge.net/projects/websolarlog/" target="_blank">WebSolarLog [at] sourceforge.net</a>
				</div>
				<div style="display: inline-block; text-align: right; width: 275px;">
					<div style="display: inline-block;">JS loadtime:&nbsp;</div>
					<div id="JSloadingtime" style="display: inline-block;"></div>
				</div>
				<div style="float: right;">
					<div id="version" style="display: inline-block;">
						<?php echo _("version") ?>:&nbsp;<?php echo($config->version_title . " (build " . $config->version_revision . ")") ?>
					</div>
				</div>
			</div>
		</div>
		<!-- END Footer -->
	</div>
</div>
