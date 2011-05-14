<channel>
	<title><?php echo $title;?></title>
	<link><?php echo AppResource::url_for_member(null);?></link>
	<description><?php echo $description;?></description>
	<?php foreach($posts as $post):?>
	<item>
		<title><?php echo $post->title;?></title>
		<link><?php echo AppResource::url_for_member($post->custom_url);?></link>
		<description><?php echo Post::get_excerpt($post);?></description>
	</item>
	<div style="clear:both"></div>
	<?php endforeach;?>
</channel>