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
	<title>Lessons</title>
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
		<br>
		<div class = "signup"><div>The calendar below shows the time slots I have open for lessons. If one of these times works for you and
			you would like to sign up for a lesson, please fill out the form below.</div>
		<iframe src="https://calendar.google.com/calendar/embed?height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=cornell.edu_4rbfh8lnan06topiedts49rub0%40group.calendar.google.com&amp;color=%23B1440E&amp;ctz=America%2FNew_York"
				class = "calendar"></iframe>
		<?php 
			if (isset($_POST['submit'])) {
				if ($settings['allow_lesson_mail']) {
					$fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
					$to = $settings['email'];
					$email_subject = "New lesson signup";
					$email_body = "You have received a new lesson signup.\n".
						"First name: ". filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING)."\n".
						"Last name: ". filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING)."\n".
						"Email: ". filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING)."\n".
						"Comments: ". filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);
					mail($to, $email_subject, $email_body);
					print(
						"
						<p>Thank you for your interest. Your message has been sent.</p>
						"
					);
				} else {
					print(
						"
						<p>Sorry; I am not accepting mail at this time.</p>
						"
					);
				}
			} else {
				print(
					"
					<div id = 'signup_form'>
						Sign Up
						<form method = 'post'>
							<input type = 'text' name = 'fname' placeholder = 'First Name' required><br>
							<input type = 'text' name = 'lname' placeholder = 'Last Name' required><br>
							<input type = 'text' name = 'email' placeholder = 'E-Mail Address' required><br>
							<input type = 'text' name = 'comments' placeholder = 'Comments' required><br>
							<input type = 'submit' name = 'submit' value = 'Submit'>
						</form>
					</div>
					"
				);
			}
		?>
	</div>
	<div id = "login">
		<a href = "login.php">Login</a>
	</div>
</body>
</html>