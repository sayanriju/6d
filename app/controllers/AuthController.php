<?php
class_exists('SuperAdmin') || require('models/SuperAdmin.php');
class_exists('Member') || require('models/Member.php');
class AuthController{
	public function __construct(){}
	public function __destruct(){}
	public static $user;
	public static function isAuthorized(){
		return self::authKey() !== null;
	}
	public static $is_super_admin;
	public static function isSuperAdmin(){
		if(self::$is_super_admin === null && self::authKey() !== null){
			self::$is_super_admin = SuperAdmin::findByEmail(self::authKey()) !== null;
		}else{
			self::$is_super_admin = false;
		}
		return self::$is_super_admin;
	}
	public static function authKey(){
		if(array_key_exists('authKey', $_SESSION) && strlen($_SESSION['authKey']) > 0){
			$sessionAuthKey = $_SESSION['authKey'];
		}else{
			$sessionAuthKey = null;
		}
		return $sessionAuthKey;
	}
	public static function setAuthKey($email){
		$_SESSION['authKey'] = $email;
	}
	public static function logout(){
		$_SESSION = array();
		$user = Person::findBySessionId(session_id());
		$user->session_id = null;
		Person::save($user);
		if(ini_get('session.use_cookies')){
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
	}
	
	public static function doVerification($email, $password){
		// I'm going to see if this is the admin trying to log, if not check the db to verify a user.
		$config = new AppConfiguration(null);
		$password = $password;
		$email = $email;
		self::$user = Member::findByEmailAndPassword($email, $password);
		return self::$user;
	}	
}