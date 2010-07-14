<?php
class_exists('SuperAdmin') || require('models/SuperAdmin.php');
class_exists('Member') || require('models/Member.php');
class AuthController{
	public function __construct(){}
	public function __destruct(){}
	public static $user;
	public static function isAuthorized(){
		$sessionAuthKey = self::authKey();
		return $sessionAuthKey !== null;
	}
	public static $is_super_admin;
	public static function isSuperAdmin(){
		if($is_super_admin === null && self::authKey() !== null){
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
		session_unset($_SESSION['authKey']);
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