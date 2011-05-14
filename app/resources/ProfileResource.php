<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("Member") || require("models/Member.php");
class ProfileResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $owner;
	public function get(){
		$this->owner = AppResource::$member;
		$this->title = self::$member->name . "'s profile";
		$this->output = View::render("profile/index", $this);
		return View::render_layout("default", $this);
	}
	public function post($state = null){
		if($state === "edit"){
			if(!AuthController::is_authed()){
				$this->set_unauthed();
				return;
			}
			$view = "profile/edit";
		}else{
			$view = "profile/index";
		}
		$this->owner = AuthController::$current_user;
		$this->owner->set_member_meta(Member_meta::find_by_id($this->owner->id, null));
		$this->title = self::$member->name . "'s profile";
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
	public function put(Member $member){
		if(!AuthController::is_authed()){
			$this->set_unauthed();
			return;
		}
		$this->owner = AuthController::$current_user;
		$view = "profile/index";
		$this->title = self::$member->name . "'s profile";
		$this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
}