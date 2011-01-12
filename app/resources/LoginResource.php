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
		$this->output = $this->render('user/login');
		return $this->render_layout('login');			
	}
	
	public function post($email, $password = null){
		$isAuthed = false;
		if( AuthController::is_authorized()){
			$isAuthed = true;
		}
		$user = null;
		if(empty($email) || empty($password)) $this->redirect_to('login');
		$member = AuthController::do_verification($email, $password);
		if(AuthController::is_authorized()){
			$person = Person::findById($member->person_id);
			$person->session_id = session_id();
			Person::save($person);
			$this->redirect($member);
		}else{
			self::set_user_message($this->render('error/login', array('errors'=>array('auth'=>'authorization failed'), 'message'=>"Those credentials can't be found. If you're really trying to sign in, please try it again.")));
			$this->redirect_to('login');
		}
	}	
	private function redirect($user){
		$this->redirect_to($user->is_owner ? null : $user->member_name);
	}
}
?>