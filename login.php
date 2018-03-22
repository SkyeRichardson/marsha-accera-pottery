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
	<title>Log in</title>
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
    	# Setting up form data
    	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		$logged_user = '';
		if (isset($_SESSION['logged_user'])) {
			$logged_user = $_SESSION['logged_user'];
		}

		# Display will be modified depending on whether user is logged in/out, or failed to log in.
		$display = '';

		if ($logged_user) {
			if (isset($_POST['logout'])) {
				unset($_SESSION['logged_user']);
				$display = 'login_form';
			} else {
				$display = 'logged_in';
				if (isset($_POST['submit_profile_image'])) {
					$image_path = getImagePath('profile_image');
					if ($image_path) {
						$database->updateProfileImage($image_path);
					}
				} else if (isset($_POST['submit_update_biography'])) {
					$new_biography = filter_input(INPUT_POST, 'biography', FILTER_SANITIZE_STRING);
					$database->updateBiography($new_biography);
				} else if (isset($_POST['submit_update_email'])) {
					$new_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
					$new_allow_comment_mail = isset($_POST['allow_comment_mail']);
					$new_allow_lesson_mail = isset($_POST['allow_lesson_mail']);
					$database->updateEmail($new_email);
					$database->updateAllowCommentMail($new_allow_comment_mail);
					$database->updateAllowLessonMail($new_allow_lesson_mail);
				} else if (isset($_POST['submit_change_password'])) {
					$old_password = filter_input(INPUT_POST, 'old_password', FILTER_SANITIZE_STRING);
					$new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
					$confirm_new_password = filter_input(INPUT_POST, 'confirm_new_password', FILTER_SANITIZE_STRING);

					$user = $database->getUserByUsername($_SESSION['logged_user']);
					if ($user && password_verify($old_password, $user['hashpassword'])) {
						if ($new_password === $confirm_new_password) {
							$database->updateUser($user['username'], password_hash($new_password, PASSWORD_DEFAULT), $user['name']);
						}
					}
				}
			}
		} else {
			if (empty($username) || empty($password)) {
				$display = 'login_form';
			} else {
				$user = $database->getUserByUsername($username);
				if ($user && password_verify($password, $user['hashpassword'])) {
					$_SESSION['logged_user'] = $username;
					$logged_user = $username;
					$display = 'logged_in';
				} else {
					$display = 'failed_login';
				}
			}
		}

		if ($display === 'login_form') {
			print(
				"
				<div class='content'>
					<div id='login_form'>
            		<form method='post'>
        				<input type='text' name='username' placeholder='Username'/>
        				<input type='password' name='password' placeholder='Password'/>
            			<input type='submit' name='submit_login' value='Log in'>
            		</form>
            		</div>
	            </div>
				"
			);
		} elseif ($display === 'logged_in') {
			$settings = $database->getSettings();
			$current_email = $settings['email'];
			$current_biography = $settings['biography'];
			$comment_checked = $settings['allow_comment_mail'] ? 'checked' : '';
			$lesson_checked = $settings['allow_lesson_mail'] ? 'checked' : '';

			print("<div class='content'>");
			print(
				"
				<div id='login_form'>
				<p>You are logged in as: $logged_user</p>
				<form method='post'>
					<input type='submit' name='logout' value='Log out'>
				</form>
				</div>
				"
			);
			print(
				"
				<div id='update_profile_image_form' class = 'form'>
				<form method='post' enctype='multipart/form-data'>
					<input type='file' name='profile_image'>
					<input type='submit' name='submit_profile_image' value='Upload new profile photo'>
				</form>
				</div>

				<div id='update_biography_form' class = 'form'>
				<form method='post'>
					<textarea rows='4' cols='40' name='biography' placeholder='Biography'>$current_biography</textarea>
					<input type='submit' name='submit_update_biography' value='Update biography'>
				</form>
				</div>

				<div id='update_email_form'>
				<form method='post' class = 'form'>
					<input type='text' name='email' placeholder='Email' value='$current_email'></textarea>
					Send mail when someone comments on a blogpost?
					<input type='checkbox' name='allow_comment_mail' $comment_checked><br>
					Send mail when someone sends a message through the lessons page?
					<input type='checkbox' name='allow_lesson_mail' $lesson_checked>
					<input type='submit' name='submit_update_email' value='Update Email Settings'>
				</form>
				</div>

				<div id='change_password_form' class = 'form'>
				<form method='post'>
					<input type='password' name='old_password' placeholder='Current password'>
					<input type='password' name='new_password' placeholder='New password'>
					<input type='password' name='confirm_new_password' placeholder='Confirm new password'>
					<input type='submit' name='submit_change_password' value='Change password'>
				</form>
				</div>
				"
			);
			print("</div>");
		} elseif ($display === 'failed_login') {
			print(
				"
				<div class='content'>
					<div id='login_form'>
					<p>
						Incorrect username or password. <a href='login.php'>Refresh</a> to try again.
					</p>
					</div>
				</div>
				"
			);
		}
    ?>
	</div>
</body>
</html>