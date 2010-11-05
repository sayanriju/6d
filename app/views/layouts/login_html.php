<?php
	class_exists('UserResource') || require('resources/UserResource.php');
	class_exists('ProfileResource') || require('resources/ProfileResource.php');
?><!DOCTYPE html>
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
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		{$resource_js}
	</head>
	<body class="<?php echo $this->name;?>">
		<div class="view">
			<div id="user_message"<?php echo (Resource::getUserMessage() !== null ? ' style="display:block;"' : null);?>>
				<?php echo Resource::getUserMessage();?>
			</div>
			<section class="content">
			{$output}
			</section>
			<?php if(AuthController::isAuthorized()):?>
			<nav id="admin_menu" class="main">
				<a id="photos_link" href="<?php echo Application::urlForWithUser('photos');?>" title="show all photos">media</a>
				<a href="<?php echo Application::urlForWithUser(null);?>" id="home_link" title="go to your home page">home</a>
				<a href="<?php echo Application::urlForWithUser('post');?>" id="new_post_link" title="new post">new post</a>
				<a href="<?php echo Application::urlForWithUser('posts');?>" id="all_posts_link" title="show all posts">posts</a>
				<a href="<?php echo Application::urlForWithUser('addressbook');?>" id="addressbook_link" title="show your addressbook">addressbook</a>
				<a href="<?php echo Application::urlForWithUser('profile');?>" id="profile_link" title="show your profile">profile</a>
				<?php if(AuthController::isSuperAdmin()):?>
				<a href="<?php echo Application::urlForWithUser('members');?>" id="members_link" title="See all the members in your network">members</a>
				<a href="<?php echo Application::urlForWithUser('member');?>" id="member_link" title="Create a new member">add a member</a>
				<?php endif;?>
			</nav>
			<p>Welcome <?php echo Application::$current_user->name;?></p>
			<?php endif;?>
			<footer id="footer">
				<p>Powered by <a href="http://get6d.com/" title="6d">6d</a></p>
				<nav>
					<?php if(!AuthController::isAuthorized()):?>
					<a href="<?php echo FrontController::urlFor('login');?>" title="Login">Login</a>
					<?php else:?>
					<a href="<?php echo FrontController::urlFor('logout');?>" title="Logout">Logout</a>
					<?php endif;?>
					<a href="<?php echo Application::urlForWithMember('blog');?>" title="Blog">Blog</a>
					<a href="<?php echo Application::urlForWithMember('profile');?>" title="Profile page">Profile</a>
					<a href="<?php echo FrontController::urlFor('members');?>" title="Member directory">Members</a>
				</nav>
			</footer>
		</div>
		<noscript>requires Javascript. Please either turn on Javascript or get a browser that supports Javascript to use 6d.</noscript>
	</body>
</html>
