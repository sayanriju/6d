<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Member") || require("models/Member.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("IndexResource") || require("IndexResource.php");
class MembersResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $members;
	public $legend;
	
	public function get(){
		if($this->request->path[0] !== null){
			$member_name = $this->request->path[0];
			if(count($this->request->path) === 1){
				$resource = new IndexResource();
				return $resource->execute($this->request);
			}
			if($this->request->path[1] === "members"){
				$this->set_not_found();
				return;
			}
			$resource_name = ucwords($this->request->path[1]) . "Resource";
			if(!class_exists($resource_name)) require($resource_name . ".php");
			$resource = new $resource_name();
			return $resource->execute($this->request);
		}
		if(AuthController::is_authed() && AuthController::$current_user->is_owner){
			$this->members = Member::find_all(0, 5);
		}else{
			$this->members = Member::find_in_directory(0, 5);
		}

		$view = "member/index";
		$this->output = View::render($view, $this);
		return View::render_layout('default', $this);
	}
	public function post(Member $member){
		if(!AuthController::is_authed() && (bool)AuthController::$current_user->is_owner){
			$this->set_unauthed();
			return;
		}
		$this->member = new Member(array("name"=>$member->name, "password"=>$member->password, "is_owner"=>false));
		save_object::execute($this->member);
		$this->set_redirect_to("members");
		$this->output = View::render("member/show", $this);
		return View::render_layout("default", $this);
	}
}