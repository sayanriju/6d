<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("Group") || require("models/Group.php");
class GroupResource extends AppResource{
	public function __construct(){
		parent::__construct();
		$this->contacts = array();
		$this->groups = array();
		if(!AuthController::is_authed()){
			$this->set_unauthed("Please signin to edit groups.");
		}
	}
	public $group;
	public function get(Group $group){
		$this->group = find_one_by::execute("ROWID=:id and owner_id=:owner_id", new Group(array("owner_id"=> AuthController::$current_user->id, "id"=>(int)$group->id)));
		$view = "group/show";
		$this->legend = "Edit this group";
		if($this->group === null) $this->group = new Group(array("id"=>0, "name"=>"New group"));
		$this->title = $this->group->name;
		if(AuthController::is_authed()){
			$view = "group/edit";
			$this->legend = $this->group->id === 0 ? "Add a new group" : "Edit this group";			
		}else{
			$this->set_not_found();
		}		
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
	public function post(Group $group){
		$this->group = new Group(array("name"=>$group->name, "owner_id"=>AuthController::$current_user->id));
		save_object::execute($this->group);
		$this->set_redirect_to(AuthController::$current_user->name . "/addressbook");
		$this->output = View::render("group/show", $this);
		return View::render_layout("default", $this);
	}
}