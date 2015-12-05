$(document).ready(function() {

	function get_course_info(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#single_course_updates').html(data);
		});
	} //end get_assignments

	$("input:radio[name=courses]").change(function() {
		var course = $(this).val();
		get_course_info(course);
	});

	$('#single_course_updates').on('click', '#button-assignments', function() {
		$('#assignments').addClass('tabs-selected').removeClass('tabs-unselected');
		$('#discussions, #quizzes').removeClass('tabs-selected').addClass('tabs-unselected');
	});

	$('#single_course_updates').on('click', '#button-discussions', function() {
		$('#discussions').addClass('tabs-selected').removeClass('tabs-unselected');
		$('#assignments, #quizzes').removeClass('tabs-selected').addClass('tabs-unselected');
	});

	$('#single_course_updates').on('click', '#button-quizzes', function() {
		$('#quizzes').addClass('tabs-selected').removeClass('tabs-unselected');
		$('#discussions, #assignments').removeClass('tabs-selected').addClass('tabs-unselected');
	});
});