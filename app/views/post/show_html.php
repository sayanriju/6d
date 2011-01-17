<article class="hentry <?php echo $post->type;?>">
	<aside class="comments">
		<time>
			<span class="month"><?php echo date('M', strtotime($post->post_date));?></span> <span class="day"><?php echo date('d', strtotime($post->post_date));?></span>
			<span class="year"><?php echo date('Y', strtotime($post->post_date));?></span>
		</time>
		<dl>
		<?php if($post->conversation !== null):?>
			<dt>No comments</dt>
		<?php else:?>
		<?php foreach($post->conversation as $comment):?>
			<dt><?php echo $comment->author->name;?></dt>
			<dd><?php echo $comment->body;?></dd>
		<?php endforeach;?>
		<?php endif;?>
		</dl>
	</aside>
	
	<section class="entry-content">
	
	<?php if($post->type === 'status'):?>
	
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<p><?php echo urldecode($post->body);?></p>
	
	<?php elseif($post->type === 'photo'):?>
	
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			<p><?php echo $post->description;?></p>
		</header>
		<img src="<?php echo urldecode($post->body);?>" alt="<?php echo $post->title;?>" />
		<aside class="description">
			<?php echo Post::get_excerpt($post);?>
		</aside>
	<?php elseif($post->type === 'video'):?>
	
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		<?php echo Post::get_excerpt($post);?>
		</header>
		<?php echo urldecode($post->body);?>
		<aside class="description">
			<?php echo Post::get_excerpt($post);?>
		</aside>
	
	<?php else:?>

		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<aside class="description">
			<?php echo Post::get_excerpt($post);?>
		</aside>

	<?php endif;?>
	
	</section>
	<footer>
		<?php require('views/post/menu_html.php');?>
	</footer>
</article>
