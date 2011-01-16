<form action="<?php echo App::url_for('login');?>" method="post" id="login_form">
	<fieldset>
		<legend>Login</legend>
		<p>
			<label for="email">name@example.com</label>
			<input type="text" value="" id="email" name="email" />
		</p>
		
		<p>
			<label for="password">Password</label>
			<input type="password" value="" id="password" name="password" />
		</p>
	</fieldset>
	<toolbar>
		<button type="submit">Sign In</button>
	</toolbar>
	
</form>