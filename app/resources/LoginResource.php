<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('Person') || require('models/Person.php');
class LoginResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}
	public function __destruct(){
		parent::__destruct();
	}
	
	public function get(){
		$this->title = 'Login';
		$this->output = $this->renderView('user/login');
		return $this->renderView('layouts/default');			
	}
	
	public function post($email, $password = null){
		$isAuthed = false;
		if( AuthController::isAuthorized()){
			$isAuthed = true;
		}
		$user = null;
		if(empty($email) || empty($password)){
			$isAuthed = false;
		}else{
			$user = self::doVerification($email, $password);
			$isAuthed = $user !== null;		
		}
		if($isAuthed){
			if($email != null && !empty($email)){
				AuthController::setAuthKey($email);
			}
			if(FrontController::requestedUrl() != null){
				$this->redirectTo(FrontController::requestedUrl());
			}else{
				if($this->current_user->is_owner){
					$this->redirectTo(null);
				}else{
					$this->redirectTo($user->member_name);
				}
			}
		}else{
			self::setUserMessage($this->renderView('error/login', array('errors'=>array('auth'=>'authorization failed'), 'message'=>"Those credentials can't be found. If you're really trying to sign in, please try it again.")));
			$this->redirectTo('login');
		}
	}
		
	public static function doVerification($email, $password){
		// I'm going to see if this is the admin trying to log, if not check the db to verify a user.
		$config = new AppConfiguration(null);
		$password = $password;
		$email = $email;
		if($config->email === $email && $config->site_password === $password){
			return true;
		}else{
			$user = Member::findByEmailAndPassword($email, $password);
			return $user;
		}
	}
	
}
?>