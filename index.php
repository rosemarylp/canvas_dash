<?php require_once 'inc/session.inc.php'; ?>
<?php require_once 'inc/functions.inc.php'; ?>
<?php require_once 'inc/check_login.php'; ?>

<!DOCTYPE html>
<html lang="en">
<!-- Author: Rosemary Perkins -->
<head>
	<meta charset="UTF-8">
	<title>Canvas Dash</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/landing_style.css">
	

</head>
	<?php 
	if (logged_in()) {
		include 'inc/logged_in_content.inc.php';
	} else {
		include 'inc/login_page.inc.php';
	}

	 ?>


