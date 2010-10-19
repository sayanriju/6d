<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('NotificationResource') || require('NotificationResource.php');

class CommentsResource extends AppResource{
	public function __construct($attributes){
		parent::__construct($attributes);
	}
	public function __destruct(){}
	public $comments;
	public $post;
	public function get($post_id){
		$view = 'comment/index';
		$this->post = Post::findById($post_id, Application::$member->person_id);
		if($this->post == null){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		
		if(!$this->post->is_published && (!AuthController::isAuthorized() || $this->post->owner_id !== Application::$current_user->person_id)){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		$this->comments = $this->post->get_comments();
		$this->comments = $this->comments === null ? array() : $this->comments;
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');
	}
	public function post(Comment $comment){
		$this->post = Post::findById($comment->post_id, Application::$member->person_id);
		if($this->post == null){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		// TODO: Vadlidate input from user in comment->body;
		if($this->post->person_post_id !== null){
			// send this comment to the person's site.
		}
		$comment->owner_id = Application::$current_user->person_id;
		$comment->created = date('c');
		$comment->comment_date = date('c');
		
		$errors = Comment::canSave($comment);
		
		if(count($errors) === 0){
			list($comment, $errors) = Comment::save($comment);			
		}
		
		if(count($errors) > 0){
			var_dump($errors);
		}else{
			$this->redirectTo('post/' . $this->post->id);
		}
		
	}
}