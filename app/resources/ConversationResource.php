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
		array_shift($this->url_parts);
		if(count($this->url_parts) > 0 && $post_id === null) $post_id = $this->url_parts[0];
		$view = 'comment/index';
		$this->post = Post::findById($post_id, Application::$member->person_id);
		if($this->post == null){
			$this->set_not_found();
			return null;
		}
		$requestor = Person::findByPublicKeyAndOwner($public_key, Application::$member->person_id);
		if(!$this->has_access($this->post, $public_key, $requestor)){
			$this->set_unauthorized();		
			return null;
		}
		$this->post->conversation = PostResource::get_conversation_for($this->post);
		$this->output = $this->render($view);
		return $this->render('layouts/default');
	}
	public function put(Comment $comment, $public_key){
		if($public_key !== null){
			$this->post = Post::findById($comment->post_id, Application::$member->person_id);
			$requestor = Person::findByPublicKeyAndOwner($public_key, Application::$member->person_id);
			if($this->post !== null && $this->has_access($this->post, $public_key, $requestor)){
				$comment->owner_id = Application::$member->person_id;
				$author = PostResource::getAuthor(new Post(array('source'=>$comment->source)));
				$comment->author = new Author(array('name'=>$requestor->name, 'source'=>$requestor->url, 'photo_url'=>$author->profile->photo_url));
				if($this->post->conversation === null){
					$this->post->conversation = array();
				}else{
					$this->post->conversation = json_decode($this->post->conversation);
				}
				$comment->id = count($this->post->conversation);
				$conversation = $this->post->conversation;
				array_unshift($conversation, $comment);
				$this->post->conversation = json_encode($conversation);
				$errors = array();
				list($this->post, $errors) = Post::save($this->post);
				if(count($errors) > 0){
					$message = array();
					foreach($errors as $key=>$value){
						$message[] = $key . ': ' . $value;
					}
					self::setUserMessage(implode(', ', $message));
				}else{
					$this->status = new HttpStatus(201);
				}
				$this->output = $this->render('comment/index');
				return $this->render('layouts/default');
			}
		}
	}
	private function has_access(Post $post, $public_key, $requestor){
		
		if($public_key !== null){
			if($requestor !== null){
				return true;
			}
		}
		
		if($this->post->is_published || (AuthController::is_authorized() && $this->post->owner_id == Application::$current_user->person_id)){
			return true;
		}
		return false;
	}
}