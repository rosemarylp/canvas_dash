<?php 
require 'connect.inc.php';
require_once 'functions.inc.php';

if (isset($_GET["course"])) {
	global $canvas_site;
	global $access_token;
	$course = $_GET["course"];

	$course_activity_data = get_course_activity($course, $canvas_site, $access_token);
	$upcoming_data = get_course_upcoming($course, $canvas_site, $access_token);

	$past_assignments = get_past_assignments($course_activity_data);
	$past_discussions = get_past_discussions($course_activity_data);
	$past_quizzes = get_past_quizzes($course_activity_data);

	$output =  output_course_activity($course_activity_data);
	$output .= output_course_upcoming($upcoming_data);

	$output .= $past_assignments;
	$output .= $past_discussions;
	$output .= $past_quizzes;

	echo $output;
}

 ?>