<?php 

// require 'functions.inc.php';
require_once 'connect.inc.php';

//Process new user

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$username = $_POST["new_username"];
	$password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
	$name = $_POST["new_name"];
	$email = $_POST["new_email"];

	try {
		$sql = "INSERT INTO user (";
		$sql .= "username, hashed_password, name, email) ";
		$sql .= "VALUES (";
		$sql .= "'{$username}', '{$password}', '{$name}', '{$email}')";
		$stmt = $connection->prepare($sql);
		$stmt->execute();
		return $stmt;
	} catch(PDOException $e) {
		return $e->getMessage();
	}

	

}

 ?>