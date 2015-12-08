$(document).ready(function() {

	function register_user() {
		//Gets data from the register form, send it to server side processing
		//If it's successful, the user will be able to log in with new account information
		$('.loading').show();
		var url = "inc/process_user.php";
		$.ajax({
			method: "POST",
			url: url,
			data: $('#register_form').serialize()
		}).done(function() {
			$('.loading').fadeOut();
			$('#message').html("Success!");
			$('.register_container').fadeOut();
		});
	}

	function login() {
		//Gets data from login form, sends it to server side processing
		//If it's successful, it replaced the login page content with the Canvas content
		var url = "inc/check_login.php";
		$.ajax({
			method: "POST",
			url: url,
			data: $('#login_form').serialize()
		}).done(function(content) {
			$('.loading').fadeOut();
			$('body').html(content);
		});
	}

	$('#register_form').submit(function() {
		event.preventDefault();
		register_user();
	});

	$('#login_form').submit(function() {
		$('.loading').show();
		event.preventDefault();
		login();
	});



});