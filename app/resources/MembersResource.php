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
				$this->members = Member::findAll(AuthController::isSuperAdmin() ? null : true);
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
			$this->output = $this->renderView($view, null);				
			return $this->renderView('layouts/default', null);
		}
		public function put(Member $member, Profile $profile = null){			
			$view = 'member/edit';
			$this->member = Member::findById($member->id);
			if($this->member !== null){
				$this->member->person = Person::findById($this->member->person_id);
				$this->member->member_name = $member->member_name;
				$this->member->person->id = $this->member->person_id;
				$this->member->person->session_id = session_id();
				$this->member->person->name = $member->name;
				$this->member->person->email = $member->email;
				$this->member->person->uid = uniqid();
				$this->member->person->url = sprintf("%s/%s", $this->current_user->url, $this->member->member_name);
				$this->member->person->is_approved = $member->is_approved === null ? false : $member->is_approved;
				$this->member->person->do_list_in_directory = $member->do_list_in_directory === null ? false : $member->do_list_in_directory;
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
				$this->member = Member::save($this->member);
				if($this->member->errors != null && count($this->member->errors) > 0){
					$message = array();
					foreach($this->member->errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					
					UserResource::setUserMessage('Failed to save member - ' . implode(', ', $message));
				}else{
					UserResource::setUserMessage("{$this->member->person->name}'s info has been saved.");
				}
			}
			$this->redirectTo('members');
			$this->output = $this->renderView($view, array('errors'=>$errors));
			return $this->renderView('layouts/default');					
		}
		public function post(Member $member, Profile $profile = null){
			$view = 'member/index';
			$this->member = $member;
			// Posting to a resource means you're creating a new object of this type.
			// I added this logic to assert that assumption.
			if($member->id === null || strlen($member->id) === 0){
				$this->member->person->session_id = session_id();
				$this->member->person->password = String::encrypt($member->password);
				$this->member->person->name = $member->name;
				$this->member->person->email = $member->email;
				$this->member->person->uid = uniqid();
				$url = String::replace('/http\:\/\//', '', FrontController::urlFor(null));
				$this->member->person->url = sprintf("%s%s", $url, $this->member->member_name);
				$this->member->person->is_approved = $member->is_approved === null ? false : $member->is_approved;
				$this->member->person->do_list_in_directory = $member->do_list_in_directory === null ? false : $member->do_list_in_directory;
				$this->member->person->is_owner = false;
				if($profile !== null){
					$this->member->person->profile = serialize($profile);
				}
				$this->member->person->is_owner = false;
				$this->member = Member::save($this->member);
				if($this->member->errors != null && count($this->member->errors) > 0){
					$message = array();
					foreach($this->member->errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					UserResource::setUserMessage('Failed to save member - ' . implode(', ', $message));
				}else{
					$this->members = Member::findAll(AuthController::isSuperAdmin() ? null : true);
				}
			}				
			$this->output = $this->renderView($view, array('errors'=>$errors));
			return $this->renderView('layouts/default');					
		}
	}
?>