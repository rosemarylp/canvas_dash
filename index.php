<?php require 'inc/functions.inc.php'; ?>
<!DOCTYPE html>
<html lang="en">
<!-- Author: Rosemary Perkins -->
<head>
	<meta charset="UTF-8">
	<title>Canvas Dash</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="js/script.js"></script>
</head>
<body>
	<header>
		<div><h1>Canvas Dash</h1>
		<!-- Welcome, {user} -->
		<?php echo get_self(); ?></div>
		<nav>
			<button id="settings">Settings</button>
			<button id="log_out">Log Out</button>
		</nav>
	</header>

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
			</div>
			<div class="container" id="single_course_updates">
				<!-- Container for recent feedback for selected course -->
				<!-- Container for upcoming assignments in selected course -->
			</div>

		<!-- End all_courses -->
		</section>
	</main>
</body>
</html>