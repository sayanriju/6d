<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('NotificationResource') || require('NotificationResource.php');

class ConversationsResource extends AppResource{
	public function __construct($attributes){
		parent::__construct($attributes);
	}
	public function __destruct(){}
	public $post;
	public function post(Comment $comment){
		$this->post = Post::findById($comment->post_id, Application::$current_user->person_id);
		if($this->post == null){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		$comment->owner_id = Application::$current_user->person_id;
		$comment->author = new Author(array('name'=>Application::$current_user->name, 'source'=>Application::$current_user->url, 'photo_url'=>Application::$current_user->profile->photo_url));
		$comment->date = date('c');
		if($this->post->conversation === null){
			$this->post->conversation = array();
		}else{
			$this->post->conversation = json_decode($this->post->conversation);
		}
		$comment->id = count($this->post->conversation);
		$conversation = $this->post->conversation;
		array_unshift($conversation, $comment);
		$this->post->conversation = json_encode($conversation);
		list($this->post, $errors) = Post::save($this->post);
		if(count($errors) > 0){
			$message = array();
			foreach($errors as $key=>$value){
				$message[] = $key . ': ' . $value;
			}
			self::setUserMessage(implode('<br />', $message));
		}
		$this->redirectTo(Application::$member->member_name . '/conversation', array('post_id'=>$this->post->id));
	}
}