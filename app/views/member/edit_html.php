<form action="<?php echo App::url_for('members');?>" method="post" class="body" id="member_form">
	<fieldset>
		<legend><?php echo ($member->id === 0 ? 'New member' : $member->person->name);?></legend>
		<p>
			<label for="name">Name</label>
			<input type="text" id="name" name="person[name]" value="<?php echo $member->person->name;?>" />
		</p>

		<p>
			<label for="email">Email</label>
			<input type="text" id="email" name="person[email]" value="<?php echo $member->person->email;?>" />
		</p>
		<p>
			<label for="member_name">Member Name</label>
			<input type="text" id="member_name" name="member[member_name]" value="<?php echo $member->member_name;?>" />
		</p>
		<p>
			<label for="password">Password</label>
			<input type="password" id="password" name="person[password]" value="" />
		</p>
		<p>
			<label for="is_approved">Approved?</label>
			<input type="checkbox" id="is_approved" name="person[is_approved]" value="true"<?php echo $member->person->is_approved ? ' checked="true"' : null;?> />
		</p>
		<p>
			<label for="do_list_in_directory">List in directory?</label>
			<input type="checkbox" value="true" name="person[do_list_in_directory]" id="do_list_in_directory"<?php echo $member->person->do_list_in_directory ? 'checked="true"' : null;?> />
		</p>
		<p>
			<button type="submit" name="save_button" id="save_button"><span>Save</span></button>
		</p>
		<input type="hidden" name="member[id]" id="id" value="<?php echo $member->id;?>" />
<?php if($member->id !== null):?>
		<input type="hidden" name="_method" value="put" />
<?php endif;?>
	</fieldset>
</form>
<?php if($member->url !== null):?>
<form action="<?php echo App::url_for('follower');?>" method="post" id="friend_request_form">
	<input type="hidden" name="member[id]" id="follower_id" value="<?php echo $member->id;?>" />
	<?php if($member->public_key !== null && strlen($member->public_key) > 0):?>
	<button id="friend_request_button" type="submit"<?php echo (strlen($member->person->url) === 0 ? ' disabled="true"':null);?>>Add as friend again</button>
	<?php else:?>
	<button id="friend_request_button" type="submit"<?php echo (strlen($member->person->url) === 0 ? ' disabled="true"':null);?>>Add as friend</button>
	<?php endif;?>
</form>
<?php endif;?>
