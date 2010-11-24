<h1>Index</h1>
<?php
	$posts = Post::findPublishedPosts(0, 10, array('updated'=>'desc', 'type'=>'desc'), Application::$member->person_id);
	$pages = Post::findPublishedPages(Application::$member->person_id);
	$cat = null;
?>
<nav>
	<dl>
		<dt>Pages</dt>
<?php while($pages != null && $page = array_shift($pages)):?>
<?php if(!$page->isHomePage($this->getHome_page_post_id())):?>
		<dd><a href="<?php echo App::url_for($page->custom_url);?>" title="<?php echo urldecode($page->description);?>"><?php echo urldecode($page->title);?></a></dd>
<?php endif;?>
<?php endwhile;?>
<?php if(AuthController::is_authorized()):?>
<?php endif;?>
	</dl>
</nav>
<h2>Latest</h2>
<nav>
	<dl>
	<?php foreach($posts as $post):?>
		<?php if($cat !== $post->type):?>
		<dt><?php echo $post->type === 'post' ? 'articles' : $post->type;?>
		<?php endif;?>
		<?php $cat = $post->type;?>
		<dd class="<?php echo $post->type;?>">
			<a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>"><?php echo strlen($post->title) === 0 ? urldecode($post->body) : urldecode($post->title);?></a>
		</dd>
	<?php endforeach;?>
	</dl>
</nav>
