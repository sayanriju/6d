<form action="<?php echo App::url_for((int)$person->id > 0 ? "member" : "members");?>" method="post">
	<fieldset>
		<legend><?php echo $legend;?></legend>
		<p>
			<label for="member[name]">Name</label>
			<input type="text" name="member[name]" id="member[name]" value="<?php echo $person->name;?>" autocomplete="off" />
		</p>
		<p>
			<label for="member[password]">Password</label>
			<input type="password" name="member[password]" id="member[password]" value="" autocomplete="off" />
		</p>
		<p>
			<label for="member[in_directory]">In Directory?</label>
			<input type="checkbox" value="true" name="member[in_directory]" id="in_directory"<?php echo $person->in_directory ? " checked" : null;?> />
		</p>
<?php if($person->id > 0):?>
		<input type="hidden" value="<?php echo $person->id;?>" name="member[id]" />
		<input type="hidden" value="put" name="_method" />
<?php endif;?>
		<footer>
			<button type="submit"><?php echo $person->id > 0 ? "Save" : "Add";?></button>
		</footer>
	</fieldset>
</form>
<form action="<?php echo App::url_for("member");?>" method="post" onsubmit="return confirm('Are you sure you want to delete?');">
	<input type="hidden" value="<?php echo $person->id;?>" name="member[id]" />
	<input type="hidden" value="delete" name="_method" />
	<button type="submit">delete</button>
</form>