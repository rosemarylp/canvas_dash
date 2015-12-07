<?php require_once 'inc/session.inc.php'; ?>
<?php require_once 'inc/functions.inc.php'; ?>
<?php require_once 'inc/functions_login_reg.inc.php'; ?>

<?php 

if (logged_in()) {
	include 'inc/logged_in_content.inc.php';
} else {
	include 'inc/login_page.inc.php';
}

 ?>