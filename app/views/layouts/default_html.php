<?php class_exists("AuthController") || require("controllers/AuthController.php");?><!DOCTYPE html>
<!--
	 ____     __                          __          ___   ___                 ___        __             
	/\  _`\  /\ \      __                /\ \      __/\_ \ /\_ \               /\_ \    __/\ \__          
	\ \ \/\_\\ \ \___ /\_\    ___     ___\ \ \___ /\_\//\ \\//\ \      __      \//\ \  /\_\ \ ,_\    __   
	 \ \ \/_/_\ \  _ `\/\ \ /' _ `\  /'___\ \  _ `\/\ \\ \ \ \ \ \   /'__`\      \ \ \ \/\ \ \ \/  /'__`\ 
	  \ \ \L\ \\ \ \ \ \ \ \/\ \/\ \/\ \__/\ \ \ \ \ \ \\_\ \_\_\ \_/\ \L\.\_     \_\ \_\ \ \ \ \_/\  __/ 
	   \ \____/ \ \_\ \_\ \_\ \_\ \_\ \____\\ \_\ \_\ \_\\____\\____\ \__/.\_\    /\____\\ \_\ \__\ \____\
	    \/___/   \/_/\/_/\/_/\/_/\/_/\/____/ \/_/\/_/\/_//____//____/\/__/\/_/    \/____/ \/_/\/__/\/____/
		everything should be this easy!
-->
<html>
	<head>
        <title>Chinchilla: <?php echo $title;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="author" content="Joey Guerra" />
		<meta name="keywords" content="restful framework, php, design patterns, oo principles" />
		<meta name="description" content="Chinchilla is a RESTful framework written in PHP." />		
		<link rel="icon" type="image/png" href="<?php echo App::url_for("favicon.png");?>" />	
		<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme("css/default.css");?>" />
		<?php echo $css;?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo App::url_for("js/default.js");?>"></script>
		<?php echo $js;?>
    </head>
    <body class="<?php echo $resource_name;?>">
		<header id="header">
			<h1 id="logo"><a href="<?php echo AppResource::url_for_member(null);?>" title="Go to the Chinchilla home page"><span>Chinchilla</span></a></h1>
			<aside>a RESTful framework</aside>
			<nav>
			<?php if($resource_name !== 'example'):?>
				<a href="<?php echo App::url_for('example.html');?>" title="example on how to use the Chinchilla framework to return HTML">example.html</a>
			<?php else:?>
				<span>example</span>
			<?php endif;?>
				<a href="<?php echo App::url_for('example.xml');?>" title="example on how to use the Chinchilla framework to return XML">example.xml</a>
				<a href="<?php echo App::url_for('example.json');?>" title="example on how to use the Chinchilla framework to return JSON">example.json</a>
				<a href="<?php echo App::url_for('example.json');?>" id="ajax_link" title="example on how to use the Chinchilla framework to return JSON for an AJAX request">ajax example.json</a>
				<a href="<?php echo AppResource::url_for_user("blog");?>" id="blog" title="<?php echo AppResource::$member->is_owner ? "Chinchillalite blog" : AppResource::$member->name;?>'s blog">blog</a>
				<a href="<?php echo App::url_for("members");?>" title="Network directory">members</a>
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
				<a href="<?php echo AppResource::url_for_user("files");?>" title="Upload files">upload files</a>
				<a href="<?php echo AppResource::url_for_user("photos");?>" title="Photos">photos</a>
				<a href="<?php echo App::url_for("signout");?>">sign out</a> <?php echo AuthController::$current_user->name;?>
			</nav>
<?php endif;?>
			<p><small>Chinchilla</small>: <?php echo round(memory_get_peak_usage(true) / 1024 / 1024, 2);?> megabytes of memory</p>
		</footer>
    </body>
	<script type="text/javascript">
		var user_message = chin.get_element('user_message');
		function on_return(request){
			var response = chin.to_json(request.responseText);
			user_message.innerHTML = decodeURIComponent(response.message.replace(/\+/gi, ' '));
		}
		function box_clicked(e){
			chin.hide(user_message);
			chin.stop_observing(user_message, 'click', box_clicked);
		}
		function clicked(e){
			chin.stop(e);
			user_message.style.cursor = 'default';
			if(chin.is_hidden(user_message)){
				var request = new chin.ajax({method: 'get', DONE: [window, on_return]});
				request.send(e.target.href);
				chin.show(user_message);
				chin.observe(user_message, 'click', box_clicked);
			}else{
				chin.stop_observing(user_messge, 'click', box_clicked);
				chin.hide(user_message);
			}
		}
		
		chin.main = function(){
			var link = chin.get_element('ajax_link');
			var user_message = chin.get_element('user_message');
			chin.observe(link, 'click', clicked);
		}

		chin.observe(window, 'load', chin.main);
		
	</script>
</html>