<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php if(AuthController::isAuthorized() && $this->current_user->person_id === Application::$member->person_id):?>
<form id="status_form" method="post" action="<?php echo FrontController::urlFor('post');?>">
	<fieldset>
		<legend>Say what? This public message gets sent to all your contacts. It's a quick way to broadcast your thoughts.</legend>
		<input class="post" type="text" name="body" />
		<input type="hidden" name="type" value="status" />
		<input type="hidden" name="groups[]" value="All+Contacts" />
		<button type="submit"><span>Blast it!</span></button>
	</fieldset>
</form>
<?php endif;?>
<?php if($posts == null):?>
	<article class="hentry">
		<?php if(AuthController::isAuthorized() && $this->current_user->person_id === $this->site_member->person_id):?>
		<p>There are no posts right now.</p>
		<a href="<?php echo FrontController::urlFor('post');?>">Create a new one</a>
		<?php else:?>
		<p>There are no posts here.</p>
		<?php endif;?>
	</article>
<?php else:?>
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
<?php endif;?>
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
