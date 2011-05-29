<?php
	class_exists('AuthController') || require('controllers/AuthController.php');
?><!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1"/>
		<link rel="icon" href="<?php echo App::url_for('images/6dicon.png');?>"/>
		<meta name="description" content="<?php echo $description;?>"/>
		<meta name="keywords" content="<?php echo $keywords;?>"/>
        <title><?php echo $title;?></title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	</head>
	<body class="<?php echo $resource_name;?>">
	
		<div class="container" id="admin_menu">
			<div class="row">
				<div class="twocol">&nbsp;</div>
				<div class="eightcol">
					<ul>
		            	<li><a href="<?php echo AppResource::url_for_member(null);?>" title="Go home">Home</a></li>
	            		<li><a href="<?php echo AppResource::url_for_member('blog');?>" title="My blog">Blog</a></li>
		            	<li><a href="<?php echo AppResource::url_for_member('profile');?>" title="View your profile">Profile</a></li>
		                <?php if(AuthController::is_authed()):?>
	                	<li><a href="<?php echo AppResource::url_for_member('post');?>" title="Create a new post">New Post</a></li>
	                	<li><a href="<?php echo AppResource::url_for_member('posts');?>" title="See all your posts">Posts</a></li>
		                <?php endif;?>
		                <li><a href="<?php echo AppResource::url_for_member('photos');?>" title="See your photo library">Photo</a></li>
		                <li><a href="<?php echo AppResource::url_for_member(AuthController::is_authed() ? 'signout' : 'signin');?>" title="Logout"><?php echo AuthController::is_authed() ? 'Sign Out' : 'Sign In';?></a>
		                <?php if(AuthController::is_authed()):?>
		                	<ul id="user_menu" style="display:none">
		                		<li><a href="<?php echo AppResource::url_for_member('addressbook');?>" title="Your addressbook">Addressbook</a></li>
		                	</ul>
		                <?php endif;?>
		                </li>
					</ul>
				</div>
				<div class="twocol">&nbsp;</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="twocol">&nbsp;</div>
				<div class="eightcol">
					<header>
						<h1><a href="<?php echo AppResource::url_for_member(null);?>" title="<?php echo AppResource::$member->display_name;?>">
								<span>
									<?php echo Setting::find("title", AppResource::$member->id)->value;?>
								</span>
							</a>
						</h1>
						<p>This site is built on 6d. <a href="http://www.get6d.com">Read more &#187;</a></p>
						<ul id="pages_menu">
						<?php
							$pages = Post::find_public_pages(AppResource::$member->id);
							$pages = $pages === null ? array() : $pages;
						
							foreach($pages as $page):?>
							<?php if($page->name === "profile") continue;?>
							<li<?php echo count($request->path) > 0 && $page->name === $request->path[0] ? ' class="current_page_item"' : null;?>>
								<a href="<?php echo AppResource::url_for_member($page->name === "index" ? null : $page->name);?>">
									<?php echo $page->title;?>
								</a>
							</li>
						<?php endforeach;?>
						</ul>
						<div style="clear:both;"></div>
					</header>
				</div
				<div class="twocol">&nbsp;</div>
			</div>
		</div>		
		<div class="container">
			<div class="row">
				<div class="twocol">&nbsp;</div>
				<div class="eightcol" id="body_content">
					<div id="user_message"<?php echo (App::get_user_message() !== null ? ' style="display:block;"' : null);?>>
						<?php echo App::get_user_message();?>
					</div>
					<?php echo $output;?>
				</div>
				<div class="twocol">&nbsp;</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="twocol">&nbsp;</div>
				<div class="eightcol">
					<footer>
						<p>footer</p>
					</footer>
				</div>
				<div class="twocol">&nbsp;</div>
			</div>
		</div>
		
		<noscript>requires Javascript. Please either turn on Javascript or get a browser that supports Javascript to use 6d.</noscript>
	</body>
	<link rel="stylesheet" media="screen" href="<?php echo App::url_for_theme('css/1140.css');?>"/>
	<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme('css/typeimg.css');?>" media="screen" />	
	<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme('css/smallerscreen.css');?>" media="only screen and (max-width: 1023px)" />	
	<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme('css/mobile.css');?>" media="handheld, only screen and (max-width: 767px)" />	
	<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme('css/layout.css');?>" media="screen" />
	<?php echo $css;?>
	<script type="text/javascript" charset="utf-8" src="<?php echo App::url_for('js/NotificationCenter.js');?>"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo App::url_for('js/default.js');?>" id="default_script"></script>
	<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php echo $js;?>
</html>
