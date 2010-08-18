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
			$user = AuthController::doVerification($email, $password);
			$isAuthed = $user !== null;		
		}
		if($isAuthed){
			if($email != null && !empty($email)){
				AuthController::setAuthKey($email);
			}
			if(FrontController::requestedUrl() != null){
				$this->redirectTo(FrontController::requestedUrl());
			}else{
				$this->redirectTo(null);
			}
		}else{
			self::setUserMessage($this->renderView('error/login', array('errors'=>array('auth'=>'authorization failed'), 'message'=>"Those credentials can't be found. If you're really trying to sign in, please try it again.")));
			$this->redirectTo('login');
		}
	}	
}
?>