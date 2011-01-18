<article class="hentry <?php echo $post->type;?>" id="view_<?php echo $post->custom_url;?>" post_id="<?php echo $post->id;?>">
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
			<h2 class="title"><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<p class="body"><?php echo urldecode($post->body);?></p>
	
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
			<h2 class="title"><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<aside class="description body">
			<?php echo Post::get_excerpt($post);?>
		</aside>

	<?php endif;?>
		<footer class="meta">
			<p class="type"><?php echo $post->type;?></p>
			<p class="description"><?php echo $post->description;?></p>
			<p class="tags"><?php echo $post->tags;?></p>
			<date class="post_date"><?php echo $post->post_date;?></date>
			<p class="is_published"><?php echo $post->is_published ? 'public' : 'private';?></p>
			<p class="is_home_page"><?php echo $post->isHomePage($this->getHome_page_post_id());?></p>
		</footer>
	</section>
	<footer>
		<?php require('views/post/menu_html.php');?>
	</footer>
</article>
