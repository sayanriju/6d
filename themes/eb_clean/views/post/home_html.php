<?php if($post != null):?>
<section id="posts">
	<div class="post">
		<h1><?php echo urldecode($post->title);?></h1>	
		<div class="body">
			<?php echo urldecode($post->body);?>
		</div>
		<div class="footer">
<?php if(AuthController::is_authed() && $post->owner_id == AuthController::$current_user->id):?>
			<a href="<?php echo AppResource::url_for_member('post', array('id'=>$post->id));?>">edit</a>
<?php endif;?>
		</div>
		<div style="clear:both;"></div>
	</div>
</section>
<?php endif;?>