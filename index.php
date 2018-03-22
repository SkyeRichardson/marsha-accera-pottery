<!DOCTYPE html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Marsha Acerra Pottery Homepage</title>
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
							include "includes/functions.php";
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
	<div class = "content">
		<div id = "coverphoto">
			<?php
				include 'includes/database.php';
				require_once 'includes/config.php';
				$database = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				$art_images = $database->getArt();
				$all_images = array();
				while ($row = $art_images->fetch_assoc()){
					$name = $row['name'];
					$file_path = $row['file_path'];
					$all_images[] = $file_path;
				}
				$key = array_rand($all_images);
				$selected_image = $all_images[$key];
				echo("<div class = 'cover'>
						<img src='$selected_image' alt='error loading image'/>
						</div>");
			?>
		</div>
		<div id = "welcome">
			Welcome to Earth Arts Studio! Check out the links above to learn more about my artwork, lessons, and more!
		</div>
	</div>
	
</body>
</html>