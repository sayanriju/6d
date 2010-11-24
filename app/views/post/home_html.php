<?php if($post != null):?>
<section id="posts">
	<div class="post">
		<h1><?php echo urldecode($post->title);?></h1>	
		<div class="body">
			<?php echo urldecode($post->body);?>
		</div>
		<div class="footer">
<?php if(AuthController::is_authorized() && $post->owner_id == Application::$current_user->person_id):?>
			<a href="<?php echo Application::url_with_member('post', array('id'=>$post->id));?>">edit</a>
<?php endif;?>
		</div>
	</div>
</section>
<?php endif;?>