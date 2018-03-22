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

	<div class = "content">
		<?php
			include 'includes/database.php';
			require_once 'includes/config.php';
			$database = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$art_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
			$art = $database->getArtById($art_id);
            $categories = $database->getCategoriesByArt($art_id);
			$cat_name = array();
			while($row = $categories->fetch_assoc()){
				$cat_name[] = $row;
			}
			$c_name = implode(", ",$cat_name);
            $name = $art['name'];
            $image_path = $art['file_path'];
            $description = $art['description'];
			$price = $art['price'];
			if ($admin_logged) {
				echo("<div id='login_form'>
						<div id = 'expand_form'>Edit Photo</div>
						<form method='post' enctype = 'multipart/form-data' id = 'blog_form'>
							<input type='text' name='name' placeholder='Change name'>
							<textarea name='description' placeholder='Change description'></textarea>
							<input type='text' name='price' placeholder='Change price'>
							<input type='file' name='newPhoto' id='fileToUpload'>");
			/*$result = $database->getCategories();
			while ($row = mysqli_fetch_array($result)) {
				$name = $row[ 'name' ];
				echo("<br><input type=\"checkbox\" name=\"categories[]\" value= \"$name\" >$name");
			}*/
				echo ("<input type='submit' name='upload' value='Change'>
						</form>
					</div>");
				$error = '';
				if (isset($_POST['upload'])) {
					$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
					$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
					$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
					$categories = filter_input(INPUT_POST, 'categories', FILTER_SANITIZE_STRING);
					$image_path = getImagePath('newPhoto');
					$art_id =filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
					$database->updateArt($art_id,$name,$price,$description,$image_path,$categories);
					if (strlen($name > 400)) {
						$error = 'Photo name too long.';
					} else if (strlen($description) > 1000) {
						$error = 'Description too long.';
					}
				}
				echo $error;
				echo("<div class = 'form'>
						<form method = 'post'>
							<input type = 'submit' name = 'delete' value = 'Delete This Photo'>
						</form>
					</div>");
				if(isset($_POST['delete'])){
					$database->deleteArt($art_id);
					header("Location: artwork.php");
				}
			}
            
            echo("<div class = 'img_desc'><div><h1>$name</h1></div>
					<!--<div>Categories: $c_name</div>-->
					<div>Description: $description</div>
					<div>Price: $price</div></div>
					<div class = 'image_focus'><img src='$image_path' alt='error loading image'></div>
					");
			
			?>
	</div>

	<div id = "login">
		<a href = "login.php">Login</a>
	</div>

</body>
</html>