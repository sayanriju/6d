<?php class_exists('TodayResource') || require('resources/TodayResource.php');?>
<!doctype html>
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
	<body>
		<section class="main">
			<aside class="filter">
				<header class="main">
					<h1>
						<a href="<?php echo Application::urlForWithMember(null);?>" title="<?php echo Application::$member->profile != null ? Application::$member->profile->site_name : Application::$member->member_name;?>">
							<span>
								<?php echo Application::$member->profile != null ? Application::$member->profile->site_name : Application::$member->member_name;?>
							</span>
						</a>
					</h1>
					<?php if(AuthController::isAuthorized()):?>
					<p>hey <a href="http://<?php echo Application::$current_user->url;?>" title=""><?php echo Application::$current_user->name;?></a></p>
					<?php endif;?>
				</header>
				<dl>
					<dt>
						<?php if(AuthController::isAuthorized()):?>
						<a href="<?php echo Application::urlForWithMember('posts');?>" title="Your whole info stream">Infostream</a>
						<?php else:?>
						Infostream
						<?php endif;?>
						</dt>
					<dd>
						<a href="<?php echo Application::urlForWithMember('today');?>" title="Today's News">Today's News</a>
						<?php if(TodayResource::get_todays_count()>0):?>
						<span class="count"><?php echo TodayResource::get_todays_count();?></span>
						<?php endif;?>
					</dd>
					<dd>
						<a href="<?php echo Application::urlForWithMember('photos');?>" title="Photos">Photos</a>
					</dd>
					<dd>
						<a href="<?php echo Application::urlForWithMember('addressbook');?>" title="Addressbook">Addressbook</a>
					</dd>
					<dd>
						<a href="<?php echo Application::urlForWithMember('profile');?>" title="Profile">Profile</a>
					</dd>
					<dd>
						<a href="<?php echo Application::urlForWithMember('settings');?>" title="Settings">Settings</a>
						|
						<?php if(AuthController::isAuthorized()):?>
						<a href="<?php echo FrontController::urlFor('logout');?>" title="Logout">Logout</a>
						<?php else:?>
						<a href="<?php echo FrontController::urlFor('login');?>" title="Login">Login</a>
						<?php endif;?>					
					</dd>
				</dl>
				<dl>
					<dt>Create</dt>
					<dd>
						<a href="<?php echo Application::urlForWithMember('photos');?>" title="Add a photo" id="photos_link">Add a photo</a>
					</dd>
					<dd>
						<a href="<?php echo Application::urlForWithMember('post');?>" title="Add a post" id="new_post_link">Add a post</a>						
					</dd>
				</dl>
			</aside>
			<section class="info-stream">
				<div id="user_message"></div>
				{$output}
			</section>
		</section>
		<footer class="main">
		
		</footer>
	</body>
</html>