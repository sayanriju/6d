<channel>
	<title><?php echo $title;?></title>
	<link><?php echo Application::url_with_member(null);?></link>
	<description><?php echo $description;?></description>
	<?php foreach($posts as $post):?>
	<item>
		<title><?php echo $post->title;?></title>
		<link><?php echo Application::url_with_member($post->custom_url);?></link>
		<description><?php echo Post::get_excerpt($post);?></description>
	</item>
	<?php endforeach;?>
</channel>