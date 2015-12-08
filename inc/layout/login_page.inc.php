<!-- This is what the user will see if they are not logged in -->
<!-- When they log in, it will be replaced with the Canvas content -->
<body>
<script src="js/login_reg_script.js"></script>
<div class="loading"></div>

	<header>
		<h1>Welcome to Canvas Dash</h1>
		<h2 id="message"></h2>
	</header>

	<main class="login_reg">
		<section class="login_container">
			<h2>Been Here Before?</h2>
			<form id="login_form">
				<h3>Log In</h3>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username" maxlength="35">

				<label for="password">Password:</label>
				<input type="password" name="password" id="password">

				<input type="submit" name="submit" id="login_submit">
			</form>
		</section>
		<section class="register_container">
			<h2>New to Canvas Dash?</h2>
			<form id="register_form">
				<h3>Create an Account:</h3>
				<label for="new_username">Username:</label>
				<input type="text" name="new_username" id="new_username" maxlength="35">

				<label for="new_password">Password:</label>
				<input type="password" name="new_password" id="new_password">

				<label for="confirm_password">Password:</label>
				<input type="password" name="confirm_password" id="confirm_password">

				<label for="new_name">Name:</label>
				<input type="text" name="new_name" id="new_name" maxlength="45">

				<label for="new_email">Email:</label>
				<input type="email" name="new_email" id="new_email" maxlength="60">

				<input type="submit" name="register_submit" id="register_submit">
			</form>
		</section>
	</main>
</body>
</html>