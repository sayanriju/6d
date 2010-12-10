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
			if(!AuthController::is_super_admin()){
				$this->set_unauthorized();
				return;
			}
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $members;
		public $member;
		public function get($member_name = null){
			$this->member = Member::findByMemberName($member_name);
			$this->title = $this->member !== null && $this->member->id > 0 ? sprintf('Member: %s', $this->member->email) : 'Add a member';
			$this->member = $this->member == null ? new Member() : $this->member;
			$this->output = $this->render('member/edit', null);	
			return $this->render_layout('default', null);
		}
		public function delete(Member $member){
			if($member->id === null || strlen($member->id) === 0){
				$this->set_not_found();
				return;
			}
			$member = Member::findById($member->id);
			$person = Person::findById($member->person_id);
			if($member !== null && $member->id > 0 && !$person->is_owner){
				Person::delete($person);
				Member::delete($member);
				Resource::setUserMessage("{$person->name} was deleted.");
			}else{
				Resource::setUserMessage("You can't delete the owner of the site.");
			}
			$this->redirect_to('members');
		}
	}
?>