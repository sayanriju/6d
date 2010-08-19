<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('Person') || require('models/Person.php');
class_exists('Profile') || require('models/Profile.php');
class_exists('LoginResource') || require('LoginResource.php');
class ProfileResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		$this->max_filesize = 2000000;
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $person;
	public $max_filesize;
	public function getState(){
		return array_key_exists('state', $_SESSION) ? $_SESSION['state'] : null;
	}
	public function setState($value){
		$_SESSION['state'] = $value;
	}
	
	public function post($state = null){
		$this->setState($state);
		$this->redirectTo('profile');
		return '';
	}
	public function get($state = null, Person $person = null){
		$state = $state == null ? $this->getState() : $state;
		$this->setState($state);
		if(count($this->url_parts) > 1){
			// Get the person's photo.
			$photo_file_type = '.png';
			$matches = String::find('/\.(.+$)/', $this->url_parts[1]);
			if(count($matches) > 0){
				$photo_file_type = $matches[1];
			}
			if(!Application::isPhotoPublic()){
				$person = Person::findByPublicKey(urldecode($person->public_key));
				if($person !== null && $person->is_approved){
					$this->person = $this->site_member;
					$this->output = $this->renderView('profile/photo');
					return $this->renderView('layouts/default');
				}else{
					throw new Exception(FrontController::NOTFOUND, 404);
				}
			}else{
				$this->person = $this->site_member;
				$this->output = $this->renderView('profile/photo');
				return $this->renderView('layouts/default');
			}
		}else{
			$this->person = Application::$member;
			$this->title = $this->person->name . "'s profile.";
			if($state === 'edit'){
				if(!AuthController::isAuthorized() || $this->current_user->person_id !== Application::$member->person_id){
					throw new Exception(FrontController::UNAUTHORIZED, 401);
				}
				$this->output = $this->renderView('profile/edit', null);
				return $this->renderView('layouts/default', null);
			}else{
				if($this->person->profile === null){
					$this->person->profile = serialize(new Profile(null));
				}
				$this->output = $this->renderView('profile/index', null);
				return $this->renderView('layouts/default', null);
			}
		}
	}
	public static function getPhotoUrl($person, $photo_file_type = '.png'){
		if($person->profile === null || String::isNullOrEmpty($person->profile->photo_url)){
			return FrontController::urlFor('images') . 'nophoto' . $photo_file_type;
		}else if(strpos($person->profile->photo_url, 'http') !== false){
			return $person->profile->photo_url;
		}else if(String::isNullOrEmpty($person->profile->photo_url) !== null){
			return '/' . FrontController::getVirtualPath() . '/' . $person->profile->photo_url;
		}
	}
	public function put(Person $person, Profile $profile){
		if(!AuthController::isAuthorized() || $this->current_user->person_id !== $this->site_member->person_id){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		$this->setState(null);
		$this->person = $person;
		$this->person->session_id = session_id();
		$existing_person = Person::findById($this->current_user->person_id);
		if($existing_person === null || $existing_person->id === 0){
			throw new Exception(FrontController::NOTFOUND, 404);
		}
		
		$this->person->uid = $existing_person->getUid();
		if($profile != null){
			$this->person->profile = serialize($profile);
		}
		if(strlen($person->password) > 0){
			$this->person->password = String::encrypt($person->password);
			$this->person->confirmation_password = String::encrypt($person->password);
		}else{
			$this->person->password = $existing_person->password;
		}
		
		$this->person->id = $existing_person->id;
		$this->person->url = String::replace('/http[s]?\:\/\//', '', FrontController::$site_path);
		$this->person->url = String::replace('/\/$/', '', $this->person->url);
		$this->person->is_owner = $existing_person->is_owner;
		$this->person->is_approved = true;
		try{
			$this->person = Person::save($this->person);
			self::setUserMessage('Profile saved');
		}catch(Exception $e){
			self::setUserMessage($e->getMessage());
		}

		$this->person->profile = unserialize($this->person->profile);
		$view = 'profile/index';
		$this->output = $this->renderView($view, null);
		return $this->renderView('layouts/default');
	}
}

?>