<?php
    session_start();
    $admin_logged = isset($_SESSION['logged_user']) && $_SESSION['logged_user'] === 'admin';
?>
<!DOCTYPE html>
<?php
    include 'includes/database.php';
    include "includes/functions.php";
    require_once 'includes/config.php';

    $database = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
?>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Blog</title>
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
	<div class = "content">
		<?php
			if ($admin_logged) {
				$add_blogpost_error = '';
				if (isset($_POST['submit_add_blogpost'])) {
					$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
					$author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
					$text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);

					if (strlen($title) > 400) {
						$add_blogpost_error = 'Title too long.';
					} else if (strlen($author > 400)) {
						$add_blogpost_error = 'Author name too long.';
					}

					if (!$add_blogpost_error) {
						$database->addBlogpost($title, $author, $text);
					}
					header("Location: blog.php");
				}

				print(
					"
					<div id='add_blogpost_form'>
					<div id='expand_form'>New Blog Post</div>
					<form method='post' id = 'blog_form'>
						<input type='text' name='title' placeholder='Title' required/>
						<input type='text' name='author' placeholder='Author' required/>
						<textarea name='text' placeholder='Text' required></textarea>
						<input type='submit' name='submit_add_blogpost' value='Post'/>
					</form>
					</div>
					"
				);
			}
			$blogposts = $database->getBlogposts();
			while ($blogpost = $blogposts->fetch_assoc()) {
				$blogpost_id = $blogpost['blogpostID'];
				$blogpost_author = $blogpost['author'];
				$blogpost_title = $blogpost['title'];
				$blogpost_text = $blogpost['text'];
				$blogpost_date = $blogpost['date'];
				makeBlogpostPreview($blogpost_id, $blogpost_title, $blogpost_author, $blogpost_text,$blogpost_date);
			}
		?>
	</div>
	<div id = "login">
		<a href = "login.php">Login</a>
	</div>
</body>
</html>