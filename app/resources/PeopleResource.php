<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('FriendRequest') || require('models/FriendRequest.php');
class PeopleResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public $people;
	public $person;
	public $follow_requestors;
	
	public function get($group = null){
		if(!AuthController::is_authorized()){
			$this->set_unauthorized();
			return;
		}
		$this->person = new Person();
		$this->people = $this->getPeople(new Tag(array('text'=>$group, 'type'=>'group')));
		if($this->people == null){
			$this->people = array();
		}else{
			usort($this->people, array('Person', 'sort_by_name'));
		}
		$this->people = Person::removeOwner(Application::$current_user->person_id, $this->people);
		$this->title = 'People';
		$this->output = $this->render('person/index', null);
		return $this->render_layout('default');
	}
	public function delete($ids = array()){
		if($ids !== null && strlen($ids) > 0){
			$ids = explode(',', $ids);
			$deleted_people = Person::delete_many($ids, Application::$current_user->person_id);
		}
		$this->person = new Person();
		$this->people = Person::findAllByOwner(Application::$current_user->person_id);
		$this->people = $this->people === null ? array() : $this->people;
		$this->title = 'People';
		$this->output = $this->render('person/index', null);
		return $this->render_layout('default');
	}
	private function getPeople($group){
		if($group->text !== 'All Contacts'){
			return Person::findByTagTextAndOwner(urlencode($group->text), Application::$current_user->person_id);
		}elseif($group->text === 'Friend Requests'){
			return FriendRequest::findAllForOwner(Application::$current_user->person_id);
		}else{
			return Person::findAllByOwner(Application::$current_user->person_id);
		}
	}
}