<section class="list">
<?php foreach($posts as $post):?>
	<article class="hentry <?php echo $post->type;?>">
		
		<?php if($post->type === 'status'):?>
		<header>
			<h2><a href="<?php echo AppResource::url_for_member('blog/' . $post->url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			<aside class="author_aside">
			<time>
				<span class="month"><?php echo date('M', $post->post_date);?></span> <span class="day"><?php echo date('d', $post->post_date);?></span>
				<span class="year"><?php echo date('Y', $post->post_date);?></span>
			</time> // <br/>
			<?php echo AppResource::$member->display_name;?>
		</aside>
		</header>
		<section class="entry-content">
			<p><?php echo urldecode($post->body);?></p>
		</section>
		<div style="clear:both;"></div>
		<footer>
			<div class="post_type">STATUS</div>
			<?php require('themes/eb_clean/views/post/menu_html.php');?>
		</footer>
		<?php elseif($post->type === 'photo'):?>
		<header>
			<h2><a href="<?php echo AppResource::url_for_member('blog/' . $post->url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			<p><?php echo $post->excerpt;?></p>
			<aside class="author_aside">
			<time>
				<span class="month"><?php echo date('M', $post->post_date);?></span> <span class="day"><?php echo date('d', $post->post_date);?></span>
				<span class="year"><?php echo date('Y', $post->post_date);?></span>
			</time> // <br/>
			<?php echo AppResource::$member->display_name;?>
		</aside>
		</header>
		<section class="entry-content">
			<img src="<?php echo urldecode($post->body);?>" alt="<?php echo $post->title;?>" />
		</section>
		<div style="clear:both;"></div>
		<footer>
			<div class="post_type"><a href="<?php echo AppResource::url_for_member('photos');?>">PHOTO</a></div>
			<div class="post_tags"><strong>TAGS:</strong>
			<ul>
			<?php foreach(String::explode_and_trim($post->tags) as $text):?>
				<li><a href="<?php echo AppResource::url_for_member('posts/'. $text, array('limit'=>0));?>"><?php echo $text;?></a></li>
			<?php endforeach;?>
			</ul>
			</div>
			<?php if(AuthController::is_authed() && AuthController::$current_user->id === $post->owner_id):?>
				<form action="<?php echo AppResource::url_for_member('post');?><?php if($q !== null){echo '?q=' . $q;}?>" method="post" onsubmit="return confirm('Are you sure you want to delete <?php echo $post->title;?>?');">
					<input type="hidden" name="id" value="<?php echo $post->id;?>" />
					<input type="hidden" name="_method" value="delete" />
					<button type="submit" name="delete_button">delete</button>
			        <a href="<?php echo AppResource::url_for_member('post/' . $post->id);?>">edit</a>
				</form>
			<?php endif;?>
		</footer>
		<?php elseif($post->type === 'video'):?>
		<header>
			<h2><a href="<?php echo AppResource::url_for_member('blog/' . $post->url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			<aside class="author_aside">
			<time>
				<span class="month"><?php echo date('M', $post->post_date);?></span> <span class="day"><?php echo date('d', $post->post_date);?></span>
				<span class="year"><?php echo date('Y', $post->post_date);?></span>
			</time> // <br/>
			<?php echo AppResource::$member->display_name;?>
		</aside>
		<div style="clear:both"></div>
		<?php echo Post::get_excerpt($post);?>
		</header>
		<section class="entry-content">
			<?php echo urldecode($post->body);?>
		</section>	
		<div style="clear:both;"></div>
		<footer>
			<div class="post_type">VIDEO</div>
			<div class="post_tags"><strong>TAGS:</strong>
			</div>
			<?php require('themes/eb_clean/views/post/menu_html.php');?>
		</footer>
		<?php else:?>
		<header>
			<h2><a href="<?php echo AppResource::url_for_member('blog/' . $post->url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			<aside class="author_aside">
			<time>
				<span class="month"><?php echo date('M', $post->post_date);?></span> <span class="day"><?php echo date('d', $post->post_date);?></span>
				<span class="year"><?php echo date('Y', $post->post_date);?></span>
			</time> // <br/>
			<?php echo AppResource::$member->display_name;?>
		</aside>
		</header>
		<section class="entry-content">
		<?php echo Post::get_excerpt($post);?>
		</section>
		<div style="clear:both;"></div>
		<footer>
			<div class="post_type">BLOG</div>
			<div class="post_tags"><strong>TAGS:</strong>
			</div>
		<?php if(AuthController::is_authed() && AuthController::$current_user->id === $post->owner_id):?>
			<form action="<?php echo AppResource::url_for_member('post');?>" method="post" onsubmit="return confirm('Are you sure you want to delete <?php echo $post->title;?>?');">
				<input type="hidden" name="id" value="<?php echo $post->id;?>" />
				<input type="hidden" name="_method" value="delete" />
				<button type="submit" name="delete_button">delete</button>
		        <a href="<?php echo AppResource::url_for_member("post", array("id"=>$post->id));?>">edit</a>
			</form>
		<?php endif;?>
		</footer>
		<?php endif;?>
	</article>
<?php endforeach;?>
<nav class="pages">
	<a href="<?php echo $previous_page === 0 ? "javascript:void(0);" : AppResource::url_for_member("blog", array("page"=>$previous_page));?>" title="Go to the previous page">
		<span>-</span>
	</a>
	<a href="<?php echo $next_page === $total_pages ? "javascript:void(0);" : AppResource::url_for_member("blog", array("page"=>$next_page));?>" title="Go to the next page">
		<span>+</span>
	</a>
</nav></section>