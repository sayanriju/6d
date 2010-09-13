<?php if(AuthController::isAuthorized() && Application::$current_user->person_id === $post->owner_id):?>
	<form action="<?php echo Application::urlForWithUser('post');?><?php if($q !== null){echo '?q=' . $q;}?>" method="post" onsubmit="return confirm('Are you sure you want to delete <?php echo $post->title;?>?');">
		<input type="hidden" name="id" value="<?php echo $post->id;?>" />
		<input type="hidden" name="_method" value="delete" />
		<button type="submit" name="delete_button">delete</button>
		<input type="hidden" name="last_page_viewed" value="<?php echo $page;?>" />
        <a href="<?php echo Application::urlForWithUser('post', array('id'=>$post->id, 'last_page_viewed'=>$this->page));?>">edit</a>
	</form>
<?php endif;?>
