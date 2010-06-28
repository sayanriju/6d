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
	}
?>