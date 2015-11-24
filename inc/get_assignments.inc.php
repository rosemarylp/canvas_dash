<?php 

require_once 'functions.inc.php';

if (isset($_GET["course"])) {
	$course = $_GET["course"];
	global $canvas_site;
	global $access_token;

	get_assignments($course, $canvas_site, $access_token);
}

 ?>