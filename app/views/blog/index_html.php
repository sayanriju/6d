<?php class_exists("PostResource") || require("resources/PostResource.php");?>
<?php foreach($posts as $post):?>
	<article>
		<header>
			<h2><?php echo $post->title;?></h2>
		</header>
		<?php echo PostResource::add_p_tags(Post::get_excerpt($post));?>
		<footer>
			<a href="<?php echo AppResource::url_for_member("blog", array("id"=>$post->id));?>">read more...</a>
		</footer>
	</article>
<?php endforeach;?>
<?php if($post_count !== null && $post_count->total > 0):?>
<nav class="pages">
	<a href="<?php echo $previous_page === 0 ? "javascript:void(0);" : AppResource::url_for_member("blog", array("page"=>$previous_page));?>" title="Go to the previous page" class="<?php echo $previous_page === 0 ? "disabled" : "";?>">
		<span>-</span>
	</a>
	<a href="<?php echo $next_page === $total_pages ? "javascript:void(0);" : AppResource::url_for_member("blog", array("page"=>$next_page));?>" title="Go to the next page" class="<?php echo $next_page === $total_pages ? "disabled" : "";?>">
		<span>+</span>
	</a>
</nav>
<?php endif;?>