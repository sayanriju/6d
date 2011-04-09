<form action="<?php echo App::url_for((int)$group->id > 0 ? "group" : "groups");?>" method="post">
	<fieldset>
		<legend><?php echo $legend;?></legend>
		<p>
			<label for="group[name]">Name</label>
			<input type="text" name="group[name]" id="group[name]" value="<?php echo $group->name;?>" autocomplete="off" />
		</p>
<?php if($group->id > 0):?>
		<input type="hidden" value="<?php echo $group->id;?>" name="group[id]" />
		<input type="hidden" value="put" name="_method" />
<?php endif;?>
		<footer>
			<button type="submit"><?php echo $group->id > 0 ? "Save" : "Add";?></button>
		</footer>
	</fieldset>
</form>
<form action="<?php echo App::url_for("group");?>" method="post" onsubmit="return confirm('Are you sure you want to delete?');">
	<input type="hidden" value="<?php echo $group->id;?>" name="group[id]" />
	<input type="hidden" value="delete" name="_method" />
	<button type="submit">delete</button>
</form>