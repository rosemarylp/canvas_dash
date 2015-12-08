<?php 

// require 'functions.inc.php';
require_once 'connect.inc.php';
require_once 'session.inc.php';
require_once 'functions.inc.php';

function find_all_users() {
	global $connection;

	$sql = "SELECT * ";
	$sql .= "FROM user ";
	$sql .= "ORDER BY username ASC";

	$user_set = get_records($connection, $sql);
	return $user_set;
} //end find_all_users

function find_user_by_id($user_id) {
	global $connection;

	$safe_user_id = mysqli_real_escape_string($connection, $user_id);

	$sql = "SELECT * ";
	$sql .= "FROM user ";
	$sql .= "WHERE id = {$safe_user_id} ";
	$sql .= "LIMIT 1";

	$user_set = get_records($connection, $sql, $safe_user_id);

	if ($user = $user_set->fetch(PDO::FETCH_ASSOC)) {
		return $user;
	} else {
		return null;
	}

} //end find_user_by_id

function find_user_by_username($username) {
	global $connection;

	$sql = "SELECT * ";
	$sql .= "FROM user ";
	$sql .= "WHERE username = '{$username}' ";
	$sql .= "LIMIT 1";

	$parameters = [$username];

	$user_set = get_records($connection, $sql, $parameters);
	if ($user = $user_set->fetch(PDO::FETCH_ASSOC)) {
		return $user;
	} else {
		return null;
	}
} //end find_user_by_username

function attempt_login($username, $password) {
	//find user, then password
	$user = find_user_by_username($username);
	if ($user) {
		//found admin, check password
		if (password_verify($password, $user["hashed_password"])) {
			//password matches
			return $user;
		} else {
			//password doesn't match
			echo "bad password<br>";
			return false;
		}
	} else {
		//not found
		return false;
	}
} //end attempt_login

function logged_in() {
	return isset($_SESSION['logged_in']);
	//check
} //logged_in

//Check Login

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$found_user = attempt_login($username, $password);

	if ($found_user) {
		//The login was successful - set session variables
		$_SESSION["username"] = $found_user["username"];
		//To use in later version
		// $_SESSION["name"] = $found_user["name"];
		//To use in later version
		// $_SESSION["preferred_name"] = $found_user["preferred_name"];
		//To use in later version
		// $_SESSION["theme"] = $found_user["theme"];
		$_SESSION["logged_in"] = TRUE;

		$content = include 'layout/logged_in_content.inc.php';

		echo $content;
	}

}

 ?>