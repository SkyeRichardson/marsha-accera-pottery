<?php
	#Navigation bar for top of every page
		function makeNavBar(){
			$nav_pages = array( 
				'Home' => 'index.php',
				'About' => 'about.php',
				'Gallery' => 'artwork.php',
				'Lessons' => 'lessons.php',
				'Blog'=>'blog.php',
				'Shop' => 'https://www.etsy.com/shop/EarthArtsStudio',
				
			);
			$page = $_SERVER['REQUEST_URI'];
			foreach($nav_pages as $title => $link){
				
				echo ("<div><a href = '$link'");
				if(strpos($page,$link)!==false){
					echo("class = 'active'");
				}
				echo(">$title</a></div>");
			}
			
		}

		function makeBlogpostPreview($id, $title, $author, $text, $date) {
			$text = nl2br($text);
			print(
				"
				<div class='blogpost preview'>
					<a href='blogpost.php?blogpost_id=$id'><div class='blogpost_preview_title'>$title</div>
					<div class='blogpost_text'>$text</div>
					<span class='blogpost_preview_author'>$author</span>
					<span class='blogpost_preview_date'>$date</span></a>
				</div>
				"
			);
		}

		function makeBlogpost($title, $author, $date, $text, $comments, $admin_logged) {
			$text = nl2br($text);
			$blogpost_string =
				"
				<div class='blogpost'>
					<h2 class='blogpost_preview_title'>$title</h2>
					<span class='blogpost_author'>$author</span>
					<span class='blogpost_date'>$date</span>
					<div class='blogpost_text'>$text</div>
				</div>
				";

			$add_comment_string =
				"
				<div class='add_comment_form'>
				<form method='post'>
					<input type='text' name='comment_author' placeholder='Your Name'/>
					<textarea name='comment_text' placeholder='Your Comment'></textarea>
					<input type='submit' name='submit_add_comment' value='Post Comment'/>
				</form>
				</div>
				";

			$comment_string =
				"
				<div class='comments'>
				";

			foreach ($comments as $comment) {
				$comment_id = $comment['commentID'];
				$comment_author = $comment['author'];
				$comment_text = $comment['text'];
				$comment_date = $comment['date'];
				$comment_html =
					"
					<div class='comment' data-commentID='$comment_id'>
						<span class='comment_author'>$comment_author</span>
						<span class='comment_date'>$comment_date</span>
						<p class='comment_text'>$comment_text</p>
					";
				if ($admin_logged) {
					$comment_html .=
						"
						<form class='comment_delete_form' method='post'>
							<input type='hidden' name='delete_comment_id' value='$comment_id'/>
							<input type='submit' name='submit_delete_comment' value='Delete this comment'/>
						</form>
						";
				}
				$comment_html .= '</div>';

				$comment_string .= $comment_html;
			}

			$comment_string .=
				"
				</div>
				";

			$output_string = $blogpost_string . $comment_string . $add_comment_string;

			print($output_string);
		}

		/**
		 * Takes as an argument the file upload form name. Returns the moved path of the
		 * file, and FALSE if there was some error with uploading the file.
		 */
		function getImagePath($FILE_name) {
			$image = $_FILES[$FILE_name];

			if (empty($image['tmp_name']) || !getimagesize($image['tmp_name'])) {
				return FALSE;
			} else {
				$original_name = $image['name'];
				$temp_name = $image['tmp_name'];
				$new_path = "images/$original_name";

				while (file_exists($new_path)) {
                    $new_name = uniqid()."-$original_name";
                    $new_path = "images/$new_name";
                }

                move_uploaded_file($temp_name, $new_path);

                return $new_path;
			}
		}
?>
