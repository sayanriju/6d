<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php class_exists('Date') || require('lib/Date.php');?>
<?php if(AuthController::is_authed() && AuthController::$current_user->id === AppResource::$member->id):?>
<article class="hentry status-entry">
	<section class="content">
		<form id="status_form" method="post" action="<?php echo App::url_for('posts');?>">
			<fieldset>
				<legend>Status Form</legend>
				<label for="status">What's on your mind?</label>
				<textarea name="body" id="status"></textarea>
				<input type="hidden" name="type" value="status" />
				<input type="hidden" name="groups[]" value="All+Contacts" />
				<button type="submit"><span>Broadcast!</span></button>
			</fieldset>
		</form>
		<header>
			<aside rel="author">
				<?php $most_recent_status = Post::findMostRecentStatus(AppResource::$member->id);?>
				<img src="<?php echo AuthController::$current_user->person->profile->photo_url;?>" class="thumbnail" title="This is me!" />
			</aside>
			<p><?php echo $most_recent_status->body;?></p>
			<time><?php echo Date::time_since(time() - strtotime($most_recent_status->post_date));?> ago.</time>
		</header>
	</section>
	<aside class="reaction"></aside>
	<footer class="post-info"></footer>
</article>
<?php endif;?>

<?php if(count($posts) == 0):?>
	<article class="hentry noposts">
		<p>No posts</p>
	</article>
<?php endif;?>
<?php foreach($posts as $key=>$post):?>
	<?php $author = PostResource::getAuthor($post);?>
	<article class="hentry<?php echo ($key === 0 ? ' first': null);?> <?php echo $post->type;?>">
		<section class="content">
			<header>
				<aside rel="author">
					<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
					<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
				</aside>
				<a href="<?php echo AppResource::url_for_member('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>

	<?php switch($post->type){
		case('status'):?>
				<p><?php echo urldecode($post->body);?></p>
			</header>
		<?php break;
		case('link'):?>
				<h2><a href="<?php echo urldecode($post->body);?>" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
			</header>
			<section class="entry-content">
				<p><?php echo urldecode($post->description);?></p>
			</section>
		<?php
			break;
		case('album'):?>
			</header>
			<section class="entry-content">
			<?php
				$album = explode("\n", urldecode($post->body));
				foreach($album as $photo):
			?>
				<img src="<?php echo $photo;?>" />			
			<?php endforeach;?>
			
				<p><?php echo urldecode($post->descrption);?></p>
			</section>
		<?php break;
		case('photo'):?>
			</header>
			<section class="entry-content">
			<?php if(stripos(urldecode($post->body), '<img') !== false):?>
				<?php echo urldecode($post->body);?>
			<?php else:?>
				<img src="<?php echo urldecode($post->body);?>" alt="<?php echo urldecode($post->title);?>" />
			<?php endif;?>
				<p><?php echo urldecode($post->description);?></p>
			</section>
		<?php
			break;
		default:?>
				<h2><a href="<?php echo AppResource::url_for_member('blog/' . $post->custom_url);?>" rel="bookmark" title="<?php echo urldecode($post->title);?>"><?php echo urldecode($post->title);?></a></h2>
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
			<?php if(AuthController::is_authed()):?>
			<form method="post" action="<?php echo AppResource::url_for_member('conversations');?>" class="comment">
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
				<?php foreach(String::explode_and_trim($post->tags) as $text):?>
				<a href="<?php echo AppResource::url_for_member('posts/'. urldecode($text), array('limit'=>0));?>"><?php echo urldecode($text);?></a>
				<?php endforeach;?>
			</aside>
			<?php require('themes/eb_clean/views/post/menu_html.php');?>
		</footer>
	</article>
	<div style="clear:both"></div>
<?php endforeach;?>
