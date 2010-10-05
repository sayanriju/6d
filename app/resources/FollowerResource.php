<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Random') || require('lib/Random.php');
class_exists('Person') || require('models/Person.php');
class_exists('FriendRequest') || require('models/FriendRequest.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('ServicePluginController') || require('controllers/ServicePluginController.php');
class_exists('IntroductionCommand') || require('commands/IntroductionCommand.php');
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
		$response = null;
		if($request !== null){
			if($person === null){
				error_log('person is null in saving follower request');
				$person = new Person(array('email'=>$request->email
					, 'name'=>$request->name
					, 'url'=>$request->url
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
				error_log("reseting public key ");
			}
			$person->owner_id = Application::$current_user->person_id;
			list($person, $errors) = Person::save($person);			
			if(count($errors) > 0){
				error_log(json_encode($errors));
				$response = implode('<br />', $errors);
			}else{
				FriendRequest::delete($request);
				$response = $this->sendNotification($person);
			}
		}else{
			if($person !== null){
				$response = $this->sendNotification($person);
			}
		}
		return $response;
	}
	// Confirm as a friend
	public function put(FriendRequest $request){
		$response = null;
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}else{
			$request = FriendRequest::findByIdAndOwnerId($request->id, Application::$current_user->person_id);
			$this->person = Person::findByUrlAndOwnerId($request->url, Application::$current_user->person_id);
			$response = $this->save($request, $person);
			Resource::setUserMessage(sprintf("%s has been made a friend. %s", $request->name, $response->output));
		}
		$this->redirectTo(Application::$current_user->member_name . '/addressbook');
	}
	private function sendNotification($person){
		$config = new AppConfiguration();
		$data = sprintf("_method=put&email=%s&url=%s&public_key=%s", urlencode(Application::$current_user->email), urlencode(Application::$current_user->url), $person->public_key);
		error_log('sending notification and $data = ' . $data);	
		$response = NotificationResource::sendNotification($person, 'followers', $data, 'post');
		return $response;
	}
	public function post(Person $person){
		$errors = array();
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}elseif($person->id !== null){
			$this->person = Person::findByIdAndOwner($person->id, Application::$current_user->person_id);
			if($this->person->url !== null && strlen($this->person->url) > 0){
				error_log('found ' . $this->person->url . ' to send a friend request to.');
				$config = new AppConfiguration();
				$site_path = String::replace('/\/$/', '', FrontController::$site_path);
				$response = ServicePluginController::execute(new IntroductionCommand($this->person, Application::$current_user));				
				if($response->headers['http_code'] == 404){
					Resource::setUserMessage("That web address was not found. Please go back and confirm that " . $this->person->url . " is a working site.");
				}else{
					Resource::setUserMessage($this->person->name . "'s site responded with " . $response->output);
					$this->output = $this->renderView('follower/confirmation');
				}
				$this->title = 'Request Sent!';
			}else{
				$this->output = $this->renderView('follower/show', array('errors'=>$errors));
				$errors['url'] = "I need the person's website address to follow them.";
				Resource::setUserMessage($errors['url']);
			}
			error_log(Resource::getUserMessage());
			return $this->renderView('layouts/default', null);
		}
	}
	public function delete(FriendRequest $request){
		if(!AuthController::isAuthorized()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		if($request->id > 0){
			$response = FriendRequest::delete($request);
			Resource::setUserMessage('Request has been deleted: ' . $response);
		}
		$this->output = $this->renderView('follower/index');
		return $this->renderView('layouts/default');
	}
}