<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Post") || require("models/Post.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class BlogResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $posts;
	public $post;
	public $page;
	public $next_page;
	public $previous_page;
	public $total_pages;
	public $post_count;
	public $limit;
	public function get($id = null, $page = 1){
		$view = "blog/index";
		$this->page = (int)$page;
		$this->page--;
		$this->limit = 3;
		if($id !== null){
			$this->post = find_one_by::execute("ROWID=:id", new Post(array("id"=>(int)$id)));
			$view = "post/show";
			$this->title = $this->post->title;
		}else{
			$this->posts = find_by_with_limit::execute("status='public' and owner_id=:owner_id"
				, new Post(array("owner_id"=>self::$member->id))
				, $this->page * 3, $this->limit);
			if(count($this->posts) === 0){
				$this->set_not_found();
				return;
			}
			$this->next_page = $this->page+2;
			$this->post_count = find_count_by::execute("status='public' and owner_id=:owner_id", new Post(array("owner_id"=>self::$member->id)));
			$this->total_pages = ceil($this->post_count->total / 3);
			$this->previous_page = $this->next_page-2;
			if($this->next_page > $this->total_pages){
				$this->next_page = $this->total_pages;
			}
			if($this->previous_page < 0){
				$this->previous_page = 0;
			}
			$this->title = !self::$member->is_owner ? self::$member->name . "'s Blog" : "Blog";
		}
		
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}	
}
