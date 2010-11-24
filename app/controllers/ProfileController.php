<?php

class ProfileController{
	public function __construct() {}
	public function __destruct(){}
	public function will_delete_photo($sender, $info){
		if($info === str_replace(App::url_for(null), '', Application::$current_user->profile->photo_url)){
			Application::$current_user->profile->photo_url = null;
			Profile::save(Application::$current_user);
		}
	}
}