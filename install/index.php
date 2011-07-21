<?php require("../app/app.php");?>
<?php require("../app/models/Comment.php");?>
<?php require("../app/models/Contact.php");?>
<?php require("../app/models/Member.php");?>
<?php require("../app/models/Post.php");?>
<?php require("../app/models/Tag.php");?>
<?php require("../app/models/Setting.php");?>
<?php require("../settings.php");?>
<?php 
	$method = $_SERVER["REQUEST_METHOD"];
?><!doctype html>
<html>
	<head>
		<title>You need to install 6d</title>
	</head>
	<body>
<?php if($method === "GET"):?>
<?php
$path = Settings::virtual_path("data/6d.db");
if(!file_exists($path)){
	file_put_contents($path, null);
}else{
	$db = new PDO("sqlite:" . $path);
	$query = "select * from members where ROWID = 1";
	$cmd = $db->prepare($query);
	if($cmd === null){
		$header = new HttpHeader(array("location"=>str_replace("install", "", App::url_for(null)), "file_type"=>"html"));
		$header->send();
		die;
	}
}
?>
		<h1>Installation</h1>
		<p>You have to enter a signin name and passowrd to get started. Please do so below.</p>
		<p>You'll be ready to go after this.</p>
		<form action="" method="post">
			<input type="text" name="signin" required />
			<input type="password" name="password" required />
			<button type="submit">Go</button>
		</form>
<?php endif;?>

<?php

if($method === "POST"){
	$signin = $_POST["signin"];
	$password = $_POST["password"];
	Setting::install();
	Comment::install();
	Contact::install();
	Member::install();
	Member_meta::install();
	Post::install();
	Post_meta::install();
	Tag::install();
	$member = new Member(array("signin"=>$signin, "password"=>String::encrypt($password), "in_directory"=>1, "name"=>$signin, "is_owner"=>1, "expiry"=>time()+60, "display_name"=>$signin));
	Member::save($member);
	$header = new HttpHeader(array("location"=>str_replace("install", "", App::url_for(null)), "file_type"=>"html"));
	$header->send();
	die;
}
?>
	</body>
</html>