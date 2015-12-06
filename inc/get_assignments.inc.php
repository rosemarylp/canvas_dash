<?php 
require_once 'connect.inc.php';
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

	$upcoming_assignments = get_upcoming_assignments($upcoming_data);
	$upcoming_discussions = get_upcoming_discussions($upcoming_data);
	$upcoming_quizzes = get_upcoming_quizzes($upcoming_data);

	$output =  output_course_activity($course_activity_data);
	$output .= output_course_upcoming($upcoming_data);

	$output .= "<div class=\"container tabbed\">";


//Buttons Tabs

	$output .= "<div class=\"wrapper-tabs-buttons\">";
	$output .= "<h3 id=\"button-assignments\" class=\"button-tabs button-selected\">Assignments</h3>";
	$output .= "<h3 id=\"button-discussions\" class=\"button-tabs button-unselected\">Discussions</h3>";
	$output .= "<h3 id=\"button-quizzes\" class=\"button-tabs button-unselected\">Quizzes</h3>";
	$output .= "</div>";

//Assignments
	// $output .= "<div class=\"container tabs-selected\">";

	$output .= "<div class=\"container tabs-selected\" id=\"assignments\">";

	$output .= "<div>";
	$output .= $past_assignments;
	$output .= $upcoming_assignments;
	$output .= "</div>";
	$output .= "</div>";
	// $output .= "</div>";

	// $output .= "<div >";
	$output .= "<div id=\"discussions\" class=\"container tabs-unselected\">";
	$output .= "<div>";
	$output .= $past_discussions;
	$output .= $upcoming_discussions;
	$output .= "</div>";
	$output .= "</div>";

	// $output .= "<div >";
	$output .= "<div class=\"container tabs-unselected\" id=\"quizzes\">";
	$output .= "<div>";
	$output .= $past_quizzes;
	$output .= $upcoming_quizzes;
	$output .= "</div>";
	$output .= "</div>";

	// $output .= "</div>";

	echo $output;
}

 ?>