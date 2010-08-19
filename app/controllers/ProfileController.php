<?php

class ProfileController{
	public function __construct() {}
	public function __destruct(){}
	public function willDeletePhoto($sender, $info){
		if($info === str_replace(FrontController::urlFor(null), '', $this->current_user->profile->photo_url)){
			$this->current_user->profile->photo_url = null;
			Profile::save($this->current_user);
		}
	}
}