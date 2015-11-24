$('document').ready(function() {

	function get_assignments(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#course_assignments').html(data);
		});
	} //end get_assignments

	$("input:radio[name=courses]").change(function() {
		var course = $(this).val();
		get_assignments(course);
	});
});