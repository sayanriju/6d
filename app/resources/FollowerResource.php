<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Random') || require('lib/Random.php');
class_exists('Person') || require('models/Person.php');
class_exists('FriendRequest') || require('models/FriendRequest.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class FollowerResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}

	public function __destruct(){
		parent::__destruct();
	}
	public $person;
	public function get(){
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		
		if(count($this->url_parts) > 1){
			$id = String::replace('/\..*$/', '', $this->url_parts[1]);
			$this->person = FriendRequest::findByIdAndOwnerId($id, Application::$current_user->person_id);
			$this->title =  $this->person->name;
		}
		$this->output = $this->renderView('follower/show');
		return $this->renderView('layouts/default', null);
	}
	private function save($request, $person = null){
		if($request !== null){
			if($person === null){
				$person = new Person(array('email'=>$request->email
					, 'name'=>$request->name
					, 'url'=>urldecode($request->url)
					, 'uid'=>uniqid()
					, 'session_id'=>session_id()
					, 'public_key'=>Random::getPassword()
					, 'is_approved'=>true
					, 'do_list_in_directory'=>false
					, 'is_owner'=>false));
			}else{
				$person->public_key = Random::getPassword();
				$person->is_approved = true;
				$person->is_owner = false;
			}			
			$person->owner_id = Application::$current_user->person_id;
			$person = Person::save($person);
			FriendRequest::delete($request);
			$this->sendNotification($person);
		}else{
			if($person !== null){
				$this->sendNotification($person);
			}
		}
	}
	// Confirm as a friend
	public function put(FriendRequest $request){		
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}else{
			$request = FriendRequest::findByIdAndOwnerId($request->id, Application::$current_user->person_id);
			$person = Person::findByUrlAndOwnerId($person->url, Application::$current_user->person_id);
			$this->save($request, $person);
		}
		$this->output = $this->renderView('follower/index');
		return $this->renderView('layouts/default');
	}
	private function sendNotification($person){
		$config = new AppConfiguration();
		$data = sprintf("_method=put&email=%s&url=%s&public_key=%s", urlencode(Application::$current_user->email), urlencode(Application::$current_user->url), urlencode($person->public_key));
		$response = NotificationResource::sendNotification($person, 'followers', $data, 'post');
		UserResource::setUserMessage(sprintf("%s has been made a friend. %s", $person->name, $response));
	}
	// Someone has sent a friend request.
	public function post(Person $person){
		$this->person = Person::findByUrlAndOwnerId($person->url, Application::$member->person_id);
		$message = null;
		if($this->person === null){
			$friend_request = FriendRequest::findByUrlAndOwnerId($person->url, Application::$member->person_id);
			if($friend_request === null){
				$friend_request = new FriendRequest(array('name'=>$person->name, 'email'=>$person->email, 'public_key'=>$person->public_key, 'created'=>date('c'), 'url'=>$person->url, 'owner_id'=>Application::$member->person_id));
				try{
					$errors = FriendRequest::canSave($friend_request);
					if(count($errors) > 0){
						foreach($errors as $key=>$value){
							$message .= sprintf("%s: %s", $key, $value);
						}
					}else{
						error_log($friend_request->name . ', ' . $friend_request->email);
						$friend_request = FriendRequest::save($friend_request);
					}
				}catch(Exception $e){
					$message = $e->getMessage();
				}
			}
		}else{
			// Someone has sent another friend request, but is already a friend.
			$friend_request = new FriendRequest(array('name'=>$this->person->name, 'email'=>$this->person->email, 'public_key'=>$this->person->public_key, 'created'=>date('c'), 'url'=>$this->person->url));
			$this->save($friend_request, $this->person);
		}
		if($message !== null){
			return $message;
		}else{
			return "Thanks for the request. I'll make sure " . Application::$member->name . " gets it.";
		}
	}
	public function delete(FriendRequest $request){
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		if($request->id > 0){
			$response = FriendRequest::delete($request);
			UserResource::setUserMessage('Request has been deleted: ' . $response);
		}
		$this->output = $this->renderView('follower/index');
		return $this->renderView('layouts/default');
	}
}