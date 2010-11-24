<form action="<?php echo App::url_for('login');?>" method="post" id="login_form">
	<fieldset>
		<legend>Sign in</legend>
		<p>
			<label for="email">Email</label>
			<input type="text" value="" id="email" name="email" />
		</p>
		
		<p>
			<label for="password">Password</label>
			<input type="password" value="" id="password" name="password" />
		</p>
		<p>
			<button type="submit">Login</button>
		</p>
	</fieldset>
</form>