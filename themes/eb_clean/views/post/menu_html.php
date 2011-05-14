<?php if(AuthController::is_authed() && AuthController::$current_user->id === $post->owner_id):?>
	<form action="<?php echo AppResource::url_for_member('post');?>" method="post" onsubmit="return confirm('Are you sure you want to delete <?php echo $post->title;?>?');">
		<input type="hidden" name="id" value="<?php echo $post->id;?>" />
		<input type="hidden" name="_method" value="delete" />
		<button type="submit" name="delete_button">delete</button>
        <a href="<?php echo AppResource::url_for_member("post", array("id"=>$post->id));?>">edit</a>
	</form>
<?php endif;?>
