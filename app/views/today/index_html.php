<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php class_exists('Date') || require('lib/Date.php');?>
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
				<p><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</p>
			</aside>
			<p><?php echo $post->body;?></p>
		</header>
			<?php break;
			case('link'):?>
		<header>
			<aside rel="author">
				<img src="<?php echo $author->profile->photo_url;?>" class="thumbnail" />
				<p><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</p>
			</aside>
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
				<p><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</p>
			</aside>
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
				<p><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</p>
			</aside>
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
				<p><a href="http://<?php echo $post->source;?>" title=""><?php echo $author->name;?></a> wrote <?php echo Date::time_since(time() - strtotime($post->post_date));?> ago.</p>
			</aside>
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
				<a href="<?php echo FrontController::urlFor(null, array('tag'=>$text));?>"><?php echo $text;?></a>
				<?php endforeach;?>
			</aside>
		</footer>
		<?php require('views/post/menu_html.php');?>
	</article>
<?php endforeach;?>