<?php if($members !== null):?>
<ul>
	<?php foreach($members as $member):?>
	<li>
		<p>
			<a href="<?php echo App::url_for($member->member_name);?>" title="Go to <?php echo $member->person->name;?>'s site"><?php echo $member->person->name;?></a>
			<?php if(AuthController::is_authorized() && AuthController::is_super_admin()):?>
			<a href="<?php echo App::url_for('member/' . $member->member_name);?>" title="Edit <?php echo $member->person->name;?>">edit</a>
			<?php endif;?>
		</p>
	<?php if(AuthController::is_super_admin()):?>
		<form action="<?php echo App::url_for('member');?>" method="post" class="delete">
			<input type="hidden" name="_method" value="delete" />
			<input type="hidden" name="id" id="id" value="<?php echo $member->id;?>" />
			<button type="submit"><span>Delete</span></button>
		</form>
	<?php endif;?>
	
	</li>
	<?php endforeach;?>
</ul>
<?php else:?>
<p>There are no members in the directory.</p>
<?php endif;?>