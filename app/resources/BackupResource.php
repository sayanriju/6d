<?php
class_exists('UserResource') || require('UserResource.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class BackupResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $posts;
	public $people;
	public function get(){
		if(! AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		if(count($this->url_parts) > 1){
			if($this->url_parts[1] === 'restore'){
				return $this->getRestore();
			}
		}
		$this->title = "Your posts";
		$this->posts = Post::findAll();
		$text = serialize($this->posts);
		file_put_contents('posts.serialized', $text);
		$this->people = Person::findAll();		
		$text = serialize($this->people);
		file_put_contents('people.serialized', $text);
		
		$this->output = $this->renderView('post/index', null);
		return $this->renderView('layouts/default', null);
		
	}
	
	public function getRestore(){
		$this->title = "Your posts";
		$text = file_get_contents('posts.serialized');
		$this->posts = unserialize($text);
		$text = file_get_contents('people.serialized');
		$this->people = unserialize($text);
		foreach($this->posts as $post){
			$post->id = null;
			$post->owner_id = 1;
			if(strlen($post->created) === 0){
				$post->created = date('c');
			}
			list($post, $errors) = Post::save($post);
		}
		foreach($this->people as $person){
			$person->id = null;
			$person->owner_id = 1;
			$person = Person::save($person);
		}
		
		$this->output = $this->renderView('post/index', null);
		return $this->renderView('layouts/default', null);
	}

}

?>