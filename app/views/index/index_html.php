<section class="list">
	<aside>
		<a href="<?php echo $page > 1 ? Application::url_with_member('blog/' . ($page - 1)) : null;?>">Previous</a>
		<a href="<?php echo $page*$limit < $total->number ? Application::url_with_member('blog/' . ($page + 1)) : null;?>">Next</a>
	</aside>
<?php foreach($posts as $post):?>
	<article class="hentry <?php echo $post->type;?>">
		<time><span class="month"><?php echo date('M', strtotime($post->post_date));?></span><span class="day"><?php echo date('d', strtotime($post->post_date));?></span></time>
	
		<?php if($post->type === 'status'):?>
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<section class="entry-content">
			<p><?php echo urldecode($post->body);?></p>
		</section>
		<?php elseif($post->type === 'photo'):?>
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			<p><?php echo $post->description;?></p>
		</header>
		<section class="entry-content">
			<img src="<?php echo urldecode($post->body);?>" alt="<?php echo $post->title;?>" />
		</section>	
		<?php elseif($post->type === 'video'):?>
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		<?php echo Post::get_excerpt($post);?>
		</header>
		<section class="entry-content">
			<?php echo urldecode($post->body);?>
		</section>	
		<?php else:?>
		<header>
			<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<section class="entry-content">
		<?php echo Post::get_excerpt($post);?>
		</section>
		<?php endif;?>
		<footer>
			<?php require('views/post/menu_html.php');?>
		</footer>
		
	</article>
<?php endforeach;?>
</section>