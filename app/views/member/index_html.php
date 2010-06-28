<?php if($members !== null):?>
<ul>
	<?php foreach($members as $member):?>
	<li>
		<p>
			<a href="<?php echo FrontController::urlFor($member->member_name);?>" title="Go to <?php echo $member->member_name;?>'s site"><?php echo $member->name;?></a>
			<?php if(AuthController::isAuthorized() && AuthController::isSuperAdmin()):?>
			<a href="<?php echo FrontController::urlFor('member/' . $member->member_name);?>" title="Edit <?php echo $member->name;?>">edit</a>
			<?php endif;?>
		</p>
	<?php if(AuthController::isSuperAdmin()):?>
		<form action="<?php echo FrontController::urlFor('member');?>" method="post" class="delete">
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