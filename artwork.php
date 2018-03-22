<?php
    session_start();
    $admin_logged = isset($_SESSION['logged_user']) && $_SESSION['logged_user'] === 'admin';
?>
<!DOCTYPE html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Artwork</title>
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

	<div class = "content" id = "artwork_main">
		<?php
			include 'includes/database.php';
			require_once 'includes/config.php';
			$database = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$art_images = $database->getArt();
			
			if ($admin_logged) {
				echo("<div id='login_form'>
						<div id = 'expand_form'>Upload Photo</div>
						<form method='post' enctype = 'multipart/form-data' id = 'blog_form'>
							<input type='text' name='name' placeholder='Name' required>
							<textarea name='description' placeholder='Description'></textarea>
							<input type='text' name='price' placeholder='Price'>
							<input type='file' name='newPhoto' id='fileToUpload' required>
							<input type='submit' name='upload' value='Upload'>
						</form>
					</div>");
				$error = '';
				if (isset($_POST['upload'])) {
					$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
					$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
					$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
					$categories = array();
					$image = getImagePath('newPhoto');
					$database->addArt($name,$price,$description,$image,$categories);
					if (strlen($name > 400)) {
						$error = 'Photo name too long.';
					} else if (strlen($description) > 1000) {
						$error = 'Description too long.';
					}
					header("Location: artwork.php");
					
				}
				echo $error;
			}
			while ($row = $art_images->fetch_assoc()){			
				$name = $row['name'];
				$file_path = $row['file_path'];
				$id = $row['artworkID'];
				echo "<div class = 'thumbnail'>
						<a href='show_image.php?id=$id'>
						<img src='$file_path' alt='error loading image'>
							<div class = 'overlay'>
								<div class = 'overlay_text'>$name<br>Click for details</div>
							</div></a>
						</div>";
			}
			
		?>
	</div>
	<div id = "login">
		<a href = "login.php">Login</a>
	</div>
</body>
</html>