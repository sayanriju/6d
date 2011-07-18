<?php class_exists("AuthController") || require("controllers/AuthController.php");?><!DOCTYPE html>
<!--
	 ____     __                          __          ___   ___
	/\  _`\  /\ \      __                /\ \      __/\_ \ /\_ \
	\ \ \/\_\\ \ \___ /\_\    ___     ___\ \ \___ /\_\//\ \\//\ \      __
	 \ \ \/_/_\ \  _ `\/\ \ /' _ `\  /'___\ \  _ `\/\ \\ \ \ \ \ \   /'__`\
	  \ \ \L\ \\ \ \ \ \ \ \/\ \/\ \/\ \__/\ \ \ \ \ \ \\_\ \_\_\ \_/\ \L\.\_
	   \ \____/ \ \_\ \_\ \_\ \_\ \_\ \____\\ \_\ \_\ \_\\____\\____\ \__/.\_\
	    \/___/   \/_/\/_/\/_/\/_/\/_/\/____/ \/_/\/_/\/_//____//____/\/__/\/_/
		everything should be this easy!
-->
<html>
	<head>
        <title><?php echo $title;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="author" content="Joey Guerra" />
		<meta name="keywords" content="<?php echo $keywords;?>" />
		<meta name="description" content="<?php echo $description;?>" />		
		<link rel="icon" type="image/png" href="<?php echo App::url_for("favicon.png");?>" />	
		<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme("css/default.css");?>" />
		<link href="http://fonts.googleapis.com/css?family=Cabin" rel="stylesheet" type="text/css" />
		<link href="http://fonts.googleapis.com/css?family=Lobster" rel="stylesheet" type="text/css" />
		<?php echo $css;?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo App::url_for("js/default.js");?>"></script>
		<?php echo $js;?>
    </head>
    <body class="<?php echo $resource_name;?>">
		<header id="header">
			<h1 id="logo"><a href="<?php echo AppResource::url_for_member(null);?>" title="6d online"><span>6d</span></a></h1>
			<aside>an online identity framework</aside>
			<nav>
			<?php
				$pages = Post::find_public_pages(AppResource::$member->id);
				$pages = $pages === null ? array() : $pages;
			?>
			<?php echo "page count = " .count($pages);?>
			<?php
				foreach($pages as $page):?>
				<a<?php echo count($request->path) > 0 && $page->name === $request->path[0] ? ' class="selected"' : null;?> href="<?php echo AppResource::url_for_member($page->name === "index" ? null : $page->name);?>">
						<?php echo $page->title;?>
				</a>
			<?php endforeach;?>			
				<a href="<?php echo AppResource::url_for_user("blog");?>" id="blog" title="<?php echo AppResource::$member->name;?>'s blog">Blog</a>
				<a href="<?php echo App::url_for("members");?>" title="Network directory">Members</a>
			</nav>
		</header>
		<section>
			<div id="user_message"<?php echo (App::get_user_message() === null ?  ' style="display:none;"' : null);?>><?php echo App::get_user_message();?></div>
			<?php echo $output;?>
		</section>
		<footer id="footer">
		<?php if(AuthController::is_authed()):?>
					<nav>
						<a href="<?php echo AppResource::url_for_user("post");?>">add a post</a>
						<a href="<?php echo AppResource::url_for_user("posts");?>">posts</a>
						<a href="<?php echo AppResource::url_for_user("addressbook");?>" title="Addressbook">addressbook</a>
						<a href="<?php echo AppResource::url_for_user("photos");?>" title="Add Photos">add photos</a>
						<a href="<?php echo App::url_for("signout");?>">sign out</a> <?php echo AuthController::$current_user->name;?>
					</nav>
		<?php endif;?>
			<address id="about" class="vcard body">
				<p class="primary">
					<span class="role">The web is the network. <a href="<?php echo App::url_for(null);?>" class="fn url">6d</a> makes it social.</span>
				</p>
				<p class="bio">6d is a web app that allows you to own your content. It gives you the ability to create a decentralized social network whith your website.</p>
			</address>
			<p>&copy;2011 <a href="http://www.get6d.com/" title="Get 6d website">Get 6d Incorporated</a>.</p>
			<p><small><?php echo round(memory_get_peak_usage(true) / 1024 / 1024, 2);?> megabytes of memory used to process this request</small></p>
		</footer>
    </body>
</html>