<?php
session_start(); 
?>
<!Doctype html>
<html lang="en">
<head>
<title>Configuration</title>
<meta charset="utf-8">
<link rel="shortcut icon" href="css/images/favicon.ico" />
<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print"/>
<!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"/><![endif]-->

<link rel="stylesheet" href="../css/jquery.pnotify.default.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/main.css" type="text/css" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/jquery.calculation.min.js"></script>
<script type="text/javascript" src="../js/jquery.pnotify.min.js"></script>
<script type="text/javascript" src="../js/handlebars.js"></script>
<script type="text/javascript" src="../js/helpers.js"></script>
<script type="text/javascript" src="../js/websolarlog.js"></script>
<script type="text/javascript" src="js/admin.js"></script>
</head>
<body>
	<!-- Wrapper -->
	<div id="wrapper">
		<div class="shell">
			<!-- Header -->
			<div id="header">
				<!-- Logo -->
				<h1 id="logo"><a href="../" title="Dashboard">WebSolarLog</a></h1>
				<div class="socials">
					<a title="in" class="in" href="#">in</a>
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
					<li class="nav-start"><a href="#" id="btnGeneral">General</a></li>
					<li><a href="#" id="btnInverters">Inverters</a></li>
					<li><a href="#" id="btnEmail">eMail</a></li>
					<li><a href="#" id="btnAdvanced">Advanced</a></li>
					<li><a href="#" id="btnUpdate">Update</a></li>
					<li><a href="#" id="btnBackup">Backup</a></li>
					<li class="nav-end"><a href="#" id="btnTestPage">Test page</a></li>
				</ul>
				<div class="cl"></div>
			</div>
			<!-- END Navigation -->
			<!-- Main -->
			<div id="main">
				<div id="main-top"></div>
				<div id="main-middle">
					<!-- Content -->
					<header>
						<h1>WSL :: Configuration</h1>
					</header>
					<div id="content">
    					<header>
    						<h2>Welcome to the configuration</h2>
    					</header>
            			<!-- Defining content section article -->
					</div>
					<!-- END Content -->
					<!-- Sidebar -->
					<div id="sidebar"></div>
					<!-- END Sidebar -->
					<div class="cl"></div>
				</div>
				<div id="main-bottom"></div>
			</div>
			<!-- END Main -->
			<!-- Footer -->
			<div id="footer">
				<div id="footer-middle">
					<span class="author"><a href="#">..</a></span>
					<p></p>
				</div>
				<div id="footer-bottom"></div>
				<div id="JSloadingtime"></div>
			</div>
			<!-- END Footer -->
		</div>
	</div>
	<!-- END Wrapper -->
</body>
</html>