<?php if( AuthController::isAuthorized()):?>
	<form action="<?php echo FrontController::urlFor('post');?><?php if($q !== null){echo '?q=' . $q;}?>" method="post" onsubmit="return confirm('Are you sure you want to delete <?php echo $post->title;?>?');">
		<input type="hidden" name="id" value="<?php echo $post->id;?>" />
		<input type="hidden" name="_method" value="delete" />
		<button type="submit" name="delete_button"><span>delete</span></button>
		<input type="hidden" name="last_page_viewed" value="<?php echo $page;?>" />
        <a href="<?php echo FrontController::urlFor('post', array('id'=>$post->id, 'last_page_viewed'=>$this->page));?>">edit</a>
	</form>
<?php endif;?>
