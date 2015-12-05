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
		if ($data[$i]->type == "Submission" || $data[$i]->type == "DiscussionTopic" && property_exists($data[$i], "workflow_state")) {
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
	return $data;
}

function output_course_activity($activity_data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Recent Updates</h2>";
	for ($i=0; $i<count($activity_data); $i++) {
		if ($activity_data[$i]->type == "Submission" || $activity_data[$i]->type == "DiscussionTopic" && property_exists($activity_data[$i], "workflow_state")) {
			$output .= "<section>";
			//Clickable title of assignment
			$output .= "<h3><a href=\"" . $activity_data[$i]->html_url . "\">" . $activity_data[$i]->title . "</a><h3>";
			//Output comment
			if (property_exists($activity_data[$i], "submission_comments")) {
				if (count($activity_data[$i]->submission_comments) > 0) {
					for ($j=0; $j<count($activity_data[$i]->submission_comments); $j++) {
						$output .= "<p>" . $activity_data[$i]->submission_comments[$j]->comment . "</p>";
					}
				}
			}
			//Output score and total points possible
			if (property_exists($activity_data[$i], "score")) {
				$output .= "<p>Score: " . $activity_data[$i]->score . "/" . $activity_data[$i]->assignment->points_possible . "</p>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";

	return $output;
}

function get_course_upcoming($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/assignments?bucket=future&access_token=" . $access_token;
	$data = call_api("GET", $url);
	return $data;
}

function output_course_upcoming($upcoming_data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Upcoming Assignments</h2>";
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false) {
			$output .= "<section>";
			$output .= "<h3><a href=\"" . $upcoming_data[$i]->html_url . "\">";
			$output .= $upcoming_data[$i]->name . "</a></h3>";
			if ($upcoming_data[$i]->due_at == null) {
				$output .= "<h4>Due: " . "None" . "</h4>";
			} else {
				$output .= "<h4>Due: " . $upcoming_data[$i]->due_at . "</h4>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";

	return $output;
}

function get_past_assignments($data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Recent Feedback</h4>";
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission") {
			if ($data[$i]->submission_type == "online_upload" || $data[$i]->submission_type == "online_url") {
				$output .= "<section>";
				$output .= "<h4>";
				$output .= "<a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a>";
				$output .= "</h4>";
				if (property_exists($data[$i], "submission_comments")) {
					if (count($data[$i]->submission_comments) > 0) {
						for ($j=0; $j<count($data[$i]->submission_comments); $j++) {
							$output .= "<p>" . $data[$i]->submission_comments[$j]->comment . "</p>";
						}
					}
				}

				if (property_exists($data[$i], "score")) {
					$output .= "<p>Score: " . $data[$i]->score . "/" . $data[$i]->assignment->points_possible . "</p>";
				}
				$output .= "</section>";
			}
		}
	}
	$output .= "</section>";
	return $output;
}

function get_past_discussions($data) {
	$output = "";
	$output .= "<h4>Recent Feedback</h4>";
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "DiscussionTopic" && property_exists($data[$i], "workflow_state")) {
				$output .= "<section>";
				$output .= "<h4>";
				$output .= "<a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a>";
				$output .= "</h4>";
				if (property_exists($data[$i], "submission_comments")) {
					if (count($data[$i]->submission_comments) > 0) {
						for ($j=0; $j<count($data[$i]->submission_comments); $j++) {
							$output .= "<p>" . $data[$i]->submission_comments[$j]->comment . "</p>";
						}
					}
				}

				if (property_exists($data[$i], "score")) {
					$output .= "<p>Score: " . $data[$i]->score . "/" . $data[$i]->assignment->points_possible . "</p>";
				}
				$output .= "</section>";
			}
		}
	return $output;
}

function get_past_quizzes($data) {
	$output = "";
	$output .= "<h4>Recent Feedback</h4>";
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission") {
			if ($data[$i]->submission_type == "online_quiz") {
				$output .= "<section>";
				$output .= "<h4>";
				$output .= "<a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a>";
				$output .= "</h4>";
				if (property_exists($data[$i], "submission_comments")) {
					if (count($data[$i]->submission_comments) > 0) {
						for ($j=0; $j<count($data[$i]->submission_comments); $j++) {
							$output .= "<p>" . $data[$i]->submission_comments[$j]->comment . "</p>";
						}
					}
				}

				if (property_exists($data[$i], "score")) {
					$output .= "<p>Score: " . $data[$i]->score . "/" . $data[$i]->assignment->points_possible . "</p>";
				}
				$output .= "</section>";
			}
		}
	}
	return $output;
}

function get_upcoming_assignments($upcoming_data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Upcoming Assignments</h4>";
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false) {

			$is_assignment = true;
			for ($j=0; $j<count($upcoming_data[$i]->submission_types); $j++) {
				if ($upcoming_data[$i]->submission_types[$j] == "discussion_topic" || $upcoming_data[$i]->submission_types[$j] == "online_quiz") {
					$is_assignment == false;
				}
			}

			if ($is_assignment == true) {
				$output .= "<section>";
				$output .= "<h4><a href=\"" . $upcoming_data[$i]->html_url . "\">";
				$output .= $upcoming_data[$i]->name . "</a></h4>";
				if ($upcoming_data[$i]->due_at == null) {
					$output .= "<h5>Due: " . "None" . "</h5>";
				} else {
					$output .= "<h5>Due: " . $upcoming_data[$i]->due_at . "</h5>";
				}
				$output .= "</section>";
			}
		}
	}
	$output .= "</section>";

	return $output;
}

function get_upcoming_discussions($upcoming_data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h3>Upcoming Discussions</h3>";
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->submission_types[0] == "discussion_topic") {
			$output .= "<section>";
			$output .= "<h4><a href=\"" . $upcoming_data[$i]->html_url . "\">";
			$output .= $upcoming_data[$i]->name . "</a></h4>";
			if ($upcoming_data[$i]->due_at == null) {
				$output .= "<h5>Due: " . "None" . "</h5>";
			} else {
				$output .= "<h5>Due: " . $upcoming_data[$i]->due_at . "</h5>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";

	return $output;
}

function get_upcoming_quizzes($upcoming_data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h3>Upcoming Quizzes</h3>";
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false && $upcoming_data[$i]->submission_types[0] == "online_quiz") {
			$output .= "<section>";
			$output .= "<h4><a href=\"" . $upcoming_data[$i]->html_url . "\">";
			$output .= $upcoming_data[$i]->name . "</a></h4>";
			if ($upcoming_data[$i]->due_at == null) {
				$output .= "<h5>Due: " . "None" . "</h5>";
			} else {
				$output .= "<h5>Due: " . $upcoming_data[$i]->due_at . "</h5>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";

	return $output;
}

function format_date($date) {
	$date_string = strtotime($date);
	$formatted_date = date("F jS, Y", $published_date);
	return $formatted_date;
}

 ?>