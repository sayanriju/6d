<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('PostResource') || require('PostResource.php');
class BlogResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		$this->total = 0;
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
	public $start;
	public $total;
	public function get($page_or_title = null){
		$this->limit = 5;
		$view = 'index/index';
		$this->sort_by = 'id';
		$this->sort_by_direction = 'desc';
		if(is_numeric($page_or_title)){
			$this->page =  $page_or_title;
			if($this->page <= 0){
				$this->page = 1;
			}
			$this->start = ($this->page-1) * $this->limit;
			$this->title = 'Page ' . $this->page . ' Posts';
			$this->description = 'This is page ' . $this->page . ' of a list of Blog posts on ' . Application::$member->person->profile->site_name;
			$this->posts = Post::findPublished($this->start, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$member->person_id);
			$this->total = Post::get_total_published(Application::$member->person_id);
		}else{
			$this->page = 0;
			$this->post = Post::findAllPublished($page_or_title, Application::$member->person_id);
			$this->title = ($this->post !== null ? $this->post->title : "Post not found");
		}
		if($this->post !== null){
			$this->description = Post::get_excerpt($this->post, false);
			$this->post->conversation = PostResource::get_conversation_for($this->post);
			$view = 'post/show';
			$this->display_date = strtotime($this->post->post_date);
		}else{
			if(count($this->posts) === 0){
				$this->set_not_found();
				return;
			}
			for($i=0; $i<count($this->posts); $i++){
				$this->posts[$i]->conversation = PostResource::get_conversation_for($this->posts[$i]);
			}
		}
		$this->keywords = implode(', ', String::getKeyWordsFromContent($this->output));
		$this->output = $this->render($view);
		return $this->render_layout('default');
	}	
}