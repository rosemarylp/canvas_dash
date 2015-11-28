<?php 
require 'connect.inc.php';
require_once 'functions.inc.php';

if (isset($_GET["course"])) {
	global $canvas_site;
	global $access_token;
	$course = $_GET["course"];

	$output = get_course_activity($course, $canvas_site, $access_token);

	echo $output;
}

 ?>