<?php
class_exists('Random') || require('lib/Random.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('aes128') || require('lib/aes128lib/aes128.php');
class_exists('NotificationResource') || require('NotificationResource.php');
	class PersonResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			if(!AuthController::isAuthorized()){
				throw new Exception(FrontController::UNAUTHORIZED, 401);
			}
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $people;
		public $person;
		public function get(Person $person = null){
			if(count($this->url_parts) > 1){
				$person = new Person(array('id'=>self::pathWithoutExtension($this->url_parts[1])));
			}
			
			if($person != null && $person->id > 0){
				if($person->id == $this->current_user->person_id){
					$this->person = $this->current_user;
				}else if(AuthController::isSuperAdmin()){
					$this->person = Person::findById($person->id);
				}else{
					$this->person = Person::findByIdAndOwner($person->id, $this->current_user->person_id);
				}
				$this->title = 'Person: ' . $this->person->email;				
				$this->output = $this->renderView('person/show', null);				
				return $this->renderView('layouts/default', null);
			}else{
				$this->person = new Person();
				$this->title = "Add a person";
				$this->output = $this->renderView('person/show', null);
				return $this->renderView('layouts/default', null);
			}
			
		}
		public function delete(Person $person){
			if($person->id == null){
				throw new Exception('Person id must be set');
			}
			$person = Person::findById($person->id);
			if(!$person->is_owner){
				Person::delete($person);
			}else{
				UserResource::setUserMessage("You can't delete the owner of the site.");
			}
			$this->redirectTo('addressbook');
		}
		
		public function put(Person $person, Profile $profile = null){
			$view = 'person/show';
			$this->person = Person::findById($person->id);
			if($this->person !== null){
				$this->person->is_approved = $person->is_approved;
				$this->person->email = $person->email;
				$this->person->url = $person->url;
				$this->person->name = $person->name;
				$this->person->session_id = session_id();
				$this->person->do_list_in_directory = false;
				if($profile !== null){
					$this->person->profile = serialize($profile);
				}
				$this->person->owner_id = $this->current_user->id;
				$this->person = Person::save($this->person);
				if($errors != null && count($errors) > 0){
					$message = array();
					foreach($errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					UserResource::setUserMessage('Failed to save person - ' . implode(', ', $message));
				}else{
					UserResource::setUserMessage("{$this->person->name}'s info has been saved.");
				}
			}
			$this->output = $this->renderView($view, array('errors'=>$errors));
			return $this->renderView('layouts/default');					
		}
		public function post(Person $person, Profile $profile = null){
			$view = 'person/index';
			$this->person = $person;
			// Posting to a resource means you're creating a new object of this type.
			// I added this logic to assert that assumption.
			if($person->id == null || strlen($person->id) == 0){
				$this->person->session_id = session_id();
				$this->person->uid = uniqid();
				$this->person->is_approved = true;
				$this->person->is_owner = false;
				$this->person->do_list_in_directory = false;
				if($profile !== null){
					$this->person->profile = serialize($profile);
				}
				$this->person->owner_id = $this->current_user->id;				
				$this->person = Person::save($this->person);
				if($errors != null && count($errors) > 0){
					$message = array();
					foreach($errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					UserResource::setUserMessage('Failed to save person - ' . implode(', ', $message));
				}else{
					if(AuthController::isSuperAdmin()){
						$this->people = Person::findAll();
					}else{
						$this->people = Person::findAllByOwner($this->current_user->id);
					}
				}
			}				
			$this->output = $this->renderView($view, array('errors'=>$errors));
			return $this->renderView('layouts/default');					
		}
	}
?>