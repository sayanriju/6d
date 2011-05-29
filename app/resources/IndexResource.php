<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("BlogResource") || require("BlogResource.php");
class_exists("Post") || require("models/Post.php");
class_exists("Setting") || require("models/Setting.php");
class IndexResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $posts;
	public $post;
	public function get(){
		$view = "index/index";
		$this->post = Post::find_public_page("index", self::$member->id);
		if($this->post !== null){
			$this->title = $this->post->title;
		}else{
			$setting = Setting::find("home_page_title", self::$member->id);
			if($setting !== null && $setting->value !== null){
				$this->title = $setting->value;
			}else{
				$this->title = "Another 6d site.";
			}
			$blog_resource = new BlogResource();
			$blog_resource->request = $this->request;
			return $blog_resource->get();
		}
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
}