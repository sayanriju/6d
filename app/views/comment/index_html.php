<?php class_exists('Date') || require('lib/Date.php');?>
<?php $author = $post->get_author();?>
<article>
	<header>
		<aside rel="author">
			<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
		</aside>
		<a href="<?php echo Application::urlForWithMember('comments', array('post_id'=>$post->id));?>" title="Show comments for this post"><?php echo count($post->get_comments());?> Comments</a>
	</header>
	<?php echo $post->body;?>
</article>
<form method="post" action="<?php echo Application::urlForWithMember('comments');?>">
	<fieldset>
		<legend>Add a comment</legend>
		<label for="comment">Write a comment on <?php echo $author->name;?>'s post</label>
		<textarea name="body" id="comment"></textarea>
		<input type="hidden" name="post_id" value="<?php echo $post->id;?>" />
		<button type="submit">Comment</button>
	</fieldset>
</form>
<ol>
<?php foreach($comments as $comment):?>
	<li>
		<?php echo $comment->body;?>
	</li>
<?php endforeach;?>
</ol>