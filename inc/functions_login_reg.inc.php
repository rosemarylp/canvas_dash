<?php 
//Author: Rosemary Perkins
require_once 'connect.inc.php';
require_once 'functions.inc.php';
require_once 'session.inc.php';

function find_all_users() {
	global $con;

	$sql = "SELECT * ";
	$sql .= "FROM user ";
	$sql .= "ORDER BY userName ASC";

	$user_set = getRecordset($con, $sql);
	return $user_set;
} //end find_all_users

function find_user_by_id($user_id) {
	global $con;

	$safe_user_id = mysqli_real_escape_string($connection, $user_id);

	$sql = "SELECT * ";
	$sql .= "FROM user ";
	$sql .= "WHERE id = {$safe_user_id} ";
	$sql .= "LIMIT 1";

	$user_set = getRecordset($connection, $sql, $safe_user_id);

	if ($user = $user_set->fetch(PDO::FETCH_ASSOC)) {
		return $user;
	} else {
		return null;
	}

} //end find_user_by_id

function find_user_by_username($username) {
	global $con;

	$sql = "SELECT * ";
	$sql .= "FROM user ";
	$sql .= "WHERE username = '{$username}' ";
	$sql .= "LIMIT 1";

	$parameters = [$usernme];

	$user_set = getRecordset($connection, $sql, $parameters);
	if ($user = $user_set->fetch(PDO::FETCH_ASSOC)) {
		return $user;
	} else {
		return null;
	}
} //end find_user_by_userName

function attempt_login($username, $password) {
	//find user, then password
	$user = find_user_by_usernme($username);
	if ($user) {
		//found admin, check password
		if (password_verify($password, $user["password"])) {
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
	return isset($_SESSION['user_id']);
	//check
} //logged_in

 ?>