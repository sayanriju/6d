<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('NotificationResource') || require('resources/NotificationResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('FriendRequest') || require('models/FriendRequest.php');
class_exists('ServicePluginController') || require('controllers/ServicePluginController.php');
class_exists('IntroductionCommand') || require('commands/IntroductionCommand.php');
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
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}else{
			$this->people = FriendRequest::findAll();
			if($this->people === null){
				$this->people = array();
			}
			$this->title = 'Friend Requests';
			$this->output = $this->renderView('follower/index', array('errors'=>$errors));
			return $this->renderView('layouts/default', null);
		}
		
	}
	// If someone confirms the friend request, a request is made to this method.
	public function put(Person $person){
		//TODO: check remote host against the url to verify who's sending the response.
		//error_log(sprintf('request from: host=%s, referrer=%s, ip=%s, public key = %s', $_SERVER['HTTP_HOST'], $_SERVER['HTTP_REFERER'], $_SERVER['REMOTE_ADDR'], urlencode($person->public_key)));
		if($person->public_key !== null && strlen($person->public_key) > 0 && $person->url !== null && strlen($person->url) > 0){
			$this->person = Person::findByUrlAndOwnerId(urldecode($person->url), $this->site_member->person_id);
			$this->person->public_key = $person->public_key;
			$this->person = Person::save($this->person);
			return 'ok';
		}else{
			return "I couldn't find that person.";
		}
	}
	public function post(Person $person){
		$errors = array();
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}elseif($person->id !== null){
			$this->person = Person::findByIdAndOwner($person->id, $this->current_user->person_id);
			if($this->person->url !== null && strlen($this->person->url) > 0){
				$config = new AppConfiguration();
				$site_path = String::replace('/\/$/', '', FrontController::$site_path);
				
				$response = ServicePluginController::execute(new IntroductionCommand($this->person, $this->current_user));
				
				/*
				$data = sprintf("email=%s&name=%s&url=%s&created=%s", urlencode($this->current_user->email), urlencode($this->current_user->name),  urlencode(str_replace('http://', '', $site_path)), urlencode(date('c')));
				$response = NotificationResource::sendNotification($this->person, 'follower', $data, 'post');
				*/
				UserResource::setUserMessage($this->person->name . "'s site responded with " . $response);
				$this->title = 'Request Sent!';
				$this->output = $this->renderView('follower/confirmation');
			}else{
				$this->output = $this->renderView('follower/show', array('errors'=>$errors));
				$errors['url'] = "I need the person's website address to follow them.";
				UserResource::setUserMessage($errors['url']);
			}
			return $this->renderView('layouts/default', null);
		}
	}
}