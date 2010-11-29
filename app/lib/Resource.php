<?php
class_exists('Object') || require('Object.php');
class_exists('HttpHeader') || require('HttpHeader.php');
class_exists('String') || require('lib/String.php');
class Resource extends Object{
	public function __construct($attributes = null){
		$this->status = new HttpStatus(200);
		parent::__construct($attributes);
		$this->name = strtolower(String::replace('/Resource/', '', get_class($this)));
		$this->headers = array(new HttpHeader(array('file_type'=>$this->file_type)));
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $status;
	public $output;
	public $name;
	public $resource_css;
	public $title;
	public $description;
	public $keywords;
	public $file_type;
	public $redirect_parameters;
	public $url_parts;
	public $headers;
	public static function pathWithoutExtension($url_part){
		if(stripos($url_part, '.') !== false){
			$parts = explode('.', $url_part);
			array_pop($parts);
			return implode('', $parts);
		}
		return $url_part;
	}
	protected function redirect_to($url, $query_parameters = null, $make_secure = false){
		$this->status = new HttpStatus(303);
		$this->headers[] = new HttpHeader(array('file_type'=>$this->file_type, 'location'=>$url));
	}

	public function render_layout($name, $data = null, $file_type = null){
		if($file_type === null && $this->file_type !== null){
			$file_type = $this->file_type;
		}
		if(!in_array($file_type, array('html', 'xml'))){
			return $this->output;
		}		
		return $this->render('layouts/' . $name, $data, $file_type);
	}
	/* This method is for rendering a view. It's based on the file type and assumes that the file type is html.
	* It also maps the resources properties to the templates in the view like <?php echo $person->name;?> or you can send 
	* in an array that will be exported for the view to use the variables.
	* I've prefixed all variable names with __ to avoid collisions when extracing variables from the array.
	*/
	public function render($file, $data = null, $file_type = null){
		if($file_type === null && $this->file_type !== null){
			$file_type = $this->file_type;
		}
		$file_type = $file_type !== null ? $file_type : 'html';
		if($file != null){
			$full_path = sprintf('%s_%s.php', $file, $file_type);
			if(!in_array($file_type, array('html', 'xml')) && $this->is_layout($file)){
				return $this->output;
			}
			$r = new ReflectionClass(get_class($this));
			$resource_fields = array();
			foreach($r->getProperties() as $property){
				if($property->isPublic()){
					$name = $property->getName();
					$resource_fields[$name] = $this->{$name};
				}
			}
			if(count($resource_fields) > 0){
				extract($resource_fields);
			}
			if($data != null){
				extract($data);
			}
			ob_start();
			$theme_view = App::get_theme_path('views/' . $full_path);
			$default_view = App::get_app_path('views/' . $full_path);
			// phtml is a special file type that I want to provide fallback logic for. If the file type
			// is phtml, then I want to check for a view with that extension but if it doesn't exist, 
			// the code should fall back and load the html view instead. This allows us to use .html views
			// for partial html requests while providing the ability to define a .phtml view specifically.
			// I don't like the way this is coded. Nested if statements are confusing. But it works. I'd like
			// to come up with a more structured way to implement this logic.
			// I've also added the __file_type parameter for situations where you want to render a view inline another
			// view so you can specify a different file type than what's assigned for the resource.						
			if($file_type === 'phtml'){
				if(file_exists($theme_view)){
					require($theme_view);
				}else if(file_exists($default_view)){
					require($default_view);
				}else{
					$phtml_theme_view = String::replace('/\_phtml/', '_html', $theme_view);
					$phtml_default_view = String::replace('/\_phtml/', '_html', $default_view);
					if(file_exists($phtml_theme_view)){
						require($phtml_theme_view);
					}else if(file_exists($phtml_default_view)){
						require($phtml_default_view);
					}else{
						$this->status = new HttpStatus(404);
					}
				}
			}else if($file_type === 'phtml'){
				$phtml_theme_view = String::replace('/\_html/', '_' . $file_type, $theme_view);
				$phtml_default_view = String::replace('/\_html/', '_' . $file_type, $default_view);
				if(file_exists($phtml_theme_view)){
					require($phtml_theme_view);
				}else if(file_exists($phtml_default_view)){
					require($phtml_default_view);
				}else{
					if(file_exists($theme_view)){
						require($theme_view);
					}else if(file_exists($default_view)){
						require($default_view);
					}else{
						$this->status = new HttpStatus(404);
					}
				}
			}else if(file_exists($theme_view)){				
				require($theme_view);				
			}else if(file_exists($default_view)){				
				require($default_view);
			}else{
				$this->status = new HttpStatus(404);
			}
			$this->output = ob_get_contents();
			ob_end_clean();
			if($this->is_layout($file) && method_exists($this, 'output_has_rendered')){
				$this->output = $this->output_has_rendered($file, $this->output);
			}
			if($file_type == 'json'){
				$this->output = String::replace('/\n|\t/', '', $this->output);
				$this->output = String::replace('/\"/', '"', $this->output);
			}
			if(count($this->headers) === 0){
				$this->headers[] = new HttpHeader(array('file_type'=>$file_type));
			}
		}
		return $this->output;
	}
	private function is_layout($file){
		return strpos($file, 'layouts/') !== false;
	}
	public function call_with($method, $url_parts, $env){
		if(method_exists($this, $method)){
			$method_info = new ReflectionMethod($this, $method);
			return $this->call_method($method_info, $url_parts, $env);
		}else{
			return null;
		}
	}
	private function call_method($method_info, $url_parts, $env){
		if($method_info == null){
			return null;
		}
		$parm_count = $method_info->getNumberOfParameters();
		if($parm_count === 0){
			return $this->{$method_info->getName()}();
		}
		$parms = array();
		if(count($url_parts) > 0){
			foreach($url_parts as $value){
				if(strlen($value) > 0 && count($parms) < $parm_count){
					$parms[] = $value;					
				}
			}
		}
		
		if(count($parms) < $parm_count){
			$method_parameters = $method_info->getParameters();
			foreach($method_parameters as $parameter){
				$parms[] = self::populateParameter($parameter);
			}
		}
		$output = null;
		$output = $method_info->invokeArgs($this, $parms);
		return $output;
	}
	public static function sendMessage($obj, $message, $resource_id = 0){
		$class_name = get_class($obj);
		$reflector = new ReflectionClass($class_name);
		$args = array();
		if($reflector->hasMethod($message)){
			$method = $reflector->getMethod($message);
			$numberOfParams = $method->getNumberOfParameters();
			if($numberOfParams > 0){
				$params = $method->getParameters();
				foreach($params as $param){
					$arg = self::populateParameter($param, $resource_id);
					if($arg != null){
						$args[] = $arg;
					}elseif($param->isDefaultValueAvailable()){
						$args[] = $param->getDefaultValue();
					}
				}
			}
			$output = $method->invokeArgs($obj, $args);
			return $output;
		}else{
			throw new Exception("404: {$class_name}::<?php echo $message;?> not found.", 404);
		}
	}
	
	public function did_finish_loading(){
		self::setUserMessage(null);
	}
	private static $user_message;
	public static function get_user_message(){
		if(array_key_exists('userMessage', $_COOKIE)){
			self::$user_message = urldecode($_COOKIE['userMessage']);
		}
		return self::$user_message;
	}
	public static function setUserMessage($value){
		self::$user_message = $value;
		if($value == null){
			unset($_COOKIE['userMessage']);
		}else{
			setcookie('userMessage', urlencode($value), time()+1);
		}
	}
	public static function appendToUserMessage($value){
		$_COOKIE['userMessage'] .= $value;
	}
	private static function populateParameter($param, $id = 0){
		$value = null;
		$obj = null;
		$ref_class = null;
		$class_name = null;
		$name = $param->getName();
		$ref_class = null;
		try{
			$ref_class = $param->getClass();
		}catch(Exception $e){}
		if($id > 0 && $name == 'id'){
			return $id;
		}
		if(array_key_exists($name, $_FILES)){
			$obj = $_FILES[$name];
		}elseif(array_key_exists($name, $_REQUEST)){
			$value = $_REQUEST[$name];
			// 2009-08-26, jguerra: Arrays are used to populate 2 different types of parameters. The 1st is to populate
			// a parameter that's an object. Where the key is the object's property name; e.g an input field name='user[name]' 
			// maps to a parameter called $user which is an instance of a class User with a public property called $name.
			// This logic should populate $user->name = the value in $_REQUEST['user[name]'];
			// The 2nd situation is for an input field name='photo_names[]'. This code should look for a parameter named 
			// photo_names that is an array data type and populate it with the values from $_REQUEST['photo_names'].
			if(is_array($value)){				
				// This block is for the situation where the parameter is an object, not an array.
				if($ref_class != null){
					$class_name = $ref_class->getName();
					$obj = new $class_name(null);
					$obj = self::initWithArray($obj, $value);
				}else{
					// and this block is for the situation where the value from the request is an indexed array.
					foreach($value as $key=>$val){
						$value[$key] = self::sanitize_magic_quotes($val);
					}
					$obj = $value;
				}
			}else{
				$obj = self::valueWithCast(self::sanitize_magic_quotes($value), ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null));
			}
		}else{
			// This else block handles the case where you want to populate an object with a form that has the object property names as their field names. For instance, I want to save a "post" and the form field names match the attributes on a Post object.			
			if($ref_class != null){
				$obj = $ref_class->newInstance(null);
				$is_null = true;
				foreach($_REQUEST as $key=>$value){
					//if($ref_class->hasProperty($key)){
					//	$prop = $ref_class->getProperty($key);
					//	if($prop != null){
							$obj->{$key} = self::valueWithCast(self::sanitize_magic_quotes($value), null);
							$is_null = false;
					//	}
					//}
				}
				// 2009-12-01, jguerra: I want to handle the situation where the id is passed in the url as a path
				// value like user/1. Right now, this code assumes that there's a property called id and it's an integer.
				// I can imagine someone wanting to use a non integer as an identifier and possibly a different name for 
				// the id property. But I'm not coding for that at this time.
				if($id > 0 && $ref_class->hasProperty('id')){
					$prop = $ref_class->getProperty('id');
					if($prop != null){
						$obj->{'id'} = self::valueWithCast(self::sanitize_magic_quotes($id), null);
						$is_null = false;
					}
				}
				
				if($is_null){
					$obj = null;
				}
			}
		}
		return $obj;
	}
	public static function sanitize_magic_quotes($value){
		if(function_exists('get_magic_quotes_gpc')){
			if(get_magic_quotes_gpc()){
				if(is_array($value)){
					array_walk_recursive($value, array('Resource', 'sanitize_magic_quotes'));
				}else{
					$value = stripslashes($value);
				}
			}
		}
		return $value;
	}
	
	// This function initializes an object with an array. It checks for getters and setters that map to the names in the request
	private static function initWithArray($obj, $array){
		$setter = null;
		$getter = null;
		if(!is_array($array)){
			if($array === 'true'){
				$array = true;
			}
			
			if($array === 'false'){
				$array = false;
			}
			return $array;
		}
		
		if($obj != null && is_object($obj)){
			foreach($array as $key=>$value){
				$name = ucwords($key);
				$setter = 'set' . $name;
				$getter = 'get' . $name;
				
				if(method_exists($obj, $setter)){
					if(is_object($obj->{$getter}())){
						$obj->{$key} = self::initWithArray($obj->{$getter}(), $value);
					}else{
						$value = self::sanitize_magic_quotes($value);
						$obj->{$key} = self::initWithArray(null, $value);	
					}
				}
				
				$setter = null;
				$getter = null;
			}
		}else{
			$obj = $array;
		}
		return $obj;
	}	
	
	private static function valueWithCast($value, $attribute_value = null){
		// I have to handle boolean's specifically because checkboxes return on or off.
		// So default to false, then set to true if value = on for checkbox.
		$result = $value;
		if(is_bool($value)){
			return $value;
		}
		
		if($value == 'false'){
			return false;
		}
		
		if($value == 'true'){
			return true;
		}
		if(is_bool($attribute_value) && $value == 'on'){
			$result = true;
		}elseif($value == 'true' || $value == 'false'){
			$result = ($value == 'true');
		}
		
                if(is_numeric($value)){
                    if(strpos($value, '.') === false){
                        $result = (int)$value;
                    }else{
                        $result = (float)$value;
                    }
                }
                return $result;
	}
	
}

?>