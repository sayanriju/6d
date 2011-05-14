<form action="<?php echo AppResource::url_for_member("profile");?>" method="post">
	<fieldset>
		<legend><?php echo $owner->display_name;?></legend>
		<p>
			<label for="owner[email]">Email</label>
			<input type="email" value="<?php echo $owner->email;?>" name="owner[email]" id="owner[email]" />
		</p>
		<p>
			<label for="owner[signin]">Signin Name</label>
			<input type="text" value="<?php echo $owner->signin;?>" name="owner[signin]" id="owner[signin]" />
		</p>
		<p>
			<label for="owner[in_directory]">In Directory?</label>
			<input type="checkbox" value="true" name="owner[in_directory]" id="owner[in_directory]"<?php echo $owner->in_directory ? " checked" : null;?> />
		</p>
	</fieldset>
	<input type="hidden" name="_method" value="put" />
	<button type="submit">Save</button>
</form>