<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('NotificationResource') || require('resources/NotificationResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('FriendRequest') || require('models/FriendRequest.php');
class FollowersResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}

	public function __destruct(){
		parent::__destruct();
	}
	public $person;
	public $people;
	public function get(){
		$errors = array();
		if(!AuthController::is_authorized()){
			$this->set_unauthorized();
			return;
		}
		
		$this->people = FriendRequest::findAllForOwner(Application::$current_user->person_id);
		if($this->people === null){
			$this->people = array();
		}
		$this->title = 'Friend Requests';
		$this->output = $this->render('follower/index', array('errors'=>$errors));
		return $this->render_layout('default');
		
	}
	// If someone confirms the friend request, a request is made to this method.
	public function put(Person $person){
		error_log('confirmed a request from ' . $person->url . ' with the public_key = ' . $person->public_key);
		//TODO: check remote host against the url to verify who's sending the response.
		//error_log(sprintf('request from: host=%s, referrer=%s, ip=%s, public key = %s', $_SERVER['HTTP_HOST'], $_SERVER['HTTP_REFERER'], $_SERVER['REMOTE_ADDR'], urlencode($person->public_key)));
		if($person->public_key !== null && strlen($person->public_key) > 0 && $person->url !== null && strlen($person->url) > 0){
			error_log('finding the person by url = ' . $person->url);
			$this->person = Person::findByUrlAndOwnerId($person->url, Application::$member->person_id);
			error_log('request from ' . $this->person->name . ' is being processed');
			$this->person->public_key = base64_decode($person->public_key);
			error_log('gonna save the public key = ' . $this->person->public_key);		
			list($this->person, $errors) = Person::save($this->person);
			if(count($errors) > 0){
				error_log('errors from confirmation on the requesters side: ' . json_encode($errors));
				return json_encode($errors);
			}else{
				return 'ok';		
			}
		}else{
			return "I couldn't find that person.";
		}
	}
	// Someone has sent a friend request.
	public function post(Person $person){
		error_log('someone has sent a friend request from ' . $person->url);
		$this->person = Person::findByUrlAndOwnerId($person->url, Application::$member->person_id);
		self::notify('friend_request_has_been_posted', $this, $this->person);
		$message = null;
		if($this->person === null){
			$friend_request = FriendRequest::findByUrlAndOwnerId($person->url, Application::$member->person_id);
			if($friend_request === null){
				$friend_request = new FriendRequest(array('name'=>$person->name, 'email'=>$person->email, 'public_key'=>base64_decode($person->public_key), 'created'=>date('c'), 'url'=>$person->url, 'owner_id'=>Application::$member->person_id));
				try{
					$errors = FriendRequest::canSave($friend_request);
					if(count($errors) > 0){
						foreach($errors as $key=>$value){
							$message .= sprintf("%s: %s", $key, $value);
						}
					}else{
						error_log('saving friend request');
						$friend_request = FriendRequest::save($friend_request);
					}
				}catch(Exception $e){
					error_log($e);
					$message = $e->getMessage();
				}
			}
		}else{
			// Someone has sent another friend request, but is already a friend.
			$friend_request = new FriendRequest(array('name'=>$this->person->name, 'email'=>$this->person->email, 'public_key'=>$this->person->public_key, 'created'=>date('c'), 'url'=>$this->person->url, 'owner_id'=>Application::$member->person_id));
			$friend_request = FriendRequest::save($friend_request);
		}
		if($message !== null){
			return $message;
		}else{
			return "Thanks for the request. I'll make sure " . Application::$member->person->name . " gets it.";
		}
	}
}