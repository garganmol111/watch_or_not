<!DOCTYPE HTML>
<!--
	Full Motion by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html>

	<head>
		<title>Full Motion</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<div class="topnav">
			<a class="active" href="#home">Home</a>
			<?php 
				session_start();
				$loggedIn = $admin = FALSE;
				if(isset($_SESSION['user_id'])) {
					$loggedIn = TRUE;
					if(isset($_SESSION['user_level'])) {
						if($_SESSION['user_level']==1) {
							$admin = TRUE;
						}
					}
				}
				
			?>
			<?php if(!$loggedIn) { ?>
				
					<a href="/reg_login/login.php">Log In</a>
					<a href="/reg_login/register.php">Register</a>
			<?php } ?>
			
			<span class="topnavleft">
				Welcome <?php
							echo isset($_SESSION['full_name'])?' '.$_SESSION['full_name']:'  guest'; 
						?>
			</span>	
			<?php if($loggedIn) { ?>
				<a class="topnavleft" href="/reg_login/logout.php">Logout</a>
			<?php } ?>
		</div> 
	</head>
	<body id="top">

			<!-- Banner -->
			<!--
				To use a video as your background, set data-video to the name of your video without
				its extension (eg. images/banner). Your video must be available in both .mp4 and .webm
				formats to work correctly.
			-->
				<section id="banner" data-video="images/banner">
					<div class="inner">
						<header>
							<h1>Watch or Not</h1>
							<p>A website where the user can see the cumulative score of a movie <br/>
								and decide whether to watch the movie or not.
							</p>
						</header>
						<a href="#main" class="more">Learn More</a>
					</div>
				</section>

			<!-- Main -->
			<div id="main">
					<div class="inner">

						<div class="thumbnails">

						<?php
							include "./reg_login/includes/mysql_connection_link.php";
							
							$query = mysqli_query($dbcl, "SELECT * from movies limit 25");
							if(mysqli_num_rows($query)) {
								$num_iterations = 0;
								$count = 1;
								$str='';
								while($row = mysqli_fetch_array($query)) {
									$img_url = $row['img_url'];
									$m_name = $row['m_name'];
									$year = $row['year'];
									$rating = $row['rating'];
									$genre = $row['genre'];
									$runtime = $row['duration'];
									$description = $row['description'];
									$director = $row['director'];
									$cast = $row['cast'];
									$str .= "<div class='box'>
												<img class='image fit' src='$img_url' alt='' />
												<div class='inner'>
													<h3>$m_name ($year)</h3>
													<h4><u>Rating:</u> $rating</p>
													<p><u>Genre:</u> $genre</p>
													<p><u>Runtime:</u> $runtime</p>
													<p>$description</p>
													<p><u>Directed By:</u> $director</p>
													<p><u>Cast:</u> $cast</p>
													<button class='btnwatch'>Watch</button>
												</div>
											</div>";
								}
								echo $str;
							}

						?>					
						</div>

					</div>
				</div>

			<!-- Footer -->
				<footer id="footer">
					<div class="inner">
						<h2><a href="/contact_form/contact_form.html" >Contact Us!</a></h2>
						<p></p>
						<h2><a href="/subscription_form/subscription_form.html" >Subscribe Now!</a></h2>
						<p></p>

						<ul class="icons">

							<li><a href="http://localhost:63342/Template/form/formpage.html?_ijt=r991aqcbtacnm2cdr8iat49bm4" class="icon fa-envelope"><span class="label">Email</span></a></li>
						</ul>
						<p class="copyright">&copy; Untitled. Design: <a> href=""</a>. Images: <a href="https://unsplash.com/">Unsplash</a>. Videos: <a href="http://coverr.co/">Coverr</a>.</p>
					</div>
				</footer>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/jquery.poptrox.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>