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
    return $result;
} // end call_api

function get_self() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self?access_token=" . $access_token;
	$data = call_api("GET",$url);
	$data = json_decode($data);
	$output = "<h2>Welcome, " . $data->name . "</h2>";
	return $output;
}

function get_activity() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/users/self/activity_stream?access_token=" . $access_token;
	$data = call_api("GET",$url);
	$data = json_decode($data);
	print_r($data);
	echo $url;
}

function get_courses() {
	global $canvas_site;
	global $access_token;
	$url = $canvas_site . "/courses?access_token=" . $access_token;
	$data = call_api("GET",$url);
	$data = json_decode($data);

	$output = "";
	for ($i=0; $i<count($data); $i++) {
		if (!property_exists($data[$i], "access_restricted_by_date")) {
			$output .= "<input type=\"radio\" name=\"courses\" value=\"" . $data[$i]->id . "\">";
			$output .= $data[$i]->name;
		} else {

		}
	}
	return $output;
}

function get_assignments($course, $canvas_site, $access_token) {
	$url = $canvas_site . "/" . "courses/" . $course . "/assignments?access_token=" . $access_token;
	$data = call_api("GET",$url);
	$data = json_decode($data);
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

 ?>