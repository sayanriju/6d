<?php
	class_exists('UserResource') || require('resources/UserResource.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $title;?></title>
		<meta name="description" content="<?php echo $description;?>"/>
		<meta name="keywords" content="<?php echo $keywords;?>"/>
		<meta name="viewport" content="width=980"/>
		<link rel="stylesheet" type="text/css" href="<?php echo App::url_for_theme('css/default.css');?>" media="all" />
		<?php echo $resource_css;?>
	</head>
	<body>
		<?php echo Resource::get_user_message();?>			
		<?php echo $output;?>
	</body>
</html>