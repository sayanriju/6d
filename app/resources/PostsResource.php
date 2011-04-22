<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Post") || require("models/Post.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class PostsResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $post;
	public $legend;
	public $posts;
	
	public function get(){
		if(!AuthController::is_authed() || AuthController::$current_user->id !== AppResource::$member->id){
			$this->set_unauthed();
			return;
		}
		$this->post = new Post(array("owner_id"=>(int)AuthController::$current_user->id));
		$this->posts = Post::find_owned_by(AuthController::$current_user->id, 0, 5);
		if($this->posts === null) $this->posts = array();
		$view = "post/index";
		$this->output = View::render($view, $this);
		return View::render_layout('default', $this);
	}
	public function post(Post $post){
		if(!AuthController::is_authed()){
			$this->set_unauthed();
			return;
		}
		//TODO: Need to create a table that just stores post titles and member name, perhaps a hash, so we can
		// check for duplicate post titles.
		$this->post = new Post(array("id"=>0
			, "title"=>$post->title
			, "body"=>$post->body
			, "status"=>$post->status
			, "owner_id"=>AuthController::$current_user->id
		));
		save_object::execute($this->post);
		$this->set_redirect_to(AuthController::$current_user->name . '/posts');
		$this->output = View::render('blog/show', $this);
		return View::render_layout('default', $this);
	}
}