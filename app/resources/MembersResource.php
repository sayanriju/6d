<?php
class_exists('Random') || require('lib/Random.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('Member') || require('models/Member.php');
	class MembersResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $members;
		public $member;
		public function get(Member $member = null){
			$view = 'member/index';
			if(count($this->url_parts) > 1){
				$member_name = $this->url_parts[1];
				$member = Member::findByMemberName($member_name);
			}else{
				$this->members = Member::findAllAsPerson(AuthController::is_super_admin() ? null : true);
			}
			if($this->members !== null){
				$this->title = "6d Directory";	
			}else{
				$view = 'member/show';
				if($member != null && $member->id > 0){
					$this->member = $member;
					$this->title = $this->member->name;				
				}else{
					$this->member = new Person();
					$this->title = "Add a member";
				}
			}
			$this->output = $this->render($view, null);				
			return $this->render_layout('default', null);
		}
		public function put(Person $person, Member $member, Profile $profile = null){			
			$view = 'member/edit';
			$this->member = Member::findById($member->id);
			if($this->member !== null){
				$this->member->person = Person::findById($this->member->person_id);
				$this->member->member_name = $member->member_name;
				$this->member->person->id = $this->member->person_id;
				$this->member->person->session_id = session_id();
				$this->member->person->name = $person->name;
				$this->member->person->email = $person->email;
				$this->member->person->url = sprintf("%s/%s", Application::$current_user->url, $this->member->member_name);
				$this->member->person->is_approved = $person->is_approved === null ? false : $person->is_approved;
				$this->member->person->do_list_in_directory = $person->do_list_in_directory === null ? false : $person->do_list_in_directory;
				$this->member->person->is_owner = false;
				if($profile !== null){
					$this->member->person->profile = serialize($profile);
				}
				$this->member->person->is_owner = false;
				if(strlen($member->password) > 0){
					$this->member->person->password = String::encrypt($member->password);
					$this->member->person->confirmation_password = String::encrypt($member->password);
				}
				if($profile !== null){
					$this->member->person->profile = serialize($profile);
				}
				$this->member = Member::saveAsPerson($this->member);
				if($this->member->errors != null && count($this->member->errors) > 0){
					$message = array();
					foreach($this->member->errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					Resource::setUserMessage('Failed to save member - ' . implode(', ', $message));
				}else{
					Resource::setUserMessage("{$this->member->person->name}'s info has been saved.");
				}
			}
			$this->redirect_to('members');
			$this->output = $this->render($view, array('errors'=>$errors));
			return $this->render_layout('default');					
		}
		public function post(Person $person, Member $member, Profile $profile = null){			
			$view = 'member/index';
			$this->member = $member;
			// Posting to a resource means you're creating a new object of this type.
			// I added this logic to assert that assumption.
			if($member->id === null || strlen($member->id) === 0){
				$this->member->person->session_id = session_id();
				$this->member->person->password = String::encrypt($person->password);
				$this->member->person->name = $person->name;
				$this->member->person->email = $person->email;
				$this->member->person->uid = uniqid();
				$url = String::replace('/http\:\/\//', '', App::url_for(null));
				$this->member->person->url = sprintf("%s%s", $url, $this->member->member_name);
				$this->member->person->is_approved = $person->is_approved === null ? false : $person->is_approved;
				$this->member->person->do_list_in_directory = $person->do_list_in_directory === null ? false : $person->do_list_in_directory;
				$this->member->person->is_owner = false;
				if($profile !== null){
					$this->member->person->profile = serialize($profile);
				}
				$this->member->person->is_owner = false;
				$this->member = Member::saveAsPerson($this->member);
				if($this->member->errors != null && count($this->member->errors) > 0){
					$message = array();
					foreach($this->member->errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					Resource::setUserMessage('Failed to save member - ' . implode(', ', $message));
				}else{
					$this->members = Member::findAllAsPerson(AuthController::is_super_admin() ? null : true);
				}
			}				
			$this->output = $this->render($view, array('errors'=>$this->member->errors));
			return $this->render_layout('default');					
		}
	}
?>