$('document').ready(function() {

	function get_course_info(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?include=info&course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#single_course_updates').html(data);
		});
	} //end get_assignments

	function get_course_quizzes(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?include=quizzes&course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#quizzes').html(data);
		});
	} //end get_course_quizzes

	function get_course_discussions(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?include=discussions&course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#discussions').html(data);
		});
	} //end get_course_discussions

	function get_course_assignments(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?include=assignments&course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#assignments').html(data);
		});
	} //end get_course_assignments

	$("input:radio[name=courses]").change(function() {
		var course = $(this).val();
		get_course_info(course);
		// get_course_assignments(course);
		// get_course_discussions(course);
		// get_course_quizzes(course);
	});
});