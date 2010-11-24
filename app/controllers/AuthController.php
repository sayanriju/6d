<?php
class_exists('SuperAdmin') || require('models/SuperAdmin.php');
class_exists('Member') || require('models/Member.php');
class AuthController{
	public function __construct(){}
	public function __destruct(){}
	public static $member;
	public static function is_authorized(){
		error_log('is authed = ' . self::authKey());
		return self::authKey() !== null;
	}
	public static $is_super_admin;
	public static function is_super_admin(){
		if(self::authKey() !== null){
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
		$member = Person::findBySessionId(session_id());
		$member->session_id = null;
		Person::save($member);
		if(ini_get('session.use_cookies')){
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
	}
	
	public static function do_verification($email, $password){
		// I'm going to see if this is the admin trying to log, if not check the db to verify a user.
		$config = new AppConfiguration(null);
		self::$member = null;
		self::$member = Member::findByEmailAndPassword($email, $password);
		if(self::$member !== null){
			self::setAuthKey(self::$member->person->email);
		}
		return self::$member;
	}	
}