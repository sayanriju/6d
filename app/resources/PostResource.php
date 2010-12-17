<?php
class_exists('LoginResource') || require('LoginResource.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('PhotoResource') || require('PhotoResource.php');
class_exists('ProfileResource') || require('ProfileResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('Photo') || require('models/Photo.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('Person') || require('models/Person.php');
class TagValidator{
	public function observe_for_key_path($key, $obj, $val){
		if($val !== null){
			$val = String::explodeAndTrim($val);
		}
		return $val;
	}
}
class DateTranslator{
	public function observe_for_key_path($key, $obj, $val){
		switch($val){
			case('today'):
				$val = date('c');
				break;
			case('tomorrow'):
				$val = date('c', strtotime('+1 day'));
				break;
			case('yesterday'):
				$val = date('c', strtotime('-1 day'));
				break;
			case('next week'):
				$val = date('c', strtotime('+1 week'));
				break;
		}		
		return $val;
	}
}

class CustomUrlCreator{
	public function observe_for_key_path($key, $obj, $val){
		if($val !== 'status'){
			$obj->custom_url = String::stringForUrl($obj->title);
		}
		return $val;
	}
}
class PasswordValidator{
	public function observe_for_key_path($key, $obj, $val){
		if(strlen($val) === 0){
			$val = null;
		}
		return $val;
	}
}
class PostProxy{
	public function post_has_been_submitted($sender, $post){
		if(!AuthController::is_authorized()){
			$sender->set_unauthorized();
			return;
		}
		if($post == null){
			$sender->set_not_found();
			return;
		}
		if($post->owner_id !== Application::$current_user->person_id && !AuthController::is_super_admin()){
			$sender->set_unauthorized();
			return;
		}
	}
}
class PostResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		$this->max_filesize = 2000000;
		$this->post = new Post();
		Post::add_observer(new TagValidator(), 'observe_for_key_path', 'tags');
		Post::add_observer(new DateTranslator(), 'observe_for_key_path', 'post_date');		
		Post::add_observer(new CustomUrlCreator(), 'observe_for_key_path', 'type');		
		Post::add_observer(new PasswordValidator(), 'observe_for_key_path', 'password');		
	}
	public function __destruct(){
		parent::__destruct();
	}
	private $notificationResource;
	public $posts;
	public $post;
	public $max_filesize;
	public $page;
	public $photos;
	public $people;
	public function get($id){
		$photo = new Photo();
		$this->photos = $photo->findAll('media/' . Application::$member->member_name);
		$view = 'post/show';
		if( AuthController::is_authorized()){
			$view = 'post/edit';
		}
		$this->post = Post::findById($id, Application::$member->person_id);
		if($id !== null && $this->post == null){
			$this->set_not_found();
			return;
		}
		if($this->post != null && strlen($this->post->id) > 0){
			$this->title = $this->post->title;
			$this->description = $this->post->description;	
			$this->post->conversation = self::get_conversation_for($this->post);
			$this->output = $this->render($view, null);
			return $this->render_layout('default', null);
		}else{
			if(!AuthController::is_authorized()){
				$this->set_unauthorized();
				return;
			}
			$this->post = new Post();
			$this->title = "New post";
			$this->output = $this->render($view, null);
			return $this->render_layout('default', null);
		}
	}
	public static function get_conversation_for(Post $post){
		if($post->person_post_id !== null){
			$author = $post->get_author();
			$response = Request::doRequest($author->url, 'conversation.json', 'public_key=' . $author->public_key . '&post_id=' . $post->person_post_id, 'get', null);
			$response = json_decode($response->output);
			// $response is an array when it's decoded successfully.
			if($response == null) return null;
			if(!is_array($response)) return null;
			if(!property_exists($response, 'message')) return null;
			return $response;
		}else{
			if($post->conversation !== null){
				return json_decode($post->conversation);
			}else{
				return array();
			}
		}
	}
	public static function getAuthor(Post $post){
		if($post->source !== null && strlen($post->source) > 0){
			$person = Person::findByUrlAndOwnerId($post->source, Application::$member->person_id);
			if($person === null){
				$person = Person::findById($post->owner_id);
				$person->setProfile(unserialize($person->profile));
			}else{
				$data = sprintf("public_key=%s", urlencode($person->public_key));
				$response = NotificationResource::sendNotification($person, 'profile.json', $data, 'get');
				$response = json_decode($response->output);
				$person->profile = strlen($person->profile > 0) ? unserialize($person->profile) : new Profile();
				$person->profile->photo_url = $response->person->photo_url;				
			}
		}else{
			$person = Application::$member;
		}
		return $person;
	}
	public static function getAuthorUrl(Post $post){
		$url = null;
		if($post->source !== null && strlen($post->source) > 0){
			$person = Person::findByUrlAndOwnerId($post->source, Application::$member->person_id);
			if($person !== null){
				$data = sprintf("public_key=%s", urlencode($person->public_key));
				$response = NotificationResource::sendNotification($person, 'profile.json', $data, 'get');
				$response = json_decode($response->output);
				$url = $response->person->photo_url;
			}else{
				$url = Application::$member->profile->photo_url;
			}
		}else{			
			$person = Person::findById($post->owner_id);
			$person->profile = unserialize($person->profile);
			if($person->profile->photo_url !== null && strlen($person->profile->photo_url) > 0){
				$url = ProfileResource::getPhotoUrl($person);
			}
		}
		$url = ($url === null ? App::url_for('images/nophoto.png') : $url);
		return $url;
	}
	public function put(Post $post, $people = array(), $groups = array(), $make_home_page = false, $public_key = null, $photo_names = array(), $previous_url = null){
		$post->owner_id = Application::$current_user->person_id;
		if($post->id !== null && strlen($post->id) > 0){
			$this->post = Post::findById($post->id, Application::$current_user->person_id);
		}
		self::add_observer(new PostProxy(), 'post_has_been_submitted', $this);
		self::notify('post_has_been_submitted', $this, $post);
		list($post, $errors) = Post::save($post);
		if($errors == null){
			if($make_home_page){
				$setting = Setting::findByName('home_page_post_id');
				$setting->value = $post->id;
				$setting->owner_id = Application::$current_user->person_id;
				Setting::save($setting);
			}else if($post->isHomePage($this->getHome_page_post_id())){
				Setting::delete('home_page_post_id');
			}
			self::setUserMessage('Post was saved.');
			$this->sendPostToGroups($groups, $post);					
			$this->sendPostToPeople($people, $post);
		}else{
			$message = 'An error occurred while saving your post:';
			foreach($errors as $key=>$value){
				$message .= "$key=$value";
			}
			self::setUserMessage($message);
		}
		$this->redirect_to(Application::url_with_member('blog/'. $this->post->custom_url));
	}
	
	public function delete(Post $post, $q = null){
		$this->q = $q;
		if(!AuthController::is_authorized()){
			$this->set_unauthorized();
			return;
		}
		$post = Post::findById($post->id, Application::$current_user->person_id);
		if($post->owner_id !== Application::$current_user->person_id && !AuthController::is_super_admin()){
			$this->set_unauthorized();
			return;
		}
		
		Post::delete($post);
		self::setUserMessage(sprintf("'%s' was deleted.", $post->title));
		if($this->q === null){
			$this->redirect_to('posts');
		}else{
			$this->redirect_to('posts', array('q'=>$this->q));
		}
	}
	
	private function sendPostToGroups($groups, Post $post){
		if(count($groups) > 0){
			foreach($groups as $text){
				$text = urldecode($text);
				if($text === 'All Contacts'){
					$this->people = Person::findAllByOwner(Application::$current_user->person_id);
				}else{
					$this->people = Person::findByTagTextAndOwner(urlencode($text), Application::$current_user->person_id);
				}
				$this->sendToPeople($this->people, $post);
			}
		}
	}
	private function sendPostToPeople($people, Post $post){
		if(count($people) > 0){
			$people = Person::findByIds($people, Application::$current_user->person_id);
			if($people !== null && count($people) > 0){
				$this->sendToPeople($people, $post);
			}
		}		
	}
	private function sendToPeople($people, $post){
		$datum = array();
		$responses = array();
		$to = array();
		foreach($people as $person){
			error_log($person->name . ' ' . $person->public_key);
			if($person->id != Application::$current_user->person_id && $person->is_approved && $person->public_key !== null){
				error_log(sprintf("sendToPeople -> person= %s, current user = %s",$person->name, Application::$current_user->name));
				$datum[] = sprintf("person_post_id=%s&title=%s&body=%s&source=%s&is_published=%s&post_date=%s&public_key=%s&type=%s", urlencode($post->id), urlencode($post->title), urlencode($post->body), urlencode($post->source), $post->is_published, urlencode($post->post_date), urlencode($person->public_key), $post->type);
				$to[] = $person;
				error_log($datum[count($datum)-1]);
			}else{
				error_log("failed trying to send to " . $person->name);
			}
		}
		if(count($datum) > 0){
			$responses = NotificationResource::sendMultiNotifications($to, 'posts', $datum, 'post');
			if(count($responses) > 0){
				$message = array();
				foreach($responses as $key=>$response){
					$person = $to[$key];
					Resource::setUserMessage($person->name . ' responded with ' . $response);
				}
			}
		}else{
			Resource::setUserMessage("Could not send to anybody you picked because none of them have been confirmed as friends.");
		}
		error_log(Resource::get_user_message());
	}
	
}