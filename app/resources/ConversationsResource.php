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
		$errors = array();
		$this->post = Post::findById($comment->post_id, Application::$current_user->person_id);
		if($this->post == null){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		if($this->post->person_post_id !== null){
			$this->send_comment_to_author($this->post, $comment);
		}else{
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
		}
		$this->post->updated = date('c');
		list($this->post, $errors) = Post::save($this->post);
		if(count($errors) > 0){
			$message = array();
			foreach($errors as $key=>$value){
				$message[] = $key . ': ' . $value;
			}
			error_log($message);
			self::setUserMessage(implode('<br />', $message));
		}
		$this->redirectTo(Application::$member->member_name . '/conversation', array('post_id'=>$this->post->id));
	}
	private function send_comment_to_author(Post $post, Comment $comment){
		$author = $post->get_author();
		$data = '_method=put&public_key=' . $author->public_key . '&post_id=' . $post->person_post_id . '&body=' . $comment->body . '&name=' . $author->name . '&date=' . $comment->date . '&source=' . Application::$current_user->url;
		$responses = NotificationResource::sendMultiNotifications(array($author), 'conversation.json', array($data), 'post');
		var_dump($responses);
		return $responses;
	}
}