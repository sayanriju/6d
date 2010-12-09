<?php
class_exists('AppResource') || require('resources/AppResource.php');
class_exists('AuthController') || require('controllers/AuthController.php');
class_exists('ProfileResource') || require('resources/ProfileResource.php');
class_exists('Member') || require('models/Member.php');
class_exists('ProfileController') || require('controllers/ProfileController.php');
class Application{
	public function __construct(){
		if(file_exists(App::get_root_path('AppConfiguration.php'))){
			class_exists('AppConfiguration') || require('AppConfiguration.php');
		}
		if (array_key_exists('PHP_AUTH_DIGEST', $_SERVER) && !AuthController::authKey()){
			$data = String::toArray($_SERVER['PHP_AUTH_DIGEST']);
			if(class_exists('AppConfiguration')){
				self::$config = new AppConfiguration();
			}else{
				self::$config = new Object();
			}
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
			if(isset($data['username']) && self::$config->email === $data['username']){
				$a1 = md5($data['username'] . ':' . $data['realm'] . ':' . self::$config->site_password);
				$a2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				$encrypted_response = md5($a1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$a2);
				if ($data['response'] === $encrypted_response){
					AuthController::setAuthKey($data['username']);
				}				
			}
		}
		if(class_exists('AppConfiguration') && AuthController::authKey() !== null){
			self::$current_user = Member::findByEmail(AuthController::authKey());
		}
		
	}
	public function __destruct(){}
	public static $member;
	public static $current_user;
	private static $config;
	public function get_theme(){
		if(!class_exists('AppConfiguration')) return null;
		self::$config = new AppConfiguration();
		return self::$config->getTheme();
	}
	public static function isPhotoPublic(){
		return true;
	}
	public static function url_with_member($resource_name, $params = null, $make_secure = false){
		if(!self::$member->is_owner){
			return App::url_for(self::$member->member_name . '/' . $resource_name, $params, $make_secure);			
		}else{
			return App::url_for($resource_name, $params, $make_secure);
		}
	}
	public function exception_has_happened($sender, $args){
		$e = $args['exception'];
		$file_type = $args['file_type'];
		$resource = new AppResource(array('file_type'=>$file_type));
		if($e->getCode() == 401){
			$resource->status = new HttpStatus(401);
			$resource->headers[] = new HttpHeader(array('location'=>App::url_for('login')));
		}elseif($e->getCode() == 404){
			$resource->output = $resource->render('error/404', array('message'=>$e->getMessage()));
			$this->status = new HttpStatus(404);
			return $resource->render_layout('default');
		}elseif(strpos($e->getMessage(), 'No database selected') !== false || get_class($e) == 'DSException'){
			Resource::setUserMessage($e->getMessage() . ' - You need to create the database first.');
			$resource->output = $resource->render('install/index', array('message'=>$e->getMessage()));
			return $resource->render_layout('install');
		}else{
			Resource::setUserMessage('Exception has occured: ' . $e->getMessage());
			return $resource->render_layout('default');
		}
	}
	public function unauthorized_request_has_happened($sender, $args){
		$resource->status = new HttpStatus(401);
		$resource->headers[] = new HttpHeader(array('location'=>App::url_for('login')));
	}
	public function before_dispatching($parts, $file_type){
		Photo::add_observer(new ProfileController(), 'will_delete_photo', 'Photo');
		if(!class_exists('AppConfiguration')){
			return $parts;
		}
		if($parts !== null){
			if(count($parts) > 0){
				$member_name = $parts[0];
				if(strlen($member_name) > 0){
					self::$member = Member::findByMemberName($member_name);
					if(self::$member !== null){
						 array_shift($parts);					
					}					
				}
			}
		}		
		if(self::$member == null){
			self::$member = Member::findOwner();
		}
		return $parts;
	}
	public function error_has_happened($message){
		console::log($message);
	}
	
	public function file_not_found($url_parts, $file_type){		
		if(!class_exists('AppConfiguration')) return null;
		$resource = new AppResource(array('file_type'=>$file_type,'url_parts'=>$url_parts));
		$method = array_key_exists('_method', $_SERVER) ? $_SERVER['_method'] : $_SERVER['REQUEST_METHOD'];
		$page_name = $url_parts !== null && count($url_parts) > 0 ? $url_parts[0] : null;
		$view = $page_name . '_' . $resource->file_type . '.php';
		$parms = array();
		//TODO: Not sure what to do with this yet. 
		// Check for a date in the URL.
		if(count($url_parts) >= 3 && is_numeric($url_parts[0]) && is_numeric($url_parts[1]) && is_numeric($url_parts[2])){
			if(checkdate($url_parts[1], $url_parts[2], $url_parts[0])){
				$parms = array(date(sprintf('%d/%d/%d', $url_parts[1], $url_parts[2], $url_parts[0])));
				array_shift($url_parts);
				array_shift($url_parts);
				array_shift($url_parts);
			}
		}
		
		if(file_exists(App::get_theme_path('/views/index/' . $view))){
			$resource->output = $resource->render('index/' . $page_name);
		}elseif(file_exists('index/' . $view)){
			$resource->output = $resource->render('index/' . $page_name);
		}else{
			if(AuthController::is_authorized()){
				$post = Post::findByAttribute('custom_url', $page_name, Application::$member->person_id);
			}else{
				$post = Post::findPublishedByCustomUrl($page_name, Application::$member->person_id);
			}
			if($post != null){
				$resource->description = $post->title;
				$resource->keywords = implode(', ', String::getKeyWordsFromContent($post->body));		
				$resource->title = $post->title;
				$resource->output = $resource->render('post/show', array('post'=>$post));
			}else{
				$resource->title = "Not found page";
				$resource->description = "The requested page was not found on this sever.";
				$resource->output = $resource->render('error/404', array('message'=>$method . ' ' . $page_name . ' was not found.'));
				$resource->status = new HttpStatus(404);
			}
			$resource->output = $resource->render_layout('default');
		}
		if($resource->title === null){
			$resource->title = $resource->get_title_from_output($resource->output);				
		}		
		return $resource;
	}
}

class console{
	public static $messages = array();
	public static function log($obj){
		error_log(json_encode($obj));
		self::$messages[] = $obj;
	}
	public function __destruct(){
		self::$messages = array();
	}
	
}