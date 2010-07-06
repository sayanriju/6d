<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Tag') || require('models/Tag.php');

class GroupsResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		if(! AuthController::isAuthorized()){
			FrontController::setRequestedUrl('addressbook');
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $groups;
	public $group;
	
	public function post(Tag $group = null){
		$view = 'group/index';
		$errors = array();
		if($group != null && $group->text != null){
			$group->type = 'group';
			if($group->parent_id > 0){
				$existing_tags = Tag::findTagsByTextAndParent_id($group->text, $group->parent_id);
			}else{
				$existing_tags = Tag::findGroupTagsByText($group->text);
			}
			$message = null;
			$errors = array();		
			if($existing_tags === null){
				$group->owner_id = $this->current_user->person_id;
				list($this->group, $errors) = Tag::save($group);				
			}
		}
		
		if(count($errors) > 0){
			$message = $this->renderView('error/index', array('message'=>"The following errors occurred when saving groups. Please resolve and try again.", 'errors'=>$errors));
			self::setUserMessage($message);
		}
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');
	}
		
	public function delete($groups = null, $ids = null, Tag $group = null){
		if($groups !== null){
			$this->groups = $groups;
			Tag::delete_many('group', $groups);
		}elseif($ids !== null && $group !== null){
			$this->group = $group;
			Tag::delete_many_with_parent_ids('group', $ids);
		}
		$all_contacts = new Tag(array('id'=>-1, 'type'=>'group', 'text'=>'All Contacts'));
		$this->groups = Tag::findAllTagsForGroups($this->current_user->person_id);
		if($this->groups === null){
			$this->groups = array();
		}
		$this->groups = array_merge(array($all_contacts), $this->groups);
		$view = 'group/index';
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');
	}	
}