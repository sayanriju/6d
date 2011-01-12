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
			$this->set_not_found();
			return;
		}
		$comment->source = Application::$current_user->person->url;
		if($this->post->person_post_id !== null){
			$this->send_comment_to_author($this->post, $comment);
		}else{
			$comment->owner_id = Application::$current_user->person_id;
			$comment->author = new Author(array('name'=>Application::$current_user->person->name, 'source'=>Application::$current_user->person->url, 'photo_url'=>Application::$current_user->person->profile->photo_url));
			$comment->post_id = $this->post->id;
			$comment->created = date('c');
			if($this->post->conversation === null){
				$conversation = array();
			}else{
				$conversation = json_decode($this->post->conversation);
				array_shift($conversation);
			}
			array_unshift($conversation, $comment);
			list($comment, $errors) = Comment::save($comment);
			$this->post->conversation = json_encode($conversation);
			$this->post->updated = date('c');
			list($this->post, $errors) = Post::save($this->post);
			if(count($errors) > 0){
				$message = array();
				foreach($errors as $key=>$value){
					$message[] = $key . ': ' . $value;
				}
				error_log($message);
				self::set_user_message(implode('<br />', $message));
			}else{
				self::set_user_message('Comment was added.');
			}
		}
		$this->redirect_to(Application::$member->member_name . '/conversation/' . $this->post->id);
		return $this->post->id;
	}
	private function send_comment_to_author(Post $post, Comment $comment){
		$author = $post->get_author();
		$data = '_method=put&public_key=' . base64_encode($author->public_key) . '&post_id=' . $post->person_post_id . '&body=' . urlencode($comment->body) . '&name=' . urlencode($author->name) . '&date=' . urlencode($comment->date) . '&source=' . urlencode(Application::$current_user->person->url);
		error_log('commenting on ' . $author->name . "'s post " . $data);
		$responses = NotificationResource::sendMultiNotifications(array($author), 'conversation.json', array($data), 'post');
		return $responses;
	}
}