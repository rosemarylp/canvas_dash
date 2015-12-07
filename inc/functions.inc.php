<?php 
//Author: Rosemary Perkins
require_once 'connect.inc.php';
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
			$output .= "<h3><a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a></h3>";
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
		$due_date = format_date($data[$i]->assignment->due_at);
		$output .= "<h4>Due: " . $due_date . "</h4>";
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
			$output .= "<input type=\"radio\" name=\"courses\" id=\"" . $data[$i]->id . "\" value=\"" . $data[$i]->id . "\">";
			$output .= "<label for=\"" . $data[$i]->id . "\">";
			$output .= $data[$i]->name;
			$output .= " (" . $data[$i]->enrollments[0]->computed_current_score . "%)";
			$output .= "</label>";
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
			$output .= "<h3>";
			$output .= "<a href=\"" . $activity_data[$i]->html_url . "\">" . $activity_data[$i]->title . "</a>";
			$output .= "</h3>";
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
				$output .= "<h4>Score: " . $activity_data[$i]->score . "/" . $activity_data[$i]->assignment->points_possible . "</h4>";
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
	//source: http://stackoverflow.com/questions/7127764/sort-array-of-objects-by-date-field
	usort($data, function($a, $b) {
		return strtotime($a->due_at) - strtotime($b->due_at);
	});

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
				$due_date = format_date($upcoming_data[$i]->due_at);
				$output .= "<h4>Due: " . $due_date . "</h4>";
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
				$output .= "<h5>";
				$output .= "<a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a>";
				$output .= "</h5>";
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
				$output .= "<h5>";
				$output .= "<a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a>";
				$output .= "</h5>";
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
	$output .= "<section>";
	$output .= "<h4>Recent Feedback</h4>";
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission") {
			if ($data[$i]->submission_type == "online_quiz") {
				$output .= "<section>";
				$output .= "<h5>";
				$output .= "<a href=\"" . $data[$i]->html_url . "\">" . $data[$i]->title . "</a>";
				$output .= "</h5>";
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
				$output .= "<h5><a href=\"" . $upcoming_data[$i]->html_url . "\">";
				$output .= $upcoming_data[$i]->name . "</a></h5>";
				if ($upcoming_data[$i]->due_at == null) {
					$output .= "<h6>Due: " . "None" . "</h6>";
				} else {
					$due_date = format_date($upcoming_data[$i]->due_at);
					$output .= "<h6>Due: " . $due_date . "</h6>";
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
	$output .= "<h4>Upcoming Discussions</h4>";
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->submission_types[0] == "discussion_topic") {
			$output .= "<section>";
			$output .= "<h5><a href=\"" . $upcoming_data[$i]->html_url . "\">";
			$output .= $upcoming_data[$i]->name . "</a></h5>";
			if ($upcoming_data[$i]->due_at == null) {
				$output .= "<h6>Due: " . "None" . "</h6>";
			} else {
				$due_date = format_date($upcoming_data[$i]->due_at);
				$output .= "<h6>Due: " . $due_date . "</h6>";
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
	$output .= "<h4>Upcoming Quizzes</h4>";
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false && $upcoming_data[$i]->submission_types[0] == "online_quiz") {
			$output .= "<section>";
			$output .= "<h5><a href=\"" . $upcoming_data[$i]->html_url . "\">";
			$output .= $upcoming_data[$i]->name . "</a></h5>";
			if ($upcoming_data[$i]->due_at == null) {
				$output .= "<h6>Due: " . "None" . "</h6>";
			} else {
				$due_date = format_date($upcoming_data[$i]->due_at);
				$output .= "<h5>Due: " . $due_date . "</h5>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";

	return $output;
}

function format_date($date) {
	$date = date_create($date);
	$date_timezone = date_timezone_set($date, timezone_open("America/Los_angeles"));
	$formatted_date = date_format($date_timezone, "F jS, g:iA");
	return $formatted_date;
}

function get_records($connection, $sql, $parameters) {
	try {
		$stmt = $connection->prepare($sql);

		$stmt->setFetchMode (PDO::FETCH_ASSOC);
		$stmt->execute($parameters);
		//returns associative array
		return $stmt->fetchAll();
	} catch (Exception $e) {
		echo '"error":"' . $e->getCode() . '","text":"' . $e->getMessage() . '"';
		exit;
	}
} //end getRecordset

function write_records($connection, $sql, $parameters) {
	try {
		$stmt = $connection->prepare($sql);
		$stmt->execute($parameters);
		return $stmt;
	}
	catch (Exception $e) {
		echo '"error":"' . $e->getCode() . '","text:"' . $e->getMessage() . '"';
		exit;
	}
}

 ?>