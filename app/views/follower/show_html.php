<form action="<?php echo Application::url_with_member('follower');?>" method="post" class="body" id="friend_request_form">
	<fieldset>
		<legend><?php echo ($person->name == null ? 'New friend' : $person->name);?></legend>
		<p>
			<label for="email">Email</label>
			<input type="text" id="email" value="<?php echo $person->email;?>" disabled />
		</p>

		<p>
			<label for="url">Url</label>
			<input type="text" name="url" id="url" value="<?php echo $person->url;?>" />
		</p>
		<p>
			<button type="submit" name="cofirm">Confirm as a friend</button>
		</p>
		<input type="hidden" name="_method" value="put" />
		<input type="hidden" name="id" id="id" value="<?php echo $person->id;?>" />
	</fieldset>
</form>
<form action="<?php echo Application::url_with_member('follower');?>" method="post">
	<input type="hidden" name="id" id="id" value="<?php echo $person->id;?>" />
	<input type="hidden" name="_method" value="delete" />
	<button type="submit">Delete</button>
</form>
