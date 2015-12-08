<body>

	<header>
		<div><h1>Canvas Dash</h1>
		<!-- Welcome, {user} -->
		<?php echo get_self(); ?></div>
		<nav>
			<button id="settings">Settings</button>
			<button id="log_out">Log Out</button>
			<script>
				$('#log_out').click(function() {
					event.preventDefault();
					logout();
				});
			</script>
		</nav>
	</header>

	<div class="loading"></div>

	<main>
		<div class="container" id="all_course_updates">
			<?php echo get_all_activity(); ?>
			<?php echo get_all_upcoming(); ?>
			<!-- Container for recent feedback for ALL courses -->
			<!-- Container for upcoming assignments in ALL courses -->
		</div>

		<section class="container" id="all_courses">
			<h2>Your Courses</h2>
			<div id="course_button_wrapper">
				<!-- Buttons for each course -->
				<?php echo get_courses(); ?>
				<script>
				$("input:radio[name=courses]").change(function() {
					var course = $(this).val();
					get_course_info(course);
				});
				</script>
			</div>
			<div class="container" id="single_course_updates">
				<!-- Container for recent feedback for selected course -->
				<!-- Container for upcoming assignments in selected course -->
			</div>

		<!-- End all_courses -->
		</section>
	</main>
	<script src="js/script.js"></script>

</body>
</html>