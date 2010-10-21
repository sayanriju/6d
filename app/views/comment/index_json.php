<?php class_exists('Date') || require('lib/Date.php');?>
<?php class_exists('PostResource') || require('resources/PostResource.php');?>
<?php echo json_encode($post->conversation);?>