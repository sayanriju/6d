<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php class_exists('Date') || require('lib/Date.php');?>
<?php if(AuthController::isAuthorized() && Application::$current_user->person_id === Application::$member->person_id):?>
<article class="hentry status-entry">				
	<form id="status_form" method="post" action="<?php echo FrontController::urlFor('posts');?>">
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
			<?php $most_recent_status = Post::findMostRecentStatus(Application::$member->person_id);?>
			<img src="<?php echo Application::$current_user->profile->photo_url;?>" class="thumbnail" title="This is me!" />
		</aside>
		<p><?php echo $most_recent_status->body;?></p>
		<time><?php echo Date::time_since(time() - strtotime($most_recent_status->post_date));?> ago.</time>
	</header>
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
		<?php switch($post->type){
			case('status'):?>
		<header>
			<aside rel="author">
				<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
				<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
			</aside>
			<a href="<?php echo Application::urlForWithMember('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>
			<p><?php echo $post->body;?></p>
		</header>
			<?php break;
			case('link'):?>
		<header>
			<aside rel="author">
				<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
				<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
			</aside>
			<a href="<?php echo Application::urlForWithMember('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>
			<h2><a href="<?php echo $post->body;?>" title="<?php echo $post->title;?>"><?php echo $post->title;?></a></h2>
		</header>
		<section class="entry-content">
			<p><?php echo $post->description;?></p>
		</section>
			<?php
				break;
			case('album'):?>
		<header>
			<aside rel="author">
				<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
				<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
			</aside>
			<a href="<?php echo Application::urlForWithMember('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>
			
			<?php
				$album = explode("\n", $post->body);
				foreach($album as $photo):
			?>
			<img src="<?php echo $photo;?>" />			
			<?php endforeach;?>
		</header>
		<section class="entry-content">
			<p><?php echo $post->descrption;?></p>
		</section>
			<?php break;
			case('photo'):?>
		<header>
			<aside rel="author">
				<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
				<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
			</aside>
			<a href="<?php echo Application::urlForWithMember('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>
			<?php if(stripos($post->body, '<img') !== false):?>
			<?php echo $post->body;?>
			<?php else:?>
			<img src="<?php echo $post->body;?>" alt="<?php echo $post->title;?>" />
			<?php endif;?>
		</header>
		<section class="entry-content">
			<p><?php echo $post->description;?></p>
		</section>
			<?php
				break;
			default:?>
		<header>
			<aside rel="author">
				<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
				<time><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</time>
			</aside>
			<a href="<?php echo Application::urlForWithMember('conversation', array('post_id'=>$post->id));?>" title="Show comments for this post" class="info"><?php echo count($post->conversation);?> Comments</a>
			<h2><a href="<?php echo Application::urlForWithMember($post->custom_url);?>" rel="bookmark" title="<?php echo $post->title;?>"><?php echo $post->title;?></a></h2>
		</header>
		<section class="entry-content">
			<?php echo $post->body;?>
		</section>
		<?php 
			break;
		}?>
		<footer class="post-info">
			<aside rel="tags">
				<?php foreach(String::explodeAndTrim($post->tags) as $text):?>
				<a href="<?php echo Application::urlForWithMember('posts/'. $text, array('limit'=>0));?>"><?php echo $text;?></a>
				<?php endforeach;?>
			</aside>
		</footer>
		<?php require('views/post/menu_html.php');?>
	</article>
<?php endforeach;?>