<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Post") || require("models/Post.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class StreamResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $post;
	public $legend;
	public $posts;
	public $total_pages;
	public $previous_page;
	public $next_page;
	
	public function get($page = 0){
		$page = (int)$page;
		if(!AuthController::is_authed() || AuthController::$current_user->id !== AppResource::$member->id){
			$this->set_unauthed();
			return;
		}
		$total = Post::find_total(AuthController::$current_user->id);
		$this->total_pages = (int)(ceil($total / 5));
		$this->next_page = $page+1;
		$this->previous_page = $page-1;
		if($this->previous_page < 0) $this->previous_page = 0;
		$start = $page * 5;
		$this->post = new Post(array("owner_id"=>(int)AuthController::$current_user->id));
		$this->posts = Post::find_owned_by(AuthController::$current_user->id, $start, 5);
		if($this->posts === null) $this->posts = array();
		$view = "stream/index";
		$this->output = View::render($view, $this);
		return View::render_layout('default', $this);
	}
}