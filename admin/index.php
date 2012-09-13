<?php ?>
<!Doctype html>
<html lang="en">
<head>
	<title>Admin interface</title>
	<meta charset="utf-8"> 
	<link rel="shortcut icon" href="css/images/favicon.ico" />
	<link rel="stylesheet" href="css/main.css" type="text/css" />
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../js/jquery.calculation.min.js"></script>
	<script type="text/javascript" src="js/admin.js"></script>
</head>
<body>
	<!-- Wrapper -->
	<div id="wrapper">
		<div class="shell">
			<!-- Header -->
			<div id="header">
				<!-- Logo -->
				<h1 id="logo"><a href="#" title="home">GreenLogo</a></h1>
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
					<li><a href="#" id="btnGrid">Grid</a></li>
					<li><a href="#" id="btnMail">Mail</a></li>
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
					<main-header>
						<h1>WSL :: Configuration</h1>
					</main-header>
					<div id="content">
					<header>
						<h2>Welcome to the configuration</h2>
					</header>
					<div class="cl"></div>
			<!-- Defining Content Section -->
			<section>
				<div style="height:300px;">
						What is the predicted kWh a year:<br>
						
						<input name="totalProdKWH" class="monthProd" id="totalKWHProd" value=""><br><br>
						<span class="monthName">Month</span>
						<span class="monthName">Jan</span>
						<span class="monthName">Feb</span>
						<span class="monthName">Mar</span>
						<span class="monthName">Apr</span>
						<span class="monthName">May</span>
						<span class="monthName">Jun</span>
						<span class="monthName">Jul</span>
						<span class="monthName">Aug</span>
						<span class="monthName">Sep</span>
						<span class="monthName">Okt</span>
						<span class="monthName">Nov</span>
						<span class="monthName">Dec</span>
						<br>
			
						<span class="monthProd">kWh</span>
						<input class="monthProd" id="janKWH" value="">
						<input class="monthProd" id="febKWH" value="">
						<input class="monthProd" id="marKWH" value="">
						<input class="monthProd" id="aprKWH" value="">
						<input class="monthProd" id="mayKWH" value="">
						<input class="monthProd" id="junKWH" value="">
						<input class="monthProd" id="julKWH" value="">
						<input class="monthProd" id="augKWH" value="">
						<input class="monthProd" id="sepKWH" value="">
						<input class="monthProd" id="octKWH" value="">
						<input class="monthProd" id="novKWH" value="">
						<input class="monthProd" id="decKWH" value="">
						
						<br>
						<span class="monthProd">%</span>
						<input class="monthProd" id="janPER" DISABLED value="">
						<input class="monthProd" id="febPER" DISABLED value="">
						<input class="monthProd" id="marPER" DISABLED value="">
						<input class="monthProd" id="aprPER" DISABLED value="">
						<input class="monthProd" id="mayPER" DISABLED value="">
						<input class="monthProd" id="junPER" DISABLED value="">
						<input class="monthProd" id="julPER" DISABLED value="">
						<input class="monthProd" id="augPER" DISABLED value="">
						<input class="monthProd" id="sepPER" DISABLED value="">
						<input class="monthProd" id="octPER" DISABLED value="">
						<input class="monthProd" id="novPER" DISABLED value="">
						<input class="monthProd" id="decPER" DISABLED value="">
						<!--  <input name="totalProdPER" class="monthProd" DISABLED id="totalPERProd" value="">-->
						<br><br>
						<span class="monthBAR">Graphs</span>
						<p class="monthBAR"><img class="monthIMG" id="janBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="febBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="marBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="aprBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="mayBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="junBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="julBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="augBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="sepBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="octBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="novBAR" src=""/></p>
						<p class="monthBAR"><img class="monthIMG" id="decBAR" src=""/></p>
						
						
						
					</div>
				<!-- Defining content section article -->
					</div>
					<!-- END Content -->
					<!-- Sidebar -->
					<div id="sidebar">
						reserverd :D
					</div>
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
					<p><a title="Home" href="#">Home</a><span>&#47;</span><a title="Who We Are" href="#">Who We Are</a><span>&#47;</span><a title="Our Projects" href="#">Our Projects</a><span>&#47;</span><a title="What We Do" href="#">What We Do</a><span>&#47;</span><a title="How We Do" href="#">How We Do</a><span>&#47;</span><a title="Get In Touch" href="#">Get In Touch</a></p>
				</div>	
				<div id="footer-bottom"></div>
			</div>
			<!-- END Footer -->
		</div>
	</div>
	<!-- END Wrapper -->
</body>
</html>