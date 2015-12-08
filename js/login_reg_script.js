$(document).ready(function() {

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

	$('#register_form').submit(function() {
		event.preventDefault();
		register_user();
	});

	$('#login_form').submit(function() {
		event.preventDefault();
		login();
	});



});