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
	}
?>