<?php
class_exists('Random') || require('lib/Random.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('Member') || require('models/Member.php');
class_exists('aes128') || require('lib/aes128lib/aes128.php');
class_exists('NotificationResource') || require('NotificationResource.php');
	class MemberResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			if(!AuthController::isAuthorized() && !AuthController::isSuperAdmin()){
				throw new Exception(FrontController::UNAUTHORIZED, 401);
			}
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $members;
		public $member;
		public function get(Member $member = null){
			if(count($this->url_parts) > 1){
				$member_name = $this->url_parts[1];
				$this->member = Member::findByMemberName($member_name);
			}else{
				$this->member = new Member();
			}
			$this->title = $this->member !== null && $this->member->id > 0 ? sprintf('Member: %s', $this->member->email) : 'Add a member';
			$this->output = $this->renderView('member/edit', null);	
			return $this->renderView('layouts/default', null);
		}
		public function delete(Member $member){			
			if($member->id === null || strlen($member->id) === 0){
				throw new Exception(FrontController::NOTFOUND, 404);
			}
			$member = Member::findById($member->id);
			$person = Person::findById($member->person_id);
			if($member !== null && $member->id > 0 && !$person->is_owner){
				Person::delete($person);
				Member::delete($member);
				UserResource::setUserMessage("{$person->name} was deleted.");
			}else{
				UserResource::setUserMessage("You can't delete the owner of the site.");
			}
			$this->redirectTo('members');
		}
		
		public function put(Person $person, Profile $profile = null){			
			$view = 'member/edit';
			$this->member = Member::findPersonByMemberId($person->id);
			if($this->member !== null){
				$this->member->is_approved = $person->is_approved === null ? false : $person->is_approved;
				$this->member->do_list_in_directory = $person->do_list_in_directory === null ? false : $person->do_list_in_directory;
				$this->member->email = $person->email;
				$this->member->url = $person->url;
				$this->member->name = $person->name;
				$this->member->session_id = session_id();
				if($person->password !== null){
					$this->member->password = $person->password;
					$this->member->confirmation_password = $person->password;
				}
				
				if($profile !== null){
					$this->member->profile = serialize($profile);
				}
				$this->member->owner_id = $this->current_user->id;
				$this->member->is_owner = false;
				$this->member->id = $this->member->person_id;
				$this->member = Person::save($this->member);
				if($errors != null && count($errors) > 0){
					$message = array();
					foreach($errors as $key=>$value){
						$message[] = sprintf("%s: %s", $key, $value);
					}
					UserResource::setUserMessage('Failed to save member - ' . implode(', ', $message));
				}else{
					UserResource::setUserMessage("{$this->member->name}'s info has been saved.");
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
				$this->member->person->password = $member->password;
				$this->member->person->name = $member->name;
				$this->member->person->email = $member->email;
				$this->member->person->uid = uniqid();
				$this->member->person->url = $member->url;
				$this->member->person->is_approved = $member->is_approved === null ? false : $member->is_approved;
				$this->member->person->do_list_in_directory = $member->do_list_in_directory === null ? false : $member->do_list_in_directory;
				$this->member->person->is_owner = false;
				if($profile !== null){
					$this->member->person->profile = serialize($profile);
				}
				$this->member->person->owner_id = $this->current_user->id;				
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