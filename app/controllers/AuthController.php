<?php

class AuthController{
	public static $current_user;
	public static function set_current_user(){
		$hash = self::get_chin_auth();
		self::$current_user = find_one_by::execute("hash=:hash and expiry>=:expiry", new Member(array("hash"=>$hash, "expiry"=>time())));
		return self::$current_user;
	}
	public static function get_current_user(){
		if(self::$current_user !== null) return self::$current_user;
		$hash = self::get_chin_auth();
		self::$current_user = find_one_by::execute("hash=:hash and expiry>=:expiry", new Member(array("hash"=>$hash, "expiry"=>time())));
		return self::$current_user;
	}
	public static function is_authed(){
		return self::get_chin_auth() !== null && self::$current_user !== null;
	}
	public static function get_chin_auth_hash($name, $expiry){
		return hash("sha256", $name . $_SERVER["REMOTE_ADDR"] . $expiry, false);
	}
	private static function get_chin_auth(){
		return array_key_exists("chin_auth", $_COOKIE) ? $_COOKIE["chin_auth"] : null;
	}
	public static function set_chin_auth($value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = true){
		setcookie("chin_auth", $value, $expire, $path, $domain, $secure, $httponly);
		$_COOKIE["chin_auth"] = $value;
	}
}

AuthController::$current_user = AuthController::get_current_user();
