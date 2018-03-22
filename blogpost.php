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
    $settings = $database->getSettings();
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
			$blogpost_id = filter_input(INPUT_GET, 'blogpost_id', FILTER_SANITIZE_NUMBER_INT);
	        $valid = TRUE;
	        if ($blogpost_id) {
	            $blogpost = $database->getBlogpostById($blogpost_id);
	            if ($blogpost) {
	                $title = $blogpost['title'];
	            } else {
	                $title = "Error";
	                $valid = FALSE;
	            }
	        } else {
	            $title = "Error";
	            $valid = FALSE;
	        }

	        if ($valid) {
				$blogpost_author = $blogpost['author'];
				$blogpost_title = $blogpost['title'];
				$blogpost_text = $blogpost['text'];
				$blogpost_date = $blogpost['date'];
				$comments = $database->getCommentsByBlogpost($blogpost_id);
				makeBlogpost($blogpost_title, $blogpost_author, $blogpost_date, $blogpost_text, $comments, $admin_logged);
	        	if ($admin_logged) {
	        		$update_blogpost_error = '';
					if (isset($_POST['submit_update_blogpost'])) {
						$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
						$author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
						$text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);

						if (strlen($title) > 400) {
							$update_blogpost_error = 'Title too long.';
						} else if (strlen($author > 400)) {
							$update_blogpost_error = 'Author name too long.';
						}

						if (!$update_blogpost_error) {
							$database->updateBlogpost($blogpost_id, $title, $author, $text);
						}
					} else if (isset($_POST['submit_delete_blogpost'])) {
						$database->deleteBlogpost($blogpost_id);
						header("Location: blog.php");
					} else if (isset($_POST['submit_delete_comment'])) {
						$delete_comment_id = filter_input(INPUT_POST, 'delete_comment_id', FILTER_SANITIZE_STRING);
						$database->deleteComment($delete_comment_id);
					}
					print(
						"
						<div id='update_blogpost_form'>
						<form method='post'>
							<input type='text' name='title' placeholder='Title' required/>
							<input type='text' name='author' placeholder='Author' required/>
							<textarea name='text' placeholder='Text' required></textarea>
							<input type='submit' name='submit_update_blogpost' value='Update Blogpost'/>
						</form>
						</div>
						<div id='delete_blogpost_form'>
						<form method='post'>
							<input type='submit', name='submit_delete_blogpost', value='Delete this blogpost'/>
						</form>
						</div>
						
						"
					);
				}
				$add_comment_error = '';
				if (isset($_POST['submit_add_comment'])) {
					$author = filter_input(INPUT_POST, 'comment_author', FILTER_SANITIZE_STRING);
					$text = filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_STRING);

					if (strlen($author > 400)) {
						$add_comment_error = 'Author name too long.';
					} else if (strlen($text) > 1000) {
						$add_comment_error = 'Comment too long.';
					}

					if (!$add_comment_error) {
						$database->addComment($author, $text, $blogpost_id);

						if ($settings['allow_comment_mail']) {
							$to = $settings['email'];
							$email_subject = 'Someone commented on your blogpost';
							$email_body = "A comment was added to your post: $title";
							mail($to, $email_subject, $email_body);
						}
					}
				}

	        }
		?>
	</div>
	<div id = "login">
		<a href = "login.php">Login</a>
	</div>
	
</body>
</html>
