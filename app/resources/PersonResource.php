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
			if(!AuthController::is_authorized()){
				$this->set_unauthorized();
			}
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $people;
		public $person;
		public function get($id = 0){
			$id = (int)$id;		
			if($id > 0){
				if($id == Application::$current_user->person_id){
					$this->person = Application::$current_user;
				}else{
					$this->person = Person::findByIdAndOwner($id, Application::$current_user->person_id);
				}
				if($this->person === null) return $this->set_not_found();
				$this->title = 'Person: ' . $this->person->email;				
				$this->output = $this->render('person/show', null);				
				return $this->render_layout('default', null);
			}else{
				$this->person = new Person();
				$this->title = "Add a person";
				$this->output = $this->render('person/show', null);
				return $this->render_layout('default', null);
			}
			
		}
		public function delete(Person $person){
			if($person->id == null){
				throw new Exception('Person id must be set');
			}
			$person = Person::findByIdAndOwner($person->id, Applicatoin::$current_user->person_id);
			if(!$person->is_owner){
				Person::delete($person);
			}else{
				Resource::setUserMessage("You can't delete the owner of the site.");
			}
			$this->redirect_to('addressbook');
		}
		
		public function put(Person $person, Profile $profile = null){
			$view = 'person/show';
			$this->person = Person::findByIdAndOwner($person->id, Application::$current_user->person_id);
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
				$this->person->owner_id = Application::$current_user->person_id;
				list($this->person, $errors) = Person::save($this->person);
				$user_message = '';
				if(count($errors) > 0){
					$message = array();
					foreach($errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					$user_message = 'Failed to save person - ' . implode(', ', $message);
				}else{
					$user_message = "{$this->person->name}'s info has been saved.";
				}
				Resource::setUserMessage($user_message);
			}
			$this->output = $this->render($view, array('user_message'=>$user_message));
			return $this->render_layout('default');					
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
				$this->person->owner_id = Application::$current_user->person_id;
				$user_message = null;
				try{
					list($this->person, $errors) = Person::save($this->person);				
				}catch(Exception $e){
					$user_message = $e->getMessage();
				}
				if($user_message !== null){
					Resource::setUserMessage('Failed to save person - ' . $user_message);
				}else{
					$this->people = Person::findAllByOwner(Application::$current_user->person_id);
				}
			}				
			$this->output = $this->render($view);
			return $this->render_layout('default');					
		}
	}
?>