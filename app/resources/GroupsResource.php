<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Group") || require("models/Group.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class GroupsResource extends AppResource{
	public function __construct(){
		parent::__construct();
		if(!AuthController::is_authed()){
			$this->set_unauthed("Please signin to edit groups.");
		}
	}
	public $groups;
	public $group;
	public $legend;
	
	public function get(){		
		$this->groups = find_by::execute("owner_id=:owner_id", new Group(array("owner_id"=>AuthController::$current_user->id)));
		if(!is_array($this->groups)) $this->groups = array($this->groups);
		$view = "group/index";
		$this->output = View::render($view, $this);
		return View::render_layout('default', $this);
	}
	public function post(Group $group){
		$this->group = new Group(array("name"=>$group->name, "owner_id"=>AuthController::$current_user->id));
		save_object::execute($this->group);
		$this->set_redirect_to(AuthController::$current_user->name . "/groups");
		$this->output = View::render("group/show", $this);
		return View::render_layout("default", $this);
	}
}