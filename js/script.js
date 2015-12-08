function get_course_info(course) {
	$('.loading').show();
	var jqxhr = $.ajax({
		url: "inc/get_assignments.inc.php?course=" + course,
		method:"GET",
	}).done(function(data) {
		$('.loading').hide();
		$('#single_course_updates').html(data);
	});
} //end get_assignments

function logout() {
	var url = "inc/logout.inc.php";
	$.ajax({
		method: "GET",
		url: url,
	}).done(function(data) {
		$('body').html(data);
	});
}

$('#single_course_updates').on('click', '#button-assignments', function() {
	$('#button-assignments').addClass('button-selected').removeClass('button-unselected');
	$('#button-discussions, #button-quizzes').addClass('button-unselected').removeClass('button-selected');

	$('#discussions, #quizzes').removeClass('tabs-selected').fadeOut().addClass('tabs-unselected');
	$('#assignments').addClass('tabs-selected').fadeIn().removeClass('tabs-unselected');
});

$('#single_course_updates').on('click', '#button-discussions', function() {
	$('#button-discussions').addClass('button-selected').removeClass('button-unselected');
	$('#button-assignments, #button-quizzes').addClass('button-unselected').removeClass('button-selected');

	$('#assignments, #quizzes').removeClass('tabs-selected').fadeOut().addClass('tabs-unselected');
	$('#discussions').addClass('tabs-selected').fadeIn().removeClass('tabs-unselected');
});

$('#single_course_updates').on('click', '#button-quizzes', function() {
	$('#button-quizzes').addClass('button-selected').removeClass('button-unselected');
	$('#button-assignments, #button-discussions').addClass('button-unselected').removeClass('button-selected');

	$('#discussions, #assignments').removeClass('tabs-selected').fadeOut().addClass('tabs-unselected');
	$('#quizzes').addClass('tabs-selected').fadeIn().removeClass('tabs-unselected');
});