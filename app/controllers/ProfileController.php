<?php

class ProfileController{
	public function __construct() {}
	public function __destruct(){}
	public function willDeletePhoto($sender, $info){
		if($info === str_replace(FrontController::urlFor(null), '', Application::$current_user->profile->photo_url)){
			Application::$current_user->profile->photo_url = null;
			Profile::save(Application::$current_user);
		}
	}
}