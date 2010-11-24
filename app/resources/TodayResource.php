<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('PostsResource') || require('PostsResource.php');
class TodayResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $posts;
	public $post;
	public $page;
	public $sort_by;
	public $sort_by_direction;
	public $limit;
	public static $cached_posts;
	public static function get_todays_count(){
		if(self::$cached_posts != null){
			return count(self::$cached_posts);
		}else{
			self::$cached_posts = self::get_todays_posts(AuthController::is_authorized(), Application::$member->person_id, null, null, null, null, null, null, 0);
			return count(self::$cached_posts);
		}
	}
	public function get(){
		$view = 'today/index';
		$this->posts = self::get_todays_posts(AuthController::is_authorized(), Application::$member->person_id, 10);
		$this->output = $this->render($view, null);
		$this->keywords = "today, info stream, activity stream";
		$this->description = "List of Today's activities";
		$this->title = "Today's Activities";
		self::$cached_posts = $this->posts;
		return $this->render('layouts/default', null);
	}
	public static function get_todays_posts($is_authed, $person_id, $limit){
		$posts = null;
		$posts = Post::findTodaysPosts(0, $limit, 'updated', 'desc', $is_authed, $person_id);
		for($i=0; $i<count($posts); $i++){
			$posts[$i]->conversation = PostResource::get_conversation_for($posts[$i]);
		}
		return $posts;
	}
}

?>