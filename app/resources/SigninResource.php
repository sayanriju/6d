<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("Member") || require("models/Member.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class SigninResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function get(){
		if(AuthController::$current_user !== null){
			$this->set_redirect_to(null);
			return;
		}
		$this->title = "Chinchllalite Sign in Page";
		$this->output = View::render("signin/index", $this);
		return View::render_layout("default", $this);
	}
	public function post(Member $member){
		$member = Member::find_by_signin_and_password($member->signin, $member->password);
		if($member !== null){
			$expiry = time() + 60*60*24*30;
			$hash = AuthController::get_chin_auth_hash($member->name, $expiry);
			AuthController::set_chin_auth($hash, $expiry);
			$member->hash = $hash;
			$member->expiry = $expiry;
			save_object::execute($member);
			$this->set_redirect_to($member->is_owner ? null : $member->name);
			return;
		}
		
		App::set_user_message("Invalid credentials");
		$this->output = View::render("signin/index", $this);
		return View::render_layout("default", $this);
	}	
}
