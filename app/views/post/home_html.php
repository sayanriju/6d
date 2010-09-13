<?php if($post != null):?>
<section id="posts">
	<div class="post">
		<h1><?php echo $post->title;?></h1>	
		<div class="body">
			<?php echo $post->body;?>
		</div>
		<div class="footer">
<?php if(AuthController::isAuthorized() && $post->owner_id == Application::$current_user->person_id):?>
			<a href="<?php echo Application::urlForWithUser('post', array('id'=>$post->id));?>">edit</a>
<?php endif;?>
		</div>
	</div>
</section>
<?php endif;?>