<?php class_exists('Date') || require('lib/Date.php');?>
<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php if($post == null):?>
<article class="hentry single">
	<p>Sorry, the page you're looking for doesn't exist.</p>
</article>
<?php else:?>
<?php $author = PostResource::getAuthor($post);?>
<article class="hentry single <?php echo $post->type;?>">
	<section class="content">
		<header>
	<?php switch($post->type){
		case('status'):?>
			<h2><?php echo urldecode($post->body);?></h2>
		</header>
		<?php break;
		case('link'):?>
			<h2><a href="<?php echo urldecode($post->body);?>" title="<?php echo $post->title;?>"><?php echo urldecode($post->title);?></a></h2>
		</header>
		<section class="entry-content">
			<p><?php echo urldecode($post->description);?></p>
		</section>	
		<?php
		case('album'):?>
			<h3><?php echo urldecode($post->title);?></h3>
			<?php
				$album = explode("\n", urldecode($post->body));
				foreach($album as $photo):
			?>
			<img src="<?php echo $photo;?>" />			
			<?php endforeach;?>
		</header>
		<section class="entry-content">
			<p><?php echo urldecode($post->description);?></p>
		</section>
		<?php
			break;
		case('photo'):?>
			<?php if(stripos(urldecode($post->body), '<img') !== false):?>
			<?php echo urldecode($post->body);?>
			<?php else:?>
			<img src="<?php echo urldecode($post->body);?>" alt="<?php echo urldecode($post->title);?>" />
			<?php endif;?>
		</header>
		<section class="entry-content">
			<p><?php echo urldecode($post->description);?></p>
		</section>
		<?php
			break;
		case('video'):?>
				<h2><a href="<?php echo Application::url_with_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			</header>
			<section class="entry-content">
				<?php echo urldecode($post->body);?>
			</section>
		<?php
			break;
		default:?>
			<h2><?php echo urldecode($post->title);?></h2>
		</header>
		<section class="entry-content">
			<?php echo urldecode($post->body);?>
		</section>
		<?php 
			break;
		}?>
	</section>
	<aside class="reaction">
		<?php if($post->conversation !== null && count($post->conversation) > 0):?>
		<ol>
		<?php $counter = 0;?>
		<?php foreach($post->conversation as $comment):?>
			<li class="author"<?php echo $counter > 1 ? ' style="display: none;"' : null;?>>
				<img src="<?php echo $comment->author->photo_url;?>" class="small" />
				<p><?php echo $comment->body;?></p>
			</li>
			<?php $counter++;?>
		<?php endforeach;?>
		</ol>
		<?php else:?>
		<p class="hint">No comments yet.</p>
		<?php endif;?>
		<?php if(AuthController::is_authorized()):?>
		<form method="post" action="<?php echo Application::url_with_member('conversations');?>" class="comment">
			<fieldset>
				<legend>Add a comment</legend>
				<label for="comment_for_<?php echo $post->id;?>">Write a comment on <?php echo $author->name;?>'s post</label>
				<input type="hidden" name="post_id" value="<?php echo $post->id;?>" />
				<input id="comment_for_<?php echo $post->id;?>" type="text" name="body" value="" />
				<button type="submit">Comment</button>
			</fieldset>
		</form>
		<?php endif;?>
	</aside>
	<footer class="post-info">
		<aside rel="tags">
			<?php foreach(String::explodeAndTrim($post->tags) as $text):?>
			<a href="<?php echo Application::url_with_member('posts/'. $text, array('limit'=>0));?>"><?php echo $text;?></a>
			<?php endforeach;?>
		</aside>
		<?php require('views/post/menu_html.php');?>
	</footer>
</article>
<?php endif;?>
