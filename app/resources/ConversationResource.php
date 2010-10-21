<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('NotificationResource') || require('NotificationResource.php');

class ConversationResource extends AppResource{
	public function __construct($attributes){
		parent::__construct($attributes);
	}
	public function __destruct(){}
	public $post;
	public function get($post_id){
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		$view = 'comment/index';
		$this->post = Post::findById($post_id, Application::$member->person_id);
		if($this->post == null){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		
		if(!$this->post->is_published && (!AuthController::isAuthorized() || $this->post->owner_id !== Application::$current_user->person_id)){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		if($this->post->conversation === null){
			$this->post->conversation = array();
		}else{
			$this->post->conversation = json_decode($this->post->conversation);
		}
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');
	}
}