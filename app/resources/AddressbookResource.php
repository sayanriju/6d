<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('Profile') || require('models/Profile.php');
class_exists('Tag') || require('models/Tag.php');

class AddressbookResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		if(!AuthController::isAuthorized() || Application::$current_user->person_id != Application::$member->person_id){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $people;
	public $person;
	public $groups;
	public function get($mini = false){
		$this->title = 'Address Book';
		$this->people = Person::findAllByOwner(Application::$current_user->person_id);
		
		$layout = 'layouts/default';
		if($this->people == null){
			$this->people = array();
		}else{
			$this->people = Person::removeOwner(Application::$current_user->person_id, $this->people);
			usort($this->people, array('Person', 'sort_by_name'));
		}
		$all_contacts = new Tag(array('id'=>-1, 'type'=>'group', 'text'=>'All Contacts'));
		$friend_requests = new Tag(array('id'=>-2, 'type'=>'group', 'text'=>'Friend Requests'));
		$this->groups = Tag::findAllTagsForGroups(Application::$current_user->person_id);
		if($this->groups === null){
			$this->groups = array();
		}
		$this->groups = array_merge(array($friend_requests), $this->groups);
		$this->groups = array_merge(array($all_contacts), $this->groups);
		$view = 'addressbook/index';
		if($mini){
			$view = 'addressbook/index_modal';
		}
		$this->output = $this->renderView($view);
		return $this->renderView($layout);
	}
	public function delete(Tag $group = null, Person $person = null){
		if($group != null){
			Tag::delete($group);
		}elseif($person != null){
			Person::delete($person);
		}
		$this->redirectTo('addressbook');
	}
	public function post($name = null){
		$errors = array();
		if($name != null){
			$profile = new Profile(array('name'=>$name));
			$this->person = new Person();
			$this->person->profile = serialize($profile);
			$this->person->owner_id = Application::$current_user->person_id;
			list($person, $errors) = Person::save($this->person);
			$this->person->id = $person->id;
		}
		if(count($errors) > 0){
			$message = $this->renderView('error/index', array('message'=>"The following errors occurred when saving groups. Please resolve and try again.", 'errors'=>$errors));
			self::setUserMessage($message);				
		}
		$view = 'addressbook/show';
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');		
	}
}