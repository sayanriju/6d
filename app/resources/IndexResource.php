<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('Application') || require('Application.php');
class IndexResource extends AppResource{
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
	public function get(){
		$this->title = (Application::$member->person->profile !== null ? Application::$member->person->profile->site_name : Application::$member->name);
		$this->output = $this->render('index/index', null);
		return $this->render_layout('home', null);
	}
}

?>