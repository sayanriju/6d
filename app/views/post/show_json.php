{
	"id":"<?php echo $post->id;?>"
	, "title":"<?php echo urlencode($post->title);?>"
	, "body":"<?php echo urlencode($post->body);?>"
	, "type":"<?php echo $post->type;?>"
	, "description":"<?php echo urlencode($post->description);?>"
	, "post_date":"<?php echo $post->post_date;?>"
	, "tags":"<?php echo implode(',', $post->tags);?>"
}