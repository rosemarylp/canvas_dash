<?php 
require 'connect.inc.php';
require_once 'functions.inc.php';

if (isset($_GET["course"])) {
	$course = $_GET["course"];
	global $canvas_site;
	global $access_token;

	// get_assignments($course, $canvas_site, $access_token);
	// get_course_activity($course, $canvas_site, $access_token);
	// get_course_upcoming($course, $canvas_site, $access_token);
}

if (isset($_GET["course"]) && isset($_GET["include"])) {
	global $canvas_site;
	global $access_token;
	$course = $_GET["course"];

	switch ($_GET["include"]) {
		case 'info':
			get_course_activity($course, $canvas_site, $access_token);
			// get_course_upcoming($course, $canvas_site, $access_token);
			break;

		case 'quizzes':
			get_quizzes($course, $canvas_site, $access_token);
			break;

		case 'discussions':
			get_discussions($course, $canvas_site, $access_token);
			break;

		case 'assignments':
			get_assignments($course, $canvas_site, $access_token);
			break;

		default:
			# code...
			break;
	}
}

 ?>