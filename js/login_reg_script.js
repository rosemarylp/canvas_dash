$(document).ready(function() {

	function get_course_info(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#single_course_updates').html(data);
		});
	} //end get_assignments

	function process_form(form_id) {
		//Get the values from the form and put them in an associative array
		var parameters = $(form_id).serialize();
		return parameters;
	} //end process_form

	function register_user() {
		var url = "inc/process_user.php";
		$.ajax({
			method: "POST",
			url: url,
			data: $('#register_form').serialize()
		}).done(function() {
			$('#register_form').hide();
		});
	}

	function login() {
		var url = "inc/check_login.php";
		$.ajax({
			method: "POST",
			url: url,
			data: $('#login_form').serialize()
		}).done(function(content) {
			$('body').html(content);
		});
	}

	function logout() {
		var url = "inc/logout.inc.php";
		$.ajax({
			method: "GET",
			url: url,
		}).done(function(data) {
			$('body').html(data);
		});
	}

	$('#register_form').submit(function() {
		event.preventDefault();
		register_user();
	});

	$('#login_form').submit(function() {
		event.preventDefault();
		login();
	});

	$('header').on('click', '#log_out', function() {
		event.preventDefault();
		logout();
	});

});