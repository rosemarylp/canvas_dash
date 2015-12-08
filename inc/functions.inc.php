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
	//Get data about this user
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self?access_token=" . $access_token;
	$data = call_api("GET",$url);
	$output = "<h2>Welcome, " . $data->name . "</h2>";
	return $output;
}

function get_all_activity() {
	//Get recent feedback in ALL courses
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self/activity_stream?per_page=15&access_token=" . $access_token;
	$data = call_api("GET",$url);
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Recent Updates</h2>";

	$content_exists = FALSE;
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission" || $data[$i]->type == "DiscussionTopic" && property_exists($data[$i], "workflow_state")) {
			$content_exists = TRUE;
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
	if ($content_exists) {
		return $output;
	} else {
		//If there isn't any recent feedback, give a message
		$output = "<section>";
		$output .= "<h2>Recent Updates</h2>";
		$output .= "<h3>No New Updates</h3>";
		$output .= "</section>";
		return $output;
	}
}

function get_all_upcoming() {
	//Get upcoming assignments for ALL courses
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self/upcoming_events?access_token=" . $access_token;
	$data = call_api("GET", $url);
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Upcoming Assignments</h2>";
	$content_exists = FALSE;
	for ($i=0; $i<count($data); $i++) {
		$content_exists = TRUE;
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
	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h2>Upcoming Assignments</h2>";
		$output .= "<h3>No Upcoming Assignments</h3>";
		$output .= "</section>";
		return $output;
	}
}

function get_courses() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/courses?include=total_scores&access_token=" . $access_token;
	$data = call_api("GET",$url);
	$output = "";
	$content_exists = FALSE;
	for ($i=0; $i<count($data); $i++) {
		//Only get current courses
		if (!property_exists($data[$i], "access_restricted_by_date")) {
			$content_exists = TRUE;
			$output .= "<input type=\"radio\" name=\"courses\" id=\"" . $data[$i]->id . "\" value=\"" . $data[$i]->id . "\">";
			$output .= "<label for=\"" . $data[$i]->id . "\">";
			$output .= $data[$i]->name;
			$output .= " (" . $data[$i]->enrollments[0]->computed_current_score . "%)";
			$output .= "</label>";
		}
	}

	if ($content_exists) {
		return $output;
	} else {
		$output = "<div>No Courses Available</div>";
	}
}

function get_course_activity($course, $canvas_site, $access_token) {
	//get feedback for this specific course
	$url = $canvas_site . "/" . "courses/" . $course . "/activity_stream?per_page=15&access_token=" . $access_token;
	$data = call_api("GET", $url);
	return $data;
}

function output_course_activity($activity_data) {
	//Output the course activity returned by get_course_activity
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Recent Updates</h2>";
	$content_exists = FALSE;
	for ($i=0; $i<count($activity_data); $i++) {
		if ($activity_data[$i]->type == "Submission" || $activity_data[$i]->type == "DiscussionTopic" && property_exists($activity_data[$i], "workflow_state")) {
			$content_exists = TRUE;
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
	if ($content_exists) {
		return $output;
	} else {
		$output .= "<section>";
		$output .= "<h2>Recent Updates</h2>";
		$output .= "<h3>No New Updates</h3>";
		$output .= "</section>";
		return $output;
	}
}

function get_course_upcoming($course, $canvas_site, $access_token) {
	//get upcoming assignments for this specific course
	$url = $canvas_site . "/" . "courses/" . $course . "/assignments?bucket=future&access_token=" . $access_token;
	$data = call_api("GET", $url);
	//source: http://stackoverflow.com/questions/7127764/sort-array-of-objects-by-date-field
	//Sort assignments by due date
	usort($data, function($a, $b) {
		return strtotime($a->due_at) - strtotime($b->due_at);
	});

	return $data;
}

function output_course_upcoming($upcoming_data) {
	//output data returned by get_course_upcoming
	$output = "";
	$output .= "<section>";
	$output .= "<h2>Upcoming Assignments</h2>";
	$content_exists = FALSE;
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false) {
			$content_exists = TRUE;
			$output .= "<section>";
			$output .= "<h3><a href=\"" . $upcoming_data[$i]->html_url . "\">";
			$output .= $upcoming_data[$i]->name . "</a></h3>";
			if ($upcoming_data[$i]->due_at == null) {
				$output .= "<h4>Due: " . "None" . "</h4>";
			} else {
				//Change default formatting returned by API to: December 7th, 6:00PM local time
				$due_date = format_date($upcoming_data[$i]->due_at);
				$output .= "<h4>Due: " . $due_date . "</h4>";
			}
			$output .= "</section>";
		}
	}
	$output .= "</section>";
	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h2>Upcoming Assignments</h2>";
		$output .= "<h3>No Upcoming Assignments</h3>";
		$output .= "</section>";
		return $output;
	}

}

function get_past_assignments($data) {
	//Using course data, filter out assignment feedback
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Recent Feedback</h4>";
	$content_exists = FALSE;
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission") {
			if ($data[$i]->submission_type == "online_upload" || $data[$i]->submission_type == "online_url") {
				$content_exists = TRUE;
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

	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h4>Recent Feedback</h4>";
		$output .= "<h5>No Recent Feedback</h5>";
		$output .= "</section>";
		return $output;
	}
}

function get_past_discussions($data) {
	//Using course data, filter out discussion feedback
	$output = "";
	$output .= "<h4>Recent Feedback</h4>";
	$content_exists = FALSE;
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "DiscussionTopic" && property_exists($data[$i], "workflow_state")) {
				$content_exists = TRUE;
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
	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h4>Recent Feedback</h4>";
		$output .= "<h5>No Recent Feedback</h5>";
		$output .= "</section>";
		return $output;
	}
}

function get_past_quizzes($data) {
	//using course data, filter out quiz feedback
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Recent Feedback</h4>";
	$content_exists = FALSE;
	for ($i=0; $i<count($data); $i++) {
		if ($data[$i]->type == "Submission") {
			if ($data[$i]->submission_type == "online_quiz") {
				$content_exists = TRUE;
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
	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h4>Recent Feedback</h4>";
		$output .= "<h5>No Recent Feedback</h5>";
		$output .= "</section>";
		return $output;
	}
}

function get_upcoming_assignments($upcoming_data) {
	//Filter out upcoming assignments
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Upcoming Assignments</h4>";
	$content_exists = FALSE;
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false) {
			$is_assignment = true;
			for ($j=0; $j<count($upcoming_data[$i]->submission_types); $j++) {
				if ($upcoming_data[$i]->submission_types[$j] == "discussion_topic" || $upcoming_data[$i]->submission_types[$j] == "online_quiz") {
					$is_assignment == false;
				}
			}

			if ($is_assignment == true) {
				$content_exists = TRUE;
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
	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h4>Upcoming Assignments</h4>";
		$output .= "<h5>No upcoming assignments</h5>";
		$output .= "</section>";
		return $output;
	}
}

function get_upcoming_discussions($upcoming_data) {
	//Filter out upcoming discussions
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Upcoming Discussions</h4>";
	$content_exists = FALSE;
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->submission_types[0] == "discussion_topic") {
			$content_exists = TRUE;
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

	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h4>Upcoming Discussions</h4>";
		$output .= "<h5>No upcoming discussions</h5>";
		$output .= "</section>";
		return $output;
	}
}

function get_upcoming_quizzes($upcoming_data) {
	$output = "";
	$output .= "<section>";
	$output .= "<h4>Upcoming Quizzes</h4>";
	$content_exists = FALSE;
	for ($i=0; $i<count($upcoming_data); $i++) {
		if ($upcoming_data[$i]->grading_type == "points" && $upcoming_data[$i]->has_submitted_submissions == false && $upcoming_data[$i]->submission_types[0] == "online_quiz") {
			$content_exists = TRUE;
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

	if ($content_exists) {
		return $output;
	} else {
		$output = "<section>";
		$output .= "<h4>Upcoming Quizzes</h4>";
		$output .= "<h5>No upcoming quizzes</h5>";
		$output .= "</section>";
		return $output;
	}
}

function format_date($date) {
	//Change date format to: December 7th, 6:00PM Pacific time
	$date = date_create($date);
	$date_timezone = date_timezone_set($date, timezone_open("America/Los_angeles"));
	$formatted_date = date_format($date_timezone, "F jS, g:iA");
	return $formatted_date;
}

    function get_records($connection, $sql, $parameters) {
        //Query the database based on the information requested
    	try {
    		$stmt = $connection->prepare($sql);
			$stmt->setFetchMode (PDO::FETCH_ASSOC);
			$stmt->execute($parameters);
			//returns associative array
    		return $stmt;
    	} catch (Exception $e) {
    		echo '"error":"' . $e->getCode() . '","text":"' . $e->getMessage() . '"';
    		exit;
    	}
    } //end get_records

 ?>