<?php

class Database {
	/**
	 * Database provides an interface to a MySQL database. It provides functions
	 * for interacting with the database so that users do not need to write raw
	 * SQL themselves.
	 *
	 * IMPORTANT NOTE: Most functions do NOT protect against SQL injection. It
	 * is the responsibility of of the user to validate inputs.
	 */

	private $db; // The mysqli object representing the database

	/**
	 * Constructor. Takes the same inputs as the mysqli constructor.
	 *
	 * ARGUMENTS
	 * $db_host: string
	 * $db_user: string
	 * $db_password: string
	 * $db_name: string
	 */
	public function __construct($db_host, $db_user, $db_password, $db_name) {
		$this->db = new mysqli($db_host, $db_user, $db_password, $db_name);
	}

	/**
	 * Queries the database with $sql_query and returns the result as a 
	 * mysqli result object.
	 *
	 * Should rarely be used.
	 */
	private function query($sql_query) {
		return $this->db->query($sql_query);
	}

	private function prepare($query_template) {
		$statement = $this->db->stmt_init();
		if ($statement->prepare($query_template)) {
			return $statement;
		}
	}

	/**
	 * Returns all rows in the Categories table.
	 */
	public function getCategories() {
		return $this->query("SELECT * FROM categories");
	}

	/**
	 * Returns the category with id $category_id.
	 *
	 * ARGUMENTS
	 * $category_id: integer. Example - '8'
	 */
	public function getCategoryById($category_id) {
		$query = "SELECT * FROM categories WHERE categoryID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $category_id);
		$statement->execute();
		$result = $statement->get_result();

		if ($category = $result->fetch_assoc()) {
			return $category;
		} else {
			return NULL;
		}
	}

	/**
	 * Returns all rows in the art table.
	 */
	public function getArt() {
		return $this->query("SELECT * FROM artworks");
	}

	/**
	 * Returns the Artwork with id $art_id.
	 *
	 * ARGUMENTS
	 * $art_id: string. Example - '8'
	 */
	public function getArtById($art_id) {
		$query = "SELECT * FROM artworks WHERE artworkID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $art_id);
		$statement->execute();
		$result = $statement->get_result();

		if ($art = $result->fetch_assoc()) {
			return $art;
		} else {
			return NULL;
		}
	}

	/**
	 * Returns all Artwork belonging to category $category_id.
	 *
	 * ARUMENTS
	 * $category_id: string. Example - '8'
	 */
	public function getArtByCategory($category_id) {
		$query = "SELECT artworks.* FROM artworks
				    INNER JOIN artworks_categories ON artworks.artworkID = artworks_categories.artworkID
				  WHERE artworks_categories.categoryID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $category_id);
		$statement->execute();
		return $statement->get_result();
	}

	/**
	 * Returns all Category rows that contain Artwork with id $art_id.
	 *
	 * ARUGMENTS
	 * $art_id: string. Example - '8'
	 */
	public function getCategoriesByArt($art_id) {
		$query =
			"
			SELECT categories.* FROM categories
				INNER JOIN artworks_categories ON categories.categoryID = artworks_categories.categoryID
			WHERE artworks_categories.categoryID = ?
			";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $art_id);
		$statement->execute();
		return $statement->get_result();
	}

	/**
	 * Returns the user with username $username.
	 *
	 * ARGUMENTS
	 * $username: string. Example - 'admin'
	 */
	public function getUserByUsername($username) {
		$query = "SELECT * FROM users WHERE username = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('s', $username);
		$statement->execute();
		$result = $statement->get_result();
        if ($result && $result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return NULL;
        }
	}

	public function getBlogposts() {
		return $this->query("SELECT * FROM blogposts ORDER BY date DESC");
	}

	public function getBlogpostById($blogpost_id) {
		$query = "SELECT * FROM blogposts WHERE blogpostID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $blogpost_id);
		$statement->execute();
		$result = $statement->get_result();
		if ($result && $result->num_rows === 1) {
			return $result->fetch_assoc();
		} else {
			return NULL;
		}
	}

	public function getCommentsByBlogpost($blogpost_id) {
		$query = "SELECT * FROM comments WHERE blogpostID = ? ORDER BY date DESC";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $blogpost_id);
		$statement->execute();
		return $statement->get_result();
	}

	/**
	 * Returns the page with name $page_name.
	 *
	 * ARGUMENTS
	 * $page_name: string. Example - 'about.php'
	 */
	public function getPageByName($page_name) {
		$query = "SELECT * FROM pages WHERE name = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('s', $page_name);
		$statement->execute();
		$result = $statement->get_result();
		if ($result && $result->num_rows === 1) {
			return $result->fetch_assoc();
		} else {
			return NULL;
		}
	}

	/**
	 * Adds a category with the same fields as the arguments. Returns TRUE if 
	 * successful, FALSE if not. Primary reason for failure is if there already
	 * exists a category with the same title.
	 *
	 * ARGUMENTS
	 * $title: string. Example - 'Pottery'
	 * $description: string. Example - 'My pottery collection.'
	 * $photo_path: string. Example - 'images/vase.jpg'
	 */
	public function addCategory($title, $description, $photo_path) {
		$query = 
			"
			INSERT INTO categories (name, description, photo_path)
			VALUES (?, ?, ?)
			";
		$statement = $this->prepare($query);
		$statement->bind_param('sss', $title, $description, $photo_path);
		$statement->execute();
		return $statement->get_result();
	}

	/**
	 * Adds an artwork with the same fields as the arguments. TRUE for success,
	 * FALSE for failure.
	 *
	 * ARGUMENTS
	 * $name: string. Example - 'My Vase'
	 * $price: decimal with up to 2 decimal places representing a dollar value. Example - 123.45
	 * $description: string. Example - 'This is a beautiful vase.'
	 * $file_path: string. Example - 'images/vase.jpg'
	 * $category_titles: array of strings. Example - array('Vases', 'Pottery')
	 */
	public function addArt($name, $price, $description, $file_path, $category_titles) {
		$query =
			"
			INSERT INTO artworks (name, price, description, file_path)
			VALUES (?, ?, ?, ?)
			";
		$statement1 = $this->prepare($query);
		$statement1->bind_param('sdss', $name, $price, $description, $file_path);
		$statement1->execute();

		$artwork_id = $this->query("SELECT LAST_INSERT_ID()")->fetch_assoc()['LAST_INSERT_ID()'];
		$statement2 = $this->prepare("SELECT * FROM categories WHERE title = ?");
		foreach ($category_titles as $category_title) {
			$statement2->bind_param('s', $category_title);
			$statement2->execute();
			$result = $statement2->get_result();
			$category = $result->fetch_assoc();
			if ($category) {
				$category_id = $category['categoryID'];
				$this->query(
					"
					INSERT INTO artworks_categories (artworkID, categoryID)
					VALUES ('$category_id', '$artwork_id')
					"
				);
			}
		}
	}

	/**
	 * Adds blogpost with arguments as fields. Returns TRUE for success; FALSE
	 * for failure.
	 *
	 * ARUMENTS
	 * $author: string. Example - 'John Smith'
	 * $title: string.
	 * $text: string.
	 */
	public function addBlogpost($title, $author, $text) {
		$query =
			"
			INSERT INTO blogposts (author, title, text)
			VALUES (?, ?, ?)
			";
		$statement = $this->prepare($query);
		$statement->bind_param('sss', $author, $title, $text);
		$statement->execute();
		print("print 1");
		return $statement->get_result();
	}

	/**
	 * Adds a comment with arguments as fields. Returns TRUE for success, FALSE
	 * for failure.
	 *
	 * ARUMGENTS
	 * $author: string.
	 * $text: string.
	 * $blogpostID: string. Example - '11'. Should be the id of the blogpost that
	 * 				this comment is associated with.
	 */
	public function addComment($author, $text, $blogpostID) {
		$query =
			"
			INSERT INTO comments (author, text, blogpostID)
			VALUES (?, ?, ?)
			";
		$statement = $this->prepare($query);
		$statement->bind_param('ssi', $author, $text, $blogpostID);
		$statement->execute();

		return $statement->get_result();
	}

	/**
	 * Updates Category with id $category_id with the arguments as fields. Returns
	 * TRUE for success, FALSE for failure.
	 *
	 * ARGUMENTS
	 * $category_id: string. The ID of the category to be updated.
	 * $title: string. The new name of this category.
	 * $description: string. The new description of this category.
	 * $photo_path: string. The new path to the photo of this category.
	 */
	public function updateCategory($category_id, $title, $description, $photo_path) {
		$query =
			"
            UPDATE categories
            SET title = ?, description = ?, photo_path = ?
            WHERE categoryID = ?
            ";
        $statement = $this->prepare($query);
        $statement->bind_param('sssi', $title, $description, $photo_path, $category_id);
        $statement->execute();

        return $statement->get_result();
	}

	/**
	 * Updates Artwork with id $art_id with arguments as fields. Returns TRUE for
	 * success, FALSE for failure.
	 *
	 * ARUMENTS
	 * $art_id: string. The ID of the Artwork to be updated.
	 * Other arguments: See specification of addArt.
	 */
	public function updateArt($art_id, $name, $price, $description, $file_path, $category_names) {
		$query = 
			"
			UPDATE artworks
			SET name = ?, price = ?, description = ?, file_path = ?
			WHERE artworkID = ?
			";
		$statement = $this->prepare($query);
		$statement->bind_param('sdssi', $name, $price, $description, $file_path, $art_id);
		$statement->execute();

		if ($category_names) {
			$query = "DELETE FROM artworks_categories WHERE artworkID = ?";
			$statement = $this->prepare($query);
			$statement->bind_param('i', $art_id);
			$statement->execute();

			foreach ($category_names as $category_name) {
				$query = "SELECT * FROM categories WHERE name = ?";
				$statement = $this->prepare($query);
				$statement->bind_param('s', $category_name);
				$statement->execute();

				$result = $statement->get_result();
				$category = $result->fetch_assoc();
				if ($category) {
					$category_id = $category['categoryID'];
					$query = "INSERT INTO artworks_categories (artworkID, categoryID) 
					          VALUES (?, ?)";
					$statement = $this->prepare($query);
					$statement->bind_param('ii', $art_id, $category_id);
					$statement->execute();
				}
			}
		}
	}

	public function updateBlogpost($blogpost_id, $title, $author, $text) {
		$query =
			"
			UPDATE blogposts
			SET title = ?, author = ?, text = ?
			WHERE blogpostID = ?
			";
		$statement = $this->prepare($query);
		$statement->bind_param('sssi', $title, $author, $text, $blogpost_id);
		$statement->execute();

		return $statement->get_result();
	}

	/**
	 * Updates User with username $username with a new hashed password and name.
	 * Returns TRUE for success, FALSE for failure.
	 *
	 * ARGUMENTS
	 * $username: string. The username of the user to be updated
	 * $hashpassword: string. The new hashed password for this user
	 * $name: string. The new name of this user.
	 */
	public function updateUser($username, $hashpassword, $name) {
		$query = "UPDATE users SET hashpassword = ?, name = ? WHERE username = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('sss', $hashpassword, $name, $username);
		$statement->execute();

		return $statement->get_result();
	}

	/**
	 * Updates Page with name $name with arguments as fields. Returne TRUE for
	 * success, FALSE for failure.
	 *
	 * ARGUMENTS
	 * $name: string. The name of the page to update
	 * $test: string. The new text on this page
	 * $photo_path: string. The path to the image on this page.
	 */
	public function updatePage($name, $text, $photo_path) {
		$query = "UPDATE pages SET text = ?, photo_path = ?, WHERE name = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('sss', $text, $photo_path, $name);
		$statement->execute();

		return $statement->get_result();
	}

	/**
	 * Delete category with id $category_id. Also removes any linking rows of
	 * this category in the Artworks_Categories table. Returne TRUE for sucess;
	 * FALSE for failure.
	 *
	 * ARGUMENTS
	 * $category_id: string. The ID of the category to delete.
	 */
	public function deleteCategory($category_id) {
		$category = $this->getCategoryById($category_id);
		$photo_path = realpath($category['photo_path']);
		
		$query = "DELETE FROM categories WHERE categoryID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $category_id);
		$statement->execute();

		$query = "DELETE FROM artworks_categories WHERE categoryID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $category_id);
		$statement->execute();

		if (is_writable($photo_path)) {
			unlink($photo_path);
		}
	}

	/**
	 * Deletes art with id $art_id, and removes correspondnig associations in the
	 * Artworks_Categories table. Returns TRUE for success; FALSE for failure.
	 *
	 * ARGUMENTS
	 * $art_id: string.
	 */
	public function deleteArt($art_id) {
		$artwork = $this->getArtById($art_id);
		$photo_path = realpath($category['file_path']);
		
		$query = "DELETE FROM artworks WHERE artworkID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $art_id);
		$statement->execute();

		$query = "DELETE FROM artworks_categories WHERE artworkID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $art_id);
		$statement->execute();

		if (is_writable($photo_path)) {
			unlink($photo_path);
		}
	}

	/**
	 * Deletes blogpost with id $blogpost_id, and also deletes coments of this
	 * post. Returns TRUE for success; FALSE for failure.
	 *
	 * ARGUMENTS
	 * $blogpost_id: string.
	 */
	public function deleteBlogpost($blogpost_id) {
		$query = "DELETE FROM blogposts WHERE blogpostID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $blogpost_id);
		$statement->execute();

		$query = "DELETE FROM comments WHERE blogpostID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $blogpost_id);
		$statement->execute();
	}

	/**
	 * Deletes comment with id $comment_id. Returns TRUE for success; FALSE for
	 * failure.
	 *
	 * ARGUMENTS
	 * $comment_id: string.
	 */
	public function deleteComment($comment_id) {
		$query = "DELETE FROM comments WHERE commentID = ?";
		$statement = $this->prepare($query);
		$statement->bind_param('i', $comment_id);
		$statement->execute();
	}

	/**
	 * 
	 */
	public function updateProfileImage($profile_image_path) {
		$current_path = $this->getSettings()['profile_image_path'];
		$query = "UPDATE settings SET profile_image_path = ? WHERE settingsID = 1";
		$statement = $this->prepare($query);
		$statement->bind_param('s', $profile_image_path);
		$statement->execute();

		if (is_writable($current_path)) {
			unlink($current_path);
		}
	}

	public function updateBiography($biography) {
		$query = "UPDATE settings SET biography = ? WHERE settingsID = 1";
		$statement = $this->prepare($query);
		$statement->bind_param('s', $biography);
		$statement->execute();
	}

	public function updateEmail($email) {
		$query = "UPDATE settings SET email = ? WHERE settingsID = 1";
		$statement = $this->prepare($query);
		$statement->bind_param('s', $email);
		$statement->execute();
	}

	public function updateAllowCommentMail($boolean) {
		if ($boolean) {
			$this->query("UPDATE settings SET allow_comment_mail = TRUE WHERE settingsID = 1");
		} else {
			$this->query("UPDATE settings SET allow_comment_mail = FALSE WHERE settingsID = 1");
		}
	}

	public function updateAllowLessonMail($boolean) {
		if ($boolean) {
			$this->query("UPDATE settings SET allow_lesson_mail = TRUE WHERE settingsID = 1");
		} else {
			$this->query("UPDATE settings SET allow_lesson_mail = FALSE WHERE settingsID = 1");
		}
	}

	public function getSettings() {
		$result = $this->query("SELECT * FROM settings WHERE settingsID = 1");
        return $result->fetch_assoc();
	}
}
