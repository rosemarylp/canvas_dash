$('document').ready(function() {
	$("input:radio[name=courses]").change(function() {
		var course = $(this).val();
	});

	function get_assignments(course) {
		var jqxhr = $.ajax({
			url: "inc/get_assignments.inc.php?course=" + course,
			method:"GET",
		}).done(function(data) {
			$('#container-assignments').html(data);
		});
	} //end get_results
});