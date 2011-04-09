<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Member") || require("models/Member.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class MemberResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $person;
	public $legend;
	public function get(Member $member){
		$this->person = find_one_by::execute("ROWID=:id", new Member(array("id"=>(int)$member->id)));
		$view = "member/show";
		$this->legend = "Edit this member";
		if($this->person === null) $this->person = new Member(array("id"=>0, "name"=>"New member"));
		$this->title = $this->person->name;
		if(AuthController::is_authed() && (bool)AuthController::$current_user->is_owner){
			$view = "member/edit";
			$this->legend = $this->person->id === 0 ? "Add a new member" : "Edit this member";			
		}else{
			$this->set_not_found();
		}		
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
	public function delete(Member $member){
		if(!AuthController::is_authed() || !(bool)AuthController::$current_user->is_owner){
			$this->set_unauthed();
			return;
		}
		if($member->id == AuthController::$current_user->id && AuthController::$current_user->is_owner){
			return "You don't want to delet the owner";
		}
		$this->person = find_one_by::execute("ROWID=:id", $member);
		if($this->person !== null){
			delete_object::execute($this->person);
		}
		$this->set_redirect_to("members");
		$this->output = View::render("member/index", $this);
		return View::render_layout("default", $this);
	}
	public function put(Member $member){
		if(!AuthController::is_authed() || !(bool)AuthController::$current_user->is_owner){
			$this->set_unauthed();
			return;
		}		
		$this->person = find_one_by::execute("ROWID=:id", new Member(array("id"=>(int)$member->id)));
		if($this->person !== null){
			$this->person->name = $member->name;
			$this->person->password = (strlen($member->password) > 0 ? $member->password : $this->person->password);
			$this->person->in_directory = $member->in_directory === null ? false : $member->in_directory;
			$this->person->is_owner = $this->person->id == 1 ? true : false;
			save_object::execute($this->person);
		}
		$this->set_redirect_to("members");
		$this->output = View::render("member/show", $this);
		return View::render_layout("default", $this);
	}	
}