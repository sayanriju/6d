<?php
class_exists("Member") || require("models/Member.php");
class AppResource extends Resource{	
	public function __construct(){
		parent::__construct();
		if(self::$member === null){
			if(strpos($_REQUEST["r"], "members/") !== false){
				$parts = explode("/", $_REQUEST["r"]);
				$member_name = $parts[1];
				self::$member = Member::find_by_name($member_name);
			}else{
				self::$member = Member::find_owner();
			}
		}
		
		if (array_key_exists('PHP_AUTH_DIGEST', $_SERVER) && !AuthController::is_authed()){
			$data = String::to_array($_SERVER['PHP_AUTH_DIGEST']);
			/* My host runs PHP as a CGI and so I added:
				
				RewriteCond %{HTTP:Authorization} !^$
				RewriteRule .* - [E=PHP_AUTH_DIGEST:%{HTTP:Authorization},L]
				
				to the .htaccess file and when I did that, PHP_AUTH_DIGEST was set
				but the username key in the array was now "Digest username".
			*/
			if(array_key_exists('Digest username', $data)){
				$data['username'] = $data['Digest username'];
			}

			$data['username'] = str_replace('"', '', $data['username']);
			$data['response'] = str_replace('"', '', $data['response']);
			$data['realm'] = str_replace('"', '', $data['realm']);
			$data['nonce'] = str_replace('"', '', $data['nonce']);
			$data['uri'] = str_replace('"', '', $data['uri']);
			$data['opaque'] = str_replace('"', '', $data['opaque']);
			$data['cnonce'] = str_replace('"', '', $data['cnonce']);
			$data['nc'] = str_replace('"', '', $data['nc']);
			$data['qop'] = str_replace('"', '', $data['qop']);
			if(isset($data['username'])){
				$user_name = $data['username'];
				$user = Member::find_by_name($user_name);
				$a1 = md5($data['username'] . ':' . $data['realm'] . ':' . $user->password);
				$a2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				$encrypted_response = md5($a1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$a2);
				if ($data['response'] === $encrypted_response){
					$expiry = time() + 60*60*24*30;
					$hash = AuthController::get_chin_auth_hash($user->name, $expiry);
					AuthController::set_chin_auth($hash, $expiry);
					$user->hash = $hash;
					$user->expiry = $expiry;
					save_object::execute($user);
				}				
			}
		}
		NotificationCenter::add($this, "file_not_found");
	}
	
	public static function url_for_member($url, $data = null){
		return App::url_for(self::$member !== null  && !self::$member->is_owner ? self::$member->name . "/" . $url : $url, $data);
	}
	public static function url_for_user($url, $data = null){
		return App::url_for(AuthController::$current_user !== null  && !AuthController::$current_user->is_owner ? AuthController::$current_user->name . "/" . $url : $url, $data);
	}
	public static function begin_request($publisher, $info){
		self::$member = Member::find_by_name($info->resource_name);
		if(self::$member !== null){
			if($info->path !== null){
				$info->resource_name = array_shift($info->path);
			}else{
				$info->resource_name = "index";
			}
		}else{			
			self::$member = Member::find_owner();
		}
	}
	public function __destruct(){
		parent::__destruct();
	}

	public static $member;
	public $page;
}