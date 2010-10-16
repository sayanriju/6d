<?php class_exists('PostResource') || require('resources/PostResource.php');?>

<?php if(count($posts) == 0):?>
	<article class="hentry noposts">
		<p>No posts</p>
	</article>
<?php endif;?>

<?php foreach($posts as $key=>$post):?>
	<article class="hentry<?php echo ($key === 0 ? ' first': null);?> <?php echo $post->type;?>">
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
			case('album'):?>
		<header>
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
			<h2><a href="<?php echo Application::urlForWithMember($post->custom_url);?>" rel="bookmark" title="<?php echo $post->title;?>"><?php echo $post->title;?></a></h2>
		</header>
		<section class="entry-content">
			<?php echo $post->body;?>
		</section>
		<?php 
			break;
		}?>
		<footer class="post-info">
			<?php require('views/post/menu_html.php');?>
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
<?php endforeach;?>
<?php if(count($posts) > 0):?>
	<nav id="pager">
	<?php if(count($posts) > 0 && $page > 1):?>
		<a href="<?php echo FrontController::urlFor(($name === 'index' ? null : $name . '/')) . ($page > 1 ? $page-1 : null) . ($this->q !== null ? '?q=' . $this->q : null);?>" title="View newer posts"> ← newer</a>
	<?php else:?>
		<span> ← newer</span>
	<?php endif;?>
	<?php if(count($posts) >= $limit):?>
		<a href="<?php echo FrontController::urlFor(($name === 'index' ? null : $name . '/')) . ($page === 0 ? $page+2 : $page+1). ($this->q !== null ? '?q=' . $this->q : null);?>" title="View older posts">older → </a>
	<?php else:?>
		<span>older → </span>
	<?php endif;?>
	</nav>
<?php endif;?>