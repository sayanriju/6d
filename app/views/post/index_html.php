<?php class_exists("PostResource") || require("resources/PostResource.php");?>
<?php foreach($posts as $post):?>
	<article>
		<header>
			<h2><?php echo $post->title;?></h2>
			<aside>
				<time><?php echo date("Y-m-d h:i:s", $post->post_date);?></time>
			</aside>
		</header>
		<?php echo PostResource::add_p_tags($post->get_excerpt());?>
		<footer>
			<a href="<?php echo AppResource::url_for_user("post", array("id"=>$post->id));?>">edit</a>
		</footer>
	</article>
<?php endforeach;?>
