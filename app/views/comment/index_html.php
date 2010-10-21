<?php class_exists('Date') || require('lib/Date.php');?>
<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php $author = PostResource::getAuthor($post);?>
<article class="hentry">
	<header>
		<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
		<aside rel="author">
			<time>
				<a href="http://<?php echo $post->source;?>" title="<?php echo $author->name;?>'s site"><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.
			</time>
		</aside>
		<a href="<?php echo Application::urlForWithMember('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>
	</header>
	<?php echo $post->body;?>
</article>
<?php if(AuthController::isAuthorized()):?>
<form method="post" action="<?php echo Application::urlForWithMember('conversations');?>">
	<fieldset>
		<legend>Add a comment</legend>
		<label for="comment">Write a comment on <?php echo $author->name;?>'s post</label>
		<textarea name="body" id="comment"></textarea>
		<input type="hidden" name="post_id" value="<?php echo $post->id;?>" />
		<button type="submit">Comment</button>
	</fieldset>
</form>
<?php endif;?>
<?php foreach($post->conversation as $comment):?>
<article class="hentry">
	<header>
		<aside rel="author">
			<img src="<?php echo $comment->author->photo_url;?>" class="thumbnail" />
			<time>
				<a href="<?php echo $comment->author->source !== null ? 'http://' . $comment->author->source : Application::urlForWithMember(null);?>" title="<?php echo $comment->author->name;?>'s comment"><?php echo $comment->author->name;?></a> <?php echo Date::time_since(time() - strtotime($comment->date));?> ago.
			</time>
		</aside>
	</header>
	<?php echo $comment->body;?>
	<footer></footer>
</article>
<?php endforeach;?>
