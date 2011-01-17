<?php
	class_exists('UserResource') || require('resources/UserResource.php');
	class_exists('ProfileResource') || require('resources/ProfileResource.php');
	class_exists('FriendRequest') || require('models/FriendRequest.php');
?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $title;?></title>
		<link rel="icon" href="<?php echo App::url_for('images/6dicon.png');?>"/>
		<meta name="description" content="<?php echo $description;?>"/>
		<meta name="keywords" content="<?php echo $keywords;?>"/>
		<meta name="viewport" content="width=980"/>
	</head>
	<body class="<?php echo $this->name;?>">
		<h1>I deploy via git.</h1>
		<header class="layout">
			<h1>
				<a href="<?php echo Application::url_with_member(null);?>" title="Home">
					<span>
						<?php echo Application::$member->person->profile != null ? Application::$member->person->profile->site_name : Application::$member->member_name;?>
					</span>
				</a>
			</h1>
			<p><?php echo Application::$member->person->profile->site_description;?></p>
		</header>
		<section class="layout">
			<aside id="user_message"<?php echo (Resource::get_user_message() !== null ? ' style="display:block;"' : null);?>>
				<?php echo Resource::get_user_message();?>
			</aside>
			<?php echo $output;?>
		</section>
		<footer class="layout">
			<div class="description">
				<h2>About</h2>
				<img src="<?php echo ProfileResource::getPhotoUrl(Application::$member->person);?>" alt="<?php echo Application::$member->person->name;?>" />
				<p><?php echo Application::$member->person->profile->site_description;?></p>
			</div>
			<?php if(AuthController::is_authorized()):?>
			<nav id="admin_menu">
				<a id="photos_link" href="<?php echo Application::url_with_user('photos');?>" title="show all photos">media</a>
				<a href="<?php echo Application::url_with_user(null);?>" id="home_link" title="go to your home page">home</a>
				<a href="<?php echo Application::url_with_user('post');?>" id="new_post_link" title="new post">new post</a>
				<a href="<?php echo Application::url_with_user('posts');?>" id="all_posts_link" title="show all posts">posts</a>
				<a href="<?php echo Application::url_with_user('addressbook');?>" id="addressbook_link" title="show your addressbook">addressbook (<?php echo FriendRequest::get_total_friend_requests(Application::$current_user->person_id)->number;?>)</a>
				<a href="<?php echo Application::url_with_user('profile');?>" id="profile_link" title="show your profile">profile</a>
				<?php if(AuthController::is_super_admin()):?>
				<a href="<?php echo Application::url_with_user('members');?>" id="members_link" title="See all the members in your network">members</a>
				<a href="<?php echo Application::url_with_user('member');?>" id="member_link" title="Create a new member">add a member</a>
				<?php endif;?>
			 	<a href="<?php echo Application::url_with_user('logout');?>" id="logout_link">logout <?php echo Application::$current_user->member_name;?></a>
			</nav>
			<?php endif;?>
			<p class="copy">&copy;<?php echo date('Y');?> Powered by <a href="http://get6d.com/" title="6d">6d</a></p>
		</footer>
		<noscript>requires Javascript. Please either turn on Javascript or get a browser that supports Javascript to use 6d.</noscript>
	</body>
	<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme('css/default.css');?>" media="screen" />	
	<?php echo $resource_css;?>
	
	<script type="text/javascript" charset="utf-8" src="<?php echo App::url_for('js/NotificationCenter.js');?>"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo App::url_for('js/default.js');?>" id="default_script"></script>
	<script type="text/javascript">
		SDObject.rootUrl = '<?php echo Application::url_with_member(null);?>';
	</script>
	<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php echo $resource_js;?>
</html>
