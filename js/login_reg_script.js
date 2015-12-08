$(document).ready(function() {

	function register_user() {
		$('.loading').show();
		var url = "inc/process_user.php";
		$.ajax({
			method: "POST",
			url: url,
			data: $('#register_form').serialize()
		}).done(function() {
			$('.loading').hide();
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
			$('.loading').hide();
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