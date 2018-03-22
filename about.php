<!DOCTYPE html>
<?php
    include 'includes/database.php';
    include "includes/functions.php";
    require_once 'includes/config.php';

    $database = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $settings = $database->getSettings();
?>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>About</title>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Josefin+Sans">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Josefin+Slab">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<?php
		$style_path = 'css/styles.css';
		$version = filemtime( $style_path);
		echo "<link rel='stylesheet'
				href='$style_path?ver=$version'>";
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type = "text/javascript" src = "js/scripts.js"></script>
	<meta name = "viewport" content = "width=device-width">
</head>
<body>
	<div class = "container-fluid">
		
		<div class = "row align-items-center head">
			<div class = "col-md">
				<h1>EarthArts<div class = "bolder">Studio</div></h1>
			</div>
			<div class = "col-md" id = "menu">
				<div id = "mobile_menu">
					Menu
					<nav class = "main_nav">
						<?php
							makeNavBar();
						?>
					</nav>
				</div>
				<div id = "desktop_menu">
					<nav class = "main_nav">
						<?php
							makeNavBar();
						?>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class = "container-fluid content">
		<div class = "row">
			<div class = "col-sm text-center" id = "marsha_photo">
				<?php 
					$image_path = $settings['profile_image_path'];
					print("<img src=$image_path alt='error loading image'>")
				?>
			</div>
			<div class = "col-sm" id = "about_me">
				<?php print(nl2br($settings['biography'])); ?>
			</div>
		</div>
	</div>
	<div id = "login">
		<a href = "login.php">Login</a>
	</div>
</body>
</html>