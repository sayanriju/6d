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
		$this->photos = $photo->findAll('media/' . Application::$member->member_name);
		$view = 'post/show';
		$layout = 'layouts/' . $layout;
		if( AuthController::isAuthorized()){
			$view = 'post/edit';
		}
		if(count($this->url_parts) > 1){
			$this->post = Post::findById($this->url_parts[1], Application::$member->person_id);
			if($this->post == null){
				throw new Exception(FrontController::NOTFOUND, 404);
			}
		}else{
			$this->post = $post;
		}
		if($this->post != null && strlen($this->post->id) > 0){
			$this->title = $this->post->title;
			$this->description = $this->post->description;
			$this->output = $this->renderView($view, null);
			return $this->renderView($layout, null);
		}else{
			if(!AuthController::isAuthorized()){
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
		error_log('from get auth');
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
		$url = ($url === null ? FrontController::urlFor('images') . 'nophoto.png' : $url);
		return $url;
	}
	public function put(Post $post, $people = array(), $groups = array(), $make_home_page = false, $public_key = null, $photo_names = array(), $last_page_viewed = 1){
		
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		
		$this->last_page_viewed = $last_page_viewed;
		
		if($post->id !== null && strlen($post->id) > 0){
			$this->post = Post::findById($post->id, Application::$current_user->person_id);
		}
		
		if($this->post->owner_id !== Application::$current_user->person_id && !AuthController::isSuperAdmin()){
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
			$post->owner_id = Application::$current_user->person_id;
			if($post->type !== 'status'){
				$post->custom_url = String::stringForUrl($post->title);
			}
			if(strlen($post->password) === 0){
				$post->password = null;
			}
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
		}else{
			self::setUserMessage("That post doesn't exist.");
		}
		$this->redirectTo(Application::$current_user->member_name . '/' . $this->post->custom_url);			
	}
	
	public function delete(Post $post, $last_page_viewed, $q = null){
		$this->q = $q;
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		$post = Post::findById($post->id, Application::$current_user->person_id);
		if($post->owner_id !== Application::$current_user->person_id && !AuthController::isSuperAdmin()){
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
}