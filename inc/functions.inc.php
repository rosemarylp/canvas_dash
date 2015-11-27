<?php 
//Author: Rosemary Perkins
require 'connect.inc.php';
$canvas_site = "https://clarkcollege.instructure.com/api/v1";

function call_api($method, $url, $data = false){
	//Author: Bruce Elgort
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // new line below for SSL
    // this is not the best solution to this
    // but it works for now
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result);
} // end call_api

function get_self() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self?access_token=" . $access_token;
	$data = call_api("GET",$url);
	$output = "<h2>Welcome, " . $data->name . "</h2>";
	return $output;
}

function get_all_activity() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self/activity_stream?per_page=15&access_token=" . $access_token;
	$data = call_api("GET",$url);
	$output = "";

	$output .= "<section>";
	$output .= "<h2>Recent Updates</h2>";
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission" || $data[$i]->type == "DiscussionTopic") {
			$output .= "<section>";
			//Clickable title of assignment
			$output .= "<h3><a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a><h3>";
			//Output comment
			if (property_exists($data[$i], "submission_comments")) {
				if (count($data[$i]->submission_comments) > 0) {
					for ($j=0; $j<count($data[$i]->submission_comments); $j++) {
						$output .= "<p>" . $data[$i]->submission_comments[$j]->comment . "</p>";
					}
				}
			}
			//Output score and total points possible
			if (property_exists($data[$i], "score")) {
				$output .= "<p>Score: " . $data[$i]->score . "/" . $data[$i]->assignment->points_possible . "</p>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";
	return $output;
}

function get_all_upcoming() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self/upcoming_events?access_token=" . $access_token;
	$data = call_api("GET", $url);
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Upcoming Assignments</h2>";
	for ($i=0; $i<count($data); $i++) {
		$output .= "<section>";
		$output .= "<h3><a href=\"" . $data[$i]->html_url . "\">";
		$output .= $data[$i]->title;
		$output .= "</a></h3>";

		$output .= "<p>" . $data[$i]->assignment->description . "</p>";

		$output .= "<h4>Due: " . $data[$i]->assignment->due_at . "</h4>";
		$output .= "</section>";
	}
	$output .= "</section>";

	return $output;
}

function get_courses() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/courses?include=total_scores&access_token=" . $access_token;
	$data = call_api("GET",$url);
	$output = "";
	for ($i=0; $i<count($data); $i++) {
		if (!property_exists($data[$i], "access_restricted_by_date")) {
			$output .= "<input type=\"radio\" name=\"courses\" value=\"" . $data[$i]->id . "\">";
			$output .= $data[$i]->name;
			$output .= " (" . $data[$i]->enrollments[0]->computed_current_score . "%)";
		}
	}
	return $output;
}

function get_course_activity($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/activity_stream?per_page=15&access_token=" . $access_token;
	$data = call_api("GET", $url);
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Recent Updates</h2>";
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission" || $data[$i]->type == "DiscussionTopic") {
			$output .= "<section>";
			//Clickable title of assignment
			$output .= "<h3><a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a><h3>";
			//Output comment
			if (property_exists($data[$i], "submission_comments")) {
				if (count($data[$i]->submission_comments) > 0) {
					for ($j=0; $j<count($data[$i]->submission_comments); $j++) {
						$output .= "<p>" . $data[$i]->submission_comments[$j]->comment . "</p>";
					}
				}
			}
			//Output score and total points possible
			if (property_exists($data[$i], "score")) {
				$output .= "<p>Score: " . $data[$i]->score . "/" . $data[$i]->assignment->points_possible . "</p>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";
	echo $output;
}

function get_course_upcoming($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/todo?access_token=" . $access_token;
	$data = call_api("GET", $url);
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Upcoming Assignments</h2>";
	for ($i=0; $i<count($data); $i++) {
		$output .= "<section>";
		$output .= "<h3><a href=\"" . $data[$i]->assignment->html_url . "\">";
		$output .= $data[$i]->assignment->name . "</a></h3>";
		$output .= "<h4>Due: " . $data[$i]->assignment->due_at . "</h4>";
		$output .= "</section>";
	}
	$output .= "</section>";

	echo $output;
}

function get_assignments($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/assignments?access_token=" . $access_token;
	$data = call_api("GET",$url);
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

function get_discussions($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/discussion_topics?access_token=" . $access_token;
	$data = call_api("GET", $url);
	echo "<pre>" . print_r($data) . "</pre>";
}

function get_quizzes($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/quizzes?access_token=" . $access_token;
	$data = call_api("GET", $url);
	echo "<pre>" . print_r($data) . "</pre>";
}

 ?>