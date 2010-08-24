<?php
class_exists('LoginResource') || require('LoginResource.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('PhotoResource') || require('PhotoResource.php');
class_exists('ProfileResource') || require('ProfileResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('Photo') || require('models/Photo.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('Person') || require('models/Person.php');
class PostResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		$this->max_filesize = 2000000;
		$this->post = new Post();
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
	public $last_page_viewed;
	public function get(Post $post = null, $layout = 'default', $last_page_viewed = 1){
		$this->last_page_viewed = $last_page_viewed;
		$photo = new Photo();
		$this->photos = $photo->findAll();
		$view = 'post/show';
		$layout = 'layouts/' . $layout;
		if( AuthController::isAuthorized()){
			$view = 'post/edit';
		}
		if(count($this->url_parts) > 1){
			$this->post = Post::findById($this->url_parts[1], $this->current_user->id);
		}else{
			$this->post = $post;
		}
		if($this->post != null && strlen($this->post->id) > 0){
			$this->title = $this->post->title;
			$this->description = $this->post->description;
			$this->output = $this->renderView($view, null);
			return $this->renderView($layout, null);
		}else{
			if(! AuthController::isAuthorized()){
				throw new Exception(FrontController::UNAUTHORIZED, 401);
			}
			$this->post = new Post();
			$this->title = "New post";
			$this->output = $this->renderView($view, null);
			return $this->renderView($layout, null);
		}
	}
	public static function getAuthorUrl(Post $post){
		$url = null;
		if($post->source !== null && strlen($post->source) > 0){
			$person = Person::findByUrlAndOwnerId($post->source, Application::$member->person_id);
			if($person !== null){
				$data = sprintf("public_key=%s", urlencode($person->public_key));
				$response = NotificationResource::sendNotification($person, 'profile.json', $data, 'get');
				$response = json_decode($response);
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
		$url = ($url === null ? FrontController::urlFor('images') . 'nophoto.png' : $url);
		return $url;
	}
	public function put(Post $post, $people = array(), $groups = array(), $make_home_page = false, $public_key = null, $photo_names = array(), $last_page_viewed = 1){
		
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		
		$this->last_page_viewed = $last_page_viewed;
		
		if($post->id !== null && strlen($post->id) > 0){
			$this->post = Post::findById($post->id, $this->current_user->id);
		}
		
		if($this->post->owner_id !== $this->current_user->id && !AuthController::isSuperAdmin()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		
		if($this->post !== null){
			switch($post->post_date){
				case('today'):
					$post->post_date = date('c');
					break;
				case('tomorrow'):
					$post->post_date = date('c', strtotime('+1 day'));
					break;
				case('yesterday'):
					$post->post_date = date('c', strtotime('-1 day'));
					break;
				case('next week'):
					$post->post_date = date('c', strtotime('+1 week'));
					break;
			}
			$post->owner_id = $this->current_user->id;
			if($post->type !== 'status'){
				$post->custom_url = String::stringForUrl($post->title);
			}
			if(strlen($post->password) === 0){
				$post->password = null;
			}
			console::log('password = ' . $post->password);
			list($post, $errors) = Post::save($post);
			if($errors == null){
				if($make_home_page){
					$setting = Setting::findByName('home_page_post_id');
					$setting->value = $post->id;
					$setting->owner_id = $this->current_user->id;
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
		}else{
			self::setUserMessage("That post doesn't exist.");
		}
		$this->redirectTo('posts/' . $this->last_page_viewed);
		
			
	}
	private function save(Post $post, $people, $groups, $make_home_page){
		$post->created = date('c');
		if(strlen($post->post_date) === 0){
			$post->post_date = date('c');
		}
		$post->owner_id = $this->current_user->id;
		if(strlen($post->body) > 0){
			if($post->type !== 'status'){
				$post->custom_url = String::stringForUrl($post->title);
			}
			list($post, $errors) = Post::save($post);
			$post->person_post_id = $post->id;
			if($errors == null){
				if($make_home_page){
					$this->makeHomePage($post);
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
		}
		$this->redirectTo('posts');
		
	}
	private function makeHomePage($post){
		$setting = Setting::findByName('home_page_post_id');
		$setting->value = $post->id;
		$setting->owner_id = $this->current_user->id;
		Setting::save($setting);
	}
	
	public function post(Post $post, $people = array(), $groups = array(), $make_home_page = false, $public_key = null, $photo_names = array()){
		$errors = array();
		if(AuthController::isAuthorized()){
			$post->source = $this->current_user->url;
			error_log('saving the post and the source is ' . $post->source . ' for member ' . $this->site_member->member_name);
			$this->save($post, $people, $groups, $make_home_page);
		}else if($public_key != null && strlen($public_key)>0){
			$person = Person::findByPublicKeyAndUrl($public_key, $post->source);
			$response = 'ok';
			if($person != null && $person->is_approved){
				// This block of code gets an existing post and updates that.
				$existing_post = Post::findByPersonPostId($post->person_post_id, $this->site_member->person_id);
				if($existing_post != null){
					$post->id = $existing_post->id;
					$post->is_published = $existing_post->is_published;
				}else{
					$post->is_published = false;
					error_log('came from ' . $person->url);
					$post->source = $person->url;
					$post->id = null;
				}
				$post->created = date('c');
				if($post->post_date === null || strlen($post->post_date) === 0 || $post->post_date === 'today'){
					$post->post_date = date('c');
				}
				$post->body = $this->filterText($post->body);
				$post->title = $this->filterText($post->title);
				$post->body = urldecode($post->body);
				$post->owner_id = $this->site_member->person_id;
				if($post->type !== Post::$status){
					$post->custom_url = String::stringForUrl($post->title);
				}
				list($post, $errors) = Post::save($post);
				if(count($errors) > 0){
					foreach($errors as $key=>$error){
						error_log("an error occured: $key=$error");
					}
				}
			}else{
				$response = "Couldn't find a person with the given public key.";
			}
		}else{
			$response = 'My website doesn\'t have you in my system. You\'ll have to send me a <form action="' . FrontController::urlFor($this->site_member->member_name . '/follower') . '" method="post"><button type="submit"><span>Send Friend Request</span></button></form> to add you as a friend before you send me any messages.';
		}		
		return $response;
	}
	public function delete(Post $post, $last_page_viewed, $q = null){
		$this->q = $q;
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		$post = Post::findById($post->id, $this->current_user->id);
		if($post->owner_id !== $this->current_user->id && !AuthController::isSuperAdmin()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		
		Post::delete($post);
		self::setUserMessage(sprintf("'%s' was deleted.", $post->title));
		if($this->q === null){
			$this->redirectTo('posts/' . $last_page_viewed);
		}else{
			$this->redirectTo('posts', array('page'=>$last_page_viewed, 'q'=>$this->q));
		}
	}
	private function sendPostToGroups($groups, Post $post){
		if(count($groups) > 0){
			foreach($groups as $text){
				if($text === 'All+Contacts'){
					$this->people = Person::findAllByOwner($this->current_user->person_id);
				}else{
					$this->people = Person::findByTagTextAndOwner($text, $this->current_user->person_id);
				}
				$this->sendToPeople($this->people, $post);
			}
		}
	}
	private function sendPostToPeople($people, Post $post){
		if(count($people) > 0){
			$people = Person::findByIds($people);
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
			if($person->id != $this->current_user->person_id && $person->is_approved){
				error_log(sprintf("person= %s, current user = %s",$person->name, $this->current_user->name));
				$datum[] = sprintf("person_post_id=%s&title=%s&body=%s&source=%s&is_published=%s&post_date=%s&public_key=%s&type=%s", urlencode($post->id), urlencode($post->title), urlencode($post->body), urlencode($post->source), $post->is_published, urlencode($post->post_date), urlencode($person->public_key), $post->type);
				$to[] = $person;
			}
		}
		$responses = NotificationResource::sendMultiNotifications($to, 'post', $datum, 'post');
		if(count($responses) > 0){
			$message = array();
			foreach($responses as $key=>$response){
				$person = $to[$key];
				UserResource::setUserMessage($person->name . ' responded with ' . $response);
			}
		}
	}
}