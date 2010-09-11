<?php class_exists('UserResource') || require('resources/UserResource.php');?>
<?php if($post == null):?>
<article class="hentry">
	<p>Sorry, the page you're looking for doesn't exist.</p>
</article>
<?php else:?>
<article class="hentry <?php echo $post->type;?>">
	<?php switch($post->type){
		case('status'):?>
		<header>
			<h2><?php echo $post->body;?></h2>
		</header>
		<?php break;
		case('link'):?>
	<header>
		<h2><a href="<?php echo $post->body;?>" title="<?php echo $post->title;?>"><?php echo $post->title;?></a></h2>
	</header>
	<section class="entry-content">
		<p><?php echo $post->description;?></p>
	</section>
		<?php
			break;
		case('photo'):?>
	<header>
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
		<h2><a href="<?php echo FrontController::urlFor($post->custom_url);?>" rel="bookmark" title="<?php echo $post->title;?>"><?php echo $post->title;?></a></h2>
	</header>
	<section class="entry-content">
		<?php echo $post->body;?>
	</section>
	<?php 
		break;
	}?>
	<footer class="post-info">
		<?php $page = 1; require('views/post/menu_html.php');?>
		<time datetime="<?php echo $post->date;?>">
			<span class="day"><?php echo date('jS', strtotime($post->post_date));?></span>
			<span class="month"><?php echo date('M', strtotime($post->post_date));?></span>
			<span class="year"><?php echo date('Y', strtotime($post->post_date));?></span>
		</time>
		<aside rel="author">
			<p><?php echo $post->source;?></p>
		</aside>
		<aside rel="tags">
			<?php foreach(String::explodeAndTrim($post->tags) as $text):?>
			<a href="<?php echo FrontController::urlFor(null, array('tag'=>$text));?>"><?php echo $text;?></a>
			<?php endforeach;?>
		</aside>
	</footer>
</article>
<?php endif;?>
