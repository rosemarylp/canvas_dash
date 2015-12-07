<?php 

// require 'functions.inc.php';
require_once 'connect.inc.php';
require_once 'session.inc.php';
require_once 'functions.inc.php';

unset($_SESSION["logged_in"]);
unset($_SESSION["name"]);
unset($_SESSION["preferred_name"]);
unset($_SESSION["theme"]);
unset($_SESSION["username"]);

$content = include 'login_page.inc.php';

echo $content;

 ?>