<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('PostResource') || require('PostResource.php');
class ConversationResource extends AppResource{
	public function __construct($attributes){
		parent::__construct($attributes);
	}
	public function __destruct(){}
	public $post;
	public function get($post_id, $public_key = null){
		$view = 'comment/index';
		$this->post = Post::findById($post_id, Application::$member->person_id);
		if($this->post == null){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		if(!$this->has_access($this->post, $public_key)){			
			throw new Exception(FrontController::UNAUTHORIZED, 401);			
		}				
		$this->post->conversation = PostResource::get_conversation_for($this->post);
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');
	}
	
	private function has_access(Post $post, $public_key){
		
		if($public_key !== null){
			$requestor = Person::findByPublicKeyAndOwner($public_key, Application::$member->person_id);
			if($requestor !== null){
				return true;
			}
		}
		
		if($this->post->is_published || (AuthController::isAuthorized() && $this->post->owner_id == Application::$current_user->person_id)){
			return true;
		}
		return false;
	}
}