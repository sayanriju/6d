<?php if(AuthController::is_authorized() && Application::$current_user->person_id === $post->owner_id):?>
	<form class="toolbar" action="<?php echo Application::url_with_member('post');?><?php if($q !== null){echo '?q=' . $q;}?>" method="post" onsubmit="return confirm('Are you sure you want to delete <?php echo $post->title;?>?');">
		<input type="hidden" name="id" value="<?php echo $post->id;?>" />
		<input type="hidden" name="_method" value="delete" />
		<button type="submit" name="delete_button" class="delete">delete</button>
	</form>
	<aside class="toolbar" rel="action">
		<button style="display: none;" class="cancel">cancel</button>
        <button class="edit">edit</button>
    	<button class="info">info</button>
	</aside>
	
<?php endif;?>
