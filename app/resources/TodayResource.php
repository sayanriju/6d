<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('LoginResource') || require('LoginResource.php');
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
			self::$cached_posts = self::get_todays_posts(AuthController::isAuthorized(), Application::$member->person_id, null, null, null, null, null, null, 0);
			return count(self::$cached_posts);
		}
	}
	public function get($id = 1, $sort_by = 'post_date', $sort_by_direction = 'desc', $direction = null, $tag = null, $q = null){
		$this->limit = 0;
		$this->q = $q;
		$page = $id;
		if($sort_by === null || strlen($sort_by) === 0){
			$sort_by = 'post_date';
		}
		
		if($sort_by_direction === null || strlen($sort_by_direction) === 0){
			$sort_by_direction = 'desc';
		}
		
		if($page === null || strlen($page) === 0){
			$page = 1;
		}

		$this->sort_by = $sort_by;
		$this->page = $page;
		$this->sort_by_direction = $sort_by_direction;
		if($direction !== null){
			switch($direction){
				case('Previous'):
					$this->page++;
					break;
				case('Back to Top'):
					$this->page--;
					break;
				default:
					$this->page = $page;
					break;
			}
		}

		$view = 'today/index';
		if($this->page < 1){
			$this->page = 1;
		}
		$this->posts = self::get_todays_posts(AuthController::isAuthorized(), Application::$member->person_id, $id, $sort_by, $sort_by_direction, $direction, $tag, $q, $this->limit);
		$this->output = $this->renderView($view, null);
		$this->keywords = implode(', ', String::getKeyWordsFromContent($this->output));
		foreach($this->posts as $post){
			$this->description .= $post->title . ',';
		}
		$this->title = "Today's Stream";
		self::$cached_posts = $this->posts;
		return $this->renderView('layouts/default', null);
	}
	public static function get_todays_posts($is_authed, $person_id, $page, $sort_by, $sort_by_direction, $direction, $tag, $q, $limit){
		$posts = null;
		$posts = Post::findTodaysPosts($page, $limit, $sort_by, $sort_by_direction, $is_authed, $person_id);
		return $posts;
		
		if($tag !== null){
			$posts = Post::findPublishedByTag(new Tag(array('text'=>$tag)), ($page-1) * $limit, $limit, $sort_by, $sort_by_direction, $person_id);
			return $posts;
		}
		
		if($q !== null){
			if($is_authed){
				$posts = Post::search($q, $page, $limit, $sort_by, $sort_by_direction, $person_id);
			}else{
				$posts = Post::searchForPublished($q, $page, $limit, $sort_by, $sort_by_direction, $person_id);
			}
			return $posts;
		}
		
		if($posts === null){
			$posts = Post::findPublishedPosts(($page-1) * $limit, $limit, $sort_by, $sort_by_direction, $person_id);
			return $posts;
		}
		return $posts;
	}
}

?>