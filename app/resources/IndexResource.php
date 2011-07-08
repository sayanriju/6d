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
			$setting = Setting::find("title", self::$member->id);
			$use_blog_for_home_page = Setting::find("use_blog_for_home_page", self::$member->id);
			if($setting !== null && $setting->value !== null){
				$this->title = $setting->value;
			}else{
				$this->title = "Another 6d site.";
			}
			if($use_blog_for_home_page != null && $use_blog_for_home_page->value){
				$blog_resource = new BlogResource();
				$blog_resource->title = $this->title;
				$blog_resource->request = $this->request;
				return $blog_resource->get();
			}
		}
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
}