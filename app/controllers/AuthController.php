<?php
class_exists('SuperAdmin') || require('models/SuperAdmin.php');
class AuthController{
	public function __construct(){}
	public function __destruct(){}
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
	
}