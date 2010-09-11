<?php
	class_exists('UserResource') || require('resources/UserResource.php');
	class_exists('ProfileResource') || require('resources/ProfileResource.php');
?><!doctype html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{$title}</title>
		<link rel="icon" href="<?php echo FrontController::urlFor('images');?>6dicon.png"/>
		<meta name="description" content="{$description}"/>
		<meta name="keywords" content="{$keywords}"/>
		<meta name="viewport" content="width=980"/>
	  	<link rel="stylesheet" type="text/css" href="<?php echo FrontController::urlFor('themes');?>css/default.css" media="screen" />	
		{$resource_css}
		<script type="text/javascript" charset="utf-8" src="<?php echo FrontController::urlFor('js');?>NotificationCenter.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo FrontController::urlFor('js');?>default.js" id="default_script"></script>
		<script type="text/javascript">
			// This is required so the ajax requests are to the correct url in the multi member case.
			SDObject.rootUrl = '<?php echo FrontController::urlFor(null);?>';
		</script>
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		{$resource_js}
	</head>
	<body>
		<header id="banner">
			<h1><a href="<?php echo FrontController::urlFor(null);?>" title="Home"><span><?php echo Application::$member->profile->site_name;?></span></a></h1>
			<nav>
				<ul>
<?php $pages = Post::findPublishedPages(Application::$member->person_id);?>
<?php while($pages != null && $page = array_shift($pages)):?>
	<?php if(!$page->isHomePage($this->getHome_page_post_id())):?>
					<li><a href="<?php echo FrontController::urlFor($page->custom_url);?>" title="<?php echo $page->description;?>"><?php echo $page->title;?></a></li>
	<?php endif;?>
<?php endwhile;?>
<?php if(AuthController::isAuthorized()):?>
					<li>
						<p>Welcome <?php echo Application::$current_user->name;?></p>
					</li>
<?php endif;?>
				</ul>
			</nav>

		</header>
		<aside id="author">
			<a href="<?php echo FrontController::urlFor(null);?>" title="Go back to my home page">
				<img width="52" height="52" src="<?php echo Application::$member->person->profile->photo_url;?>" alt="photo of <?php echo Application::$member->name;?>" class="author" />
			</a>
		  	<footer id="tweets">
				<nav>
					<?php if(!AuthController::isAuthorized()):?>
					<a href="<?php echo FrontController::urlFor('login');?>" title="Login">Login</a>
					<?php endif;?>
				</nav>
			</footer>
		</aside>
		<section id="content">
			<div id="user_message"<?php echo (Resource::getUserMessage() !== null ? ' style="display:block;"' : null);?>>
				<?php echo Resource::getUserMessage();?>
			</div>
			{$output}
		</section>

		<?php if(AuthController::isAuthorized()):?>
		<nav id="admin_menu" class="main">
			<a href="<?php echo FrontController::urlFor(Application::$current_user->member_name);?>" id="home_link" title="go to your home page">home</a>
			<a href="<?php echo FrontController::urlFor('post');?>" id="new_post_link" title="new post">new post</a>
			<a href="<?php echo FrontController::urlFor('posts');?>" id="all_posts_link" title="show all posts">posts</a>
			<a href="<?php echo FrontController::urlFor('photos');?>" id="all_photos_link" title="show all photos">media</a>
			<a href="<?php echo FrontController::urlFor('addressbook');?>" id="addressbook_link" title="show your addressbook">addressbook</a>
			<a href="<?php echo FrontController::urlFor('profile');?>" id="profile_link" title="show your profile">profile</a>
			<?php if(AuthController::isSuperAdmin()):?>
			<a href="<?php echo FrontController::urlFor('members');?>" id="members_link" title="See all the members in your network">members</a>
			<a href="<?php echo FrontController::urlFor('member');?>" id="member_link" title="Create a new member">add a member</a>
			<?php endif;?>
		 	<a href="<?php echo FrontController::urlFor('logout');?>" id="logout_link">logout</a>
		</nav>
		<?php endif;?>
		<footer id="footer">
			<p>&copy;<?php echo date('Y');?> Powered by <a href="http://get6d.com/" title="6d">6d</a></p>
		</footer>
		<noscript>requires Javascript. Please either turn on Javascript or get a browser that supports Javascript to use 6d.</noscript>
	</body>
</html>
