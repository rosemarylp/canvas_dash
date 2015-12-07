function get_course_info(course) {
	var jqxhr = $.ajax({
		url: "inc/get_assignments.inc.php?course=" + course,
		method:"GET",
	}).done(function(data) {
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

	$('#assignments').addClass('tabs-selected').removeClass('tabs-unselected');
	$('#discussions, #quizzes').removeClass('tabs-selected').addClass('tabs-unselected');
});

$('#single_course_updates').on('click', '#button-discussions', function() {
	$('#button-discussions').addClass('button-selected').removeClass('button-unselected');
	$('#button-assignments, #button-quizzes').addClass('button-unselected').removeClass('button-selected');

	$('#discussions').addClass('tabs-selected').removeClass('tabs-unselected');
	$('#assignments, #quizzes').removeClass('tabs-selected').addClass('tabs-unselected');
});

$('#single_course_updates').on('click', '#button-quizzes', function() {
	$('#button-quizzes').addClass('button-selected').removeClass('button-unselected');
	$('#button-assignments, #button-discussions').addClass('button-unselected').removeClass('button-selected');

	$('#quizzes').addClass('tabs-selected').removeClass('tabs-unselected');
	$('#discussions, #assignments').removeClass('tabs-selected').addClass('tabs-unselected');
});