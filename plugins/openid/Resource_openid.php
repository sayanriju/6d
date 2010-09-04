<?php
class_exists('Random') || require('lib/Random.php');

// models
class OpenidRequest extends Object{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		if($this->ns === null){
			$this->ns = 'http://specs.openid.net/auth/2.0';
		}
	}
	public function __destruct(){
		parent::__destruct();
	}
	private $id;
	public function getId(){
		return $this->id;
	}
	public function setId($val){
		$this->id = $val;
	}

	private $op_endpoint;
	public function getOp_endpoint(){
		return $this->op_endpoint;
	}
	public function setOp_endpoint($val){
		$this->op_endpoint = $val;
	}

	private $assoc_type;
	public function getAssoc_type(){
		return $this->assoc_type;
	}
	public function setAssoc_type($val){
		$this->assoc_type = $val;
	}
	
	private $mac_key;
	public function getMac_key(){
		return $this->mac_key;
	}
	public function setMac_key($val){
		$this->mac_key = $val;
	}
	
	private $expires;
	public function getExpires(){
		return $this->expires;
	}
	public function setExpires($val){
		$this->expires = $val;
	}
	
	private $owner_id;
	public function getOwner_id(){
		return $this->owner_id;
	}
	public function setOwner_id($val){
		$this->owner_id = $val;
	}

	private $mode;
	public function getMode(){
		return $this->mode;
	}
	public function setMode($val){
		$this->mode = $val;
	}

	private $identity;
	public function getIdentity(){
		return $this->identity;
	}
	public function setIdentity($val){
		$this->identity = $val;
	}

	private $return_to;
	public function getReturn_to(){
		return $this->return_to;
	}
	public function setReturn_to($val){
		$this->return_to = $val;
	}
		
	private $response_nonce;
	public function getResponse_nonce(){
		return $this->response_nonce;
	}
	public function setResponse_nonce($val){
		$this->response_nonce = $val;
	}
	
	private $assoc_handle;
	public function getAssoc_handle(){
		return $this->assoc_handle;
	}
	public function setAssoc_handle($val){
		$this->assoc_handle = $val;
	}
	
	private $invalidate_handle;
	public function getInvalidate_handle(){
		return $this->invalidate_handle;
	}
	public function setInvalidate_handle($val){
		$this->invalidate_handle = $val;
	}
	
	private $claimed_id;
	public function getClaimed_id(){
		return $this->claimed_id;
	}
	public function setClaimed_id($val){
		$this->claimed_id = $val;
	}
	
	private $ns;
	public function getNs(){
		return $this->ns;
	}
	public function setNs($val){
		$this->ns = $val;
	}
	
	private $session_type;
	public function getSession_type(){
		return $this->session_type;
	}
	public function setSession_type($val){
		$this->session_type = $val;
	}
	private $is_valid;
	public function getIs_valid(){
		return $this->is_valid;
	}
	public function setIs_valid($val){
		$this->is_valid = $val;
	}
	
	private $dh_modulus;
	public function getDh_modulus(){
		return $this->dh_modulus;
	}
	public function setDh_modulus($val){
		$this->dh_modulus = $val;
	}
	
	private $dh_gen;
	public function getDh_gen(){
		return $this->dh_gen;
	}
	public function setDh_gen($val){
		$this->dh_gen = $val;
	}

	private $dh_consumer_public;
	public function getDh_consumer_public(){
		return $this->dh_consumer_public;
	}
	public function setDh_consumer_public($val){
		$this->dh_consumer_public = $val;
	}	
	
	private $dh_server_public;
	public function getDh_server_public(){
		return $this->dh_server_public;
	}
	public function setDh_server_public($val){
		$this->dh_server_public = $val;
	}	
	
	private $private_key;
	public function getPrivate_key(){
		return $this->private_key;
	}
	public function setPrivate_key($val){
		$this->private_key = $val;
	}	
	
	private $realm;
	public function getRealm(){
		return $this->realm;
	}
	public function setRealm($val){
		$this->realm = $val;
	}	
	
	public static function findByAssocHandle($assoc_handle, $current_time_in_seconds){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$clause = new ByClause(sprintf("assoc_handle='%s' and expires > %s", $assoc_handle, $current_time_in_seconds), null, 1, null);
		$obj = $db->find($clause, new OpenidRequest(null));
		return $obj;
	}
	public static function save($openid_request){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		try{
			$new_openid_request = $db->save(null, $openid_request);			
		}catch(DSException $e){
			$openid_request->install($config);
			$new_openid_request = $db->save(null, $openid_request);
		}
		$openid_request->id = $new_openid_request->id;
		self::notify('didSaveOpenidRequest', $openid_request, $openid_request);
		return $openid_request;
	}
	public function getTableName($config = null){
		if($config == null){
			$config = new AppConfiguration();
		}
		return $config->prefix . 'openid_requests';
	}
	
	public function install(Configuration $config){
		$message = '';
		$db = Factory::get($config->db_type, $config);
		try{
			$table = new Table($this->getTableName($config), $db);
			$table->addColumn('id', 'biginteger', array('is_nullable'=>false, 'auto_increment'=>true));
			$table->addColumn('op_endpoint', 'string', array('is_nullable'=>false, 'size'=>255));
			$table->addColumn('assoc_handle', 'string', array('is_nullable'=>false, 'size'=>255));
			$table->addColumn('invalidate_handle', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('expires', 'biginteger', array('is_nullable'=>false, 'default'=>0));
			$table->addColumn('owner_id', 'biginteger', array('is_nullable'=>false, 'default'=>1));
			$table->addColumn('assoc_type', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>80));
			$table->addColumn('session_type', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>80));
			$table->addColumn('mode', 'string', array('is_nullable'=>false, 'size'=>80));
			$table->addColumn('identity', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('claimed_id', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('return_to', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('response_nonce', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>80));
			$table->addColumn('ns', 'string', array('is_nullable'=>false, 'size'=>80));
			$table->addColumn('is_valid', 'boolean', array('is_nullable'=>true, 'default'=>0));
			$table->addColumn('mac_key', 'string', array('is_nullable'=>true, 'size'=>255));
			$table->addColumn('dh_modulus', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('dh_gen', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('dh_consumer_public', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('dh_server_public', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			$table->addColumn('private_key', 'string', array('is_nullable'=>false, 'size'=>255));
			$table->addColumn('realm', 'string', array('is_nullable'=>true, 'default'=>null, 'size'=>255));
			
			$table->addKey('primary', 'id');
			$table->addKey('key', array('assoc_handle_key'=>'assoc_handle'));
			$table->addKey('key', array('owner_id_key'=>'owner_id'));
			$table->addKey('key', array('op_endpoint_key'=>'op_endpoint'));
			$table->addOption('ENGINE=MyISAM DEFAULT CHARSET=utf8');
			$errors = $table->save();
			if(count($errors) > 0){
				foreach($errors as $error){
					$message .= $error;
				}
				throw new Exception($message);
			}
		}catch(Exception $e){
			$db->deleteTable($this->getTableName($config));
			throw $e;
		}
	}
}

class Resource_openid{
	public function __construct(){}
	public function __destruct(){}
	public function canHandle($class_name, $http_method){
		return in_array($class_name, array('OpenidResource'));
	}
	public function execute($class_name, $http_method = 'get', $path_info = null){
		$resource = class_exists($class_name) ? new $class_name() : null;
		if($resource !== null){
			return Resource::sendMessage($resource, $http_method, null);
		}
		return null;
	}
}

// resource.
class OpenidResource extends AppResource{
	public function __construct($url_parts){
		parent::__construct($url_parts);
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$openid_request = new OpenidRequest();
		if(!$db->tableExists($openid_request->getTableName())){
			$openid_request->install($config);
		}
		self::$expires_in = 60*60*1;
	}
	public function __destruct(){}
	
	public static $expires_in;
	public function get($openid_mode){
		error_log('getting ' . $openid_mode . ' ' . json_encode($_REQUEST));
		return $this->dispatch($openid_mode, 'get');
	}
	
	public function post($openid_mode){
		error_log('posting ' . $openid_mode);
		return $this->dispatch($openid_mode, 'post');
	}
	private function dispatch($openid_mode, $http_method){
		$command = new OpenidCommand();
		$mode = $openid_mode;
		if(class_exists('Openid_' . $mode)){
			$mode = 'Openid_' . $mode;
			error_log('dispatching ' . $openid_mode);
			$command = new $mode($openid_mode);
		}
		return $command->$http_method($openid_mode);
	}
	public static function btowc($str){
		if (ord($str[0]) > 127) {
			return "\x00" . $str;
		}
		return dechex($str);
	}	
}

class OpenidCommand{
	public function __construct(){
		$this->expires_in = time() + (60*60*1);
		$this->ns = 'http://specs.openid.net/auth/2.0';
	}
	public $expires_in;
	public $ns;
	public $error_message;
	public $request;
	protected function sendErrorResponse($error_message){
		$response = self::keyValueFormEncode('ns', $this->ns);
		$response .= self::keyValueFormEncode('error', $error_message);
		$response .= self::keyValueFormEncode('contact', '6d Support');
		$response .= self::keyValueFormEncode('reference', FrontController::urlFor('openid'));
		$response .= self::keyValueFormEncode('mode', 'error');
		throw new Exception($response, 400);
	}
	protected function sendDirectResponse($request){
		$reflector = new ReflectionObject($request);
		$name = null;
		$response = null;
		foreach($reflector->getMethods() as $method){
			if($method->isPublic() && stripos($method->getName(), 'get') === 0){
				$name = str_replace('get', '', String::toLower($method->getName()));
				$response .= self::keyValueFormEncode('openid.' . $name, $method->invoke());
			}
		}
		return $response;
	}
	protected function sendIndirectResponse(){
		
	}
	public static function keyValueFormEncode($key, $value){
		return $key . ':' . $value . PHP_EOL;
	}
	public static function generateRandomNumber($min, $max){
		return Random::getNumber($min, $max, 1);
	}
	protected static function generateSecret(){
		$g = Random::getNumber(1, 256);
		$p = Random::getNumber(257, 524);
		$k = Random::getNumber(1, $p-1);
		$secret = ($g^$k) % $p;
		return $secret;
	}
	protected function buildUrl($base, $params){
		$base = (strpos($base, '?') !== false) ? '&' : '?';
		$base .= implode('&', $params);
		return $base;
	}
	protected function makeNegativeAssertion(){
		
	}
	protected function makePositiveAssertion($request){
		$data = array('openid.ns'=>urlencode($request->ns), 'openid.mode'=>'id_res', 'openid.op_endpoint'=>urlencode(FrontController::urlFor('openid.txt/')));
		$data['openid.signed'] = 'op_endpoint,return_to,response_nonce,assoc_handle';		
		if($request->claimed_id !== null && $request->identity !== null){
			$data['openid.claimed_id'] = urlencode($request->claimed_id);
			$data['openid.identity'] = urlencode($request->identity);
			$data['openid.signed'] .= ',claimed_id,identity';
		}
		$data['openid.return_to'] = urlencode($request->identity->return_to);
		$data['openid.response_nonce'] = gmdate("Y-m-d\TH:i:s\Z").session_id();
		if($this->request->invalidate_handle !== null){
			$data['openid.invalidate_handle'] = $this->request->invalidate_handle;
		}
		$data['openid.assoc_handle'] = $this->request->assoc_handle;
		$data['openid.sig'] = base64_encode($this->generateMessageSignature($data['openid.signed']));
		FrontController::setNeedsToRedirectRaw($this->buildUrl($data['openid.return_to'], $data));
	}
	protected function generateMessageSignature($signed, $request){
		$data = null;
		$signed = String::explodeAndTrim($signed);
		foreach($signed as $key){
			$data .= self::keyValueFormEncode($key, $request->$key);
		}
		$sig = $this->sign($data, $request->secret, $request->assoc_type);
		return $sig;
	}
	protected function getSig($signed_keys){
		$params = self::getOpenidParamsFromRequest();
		$params['openid.signed'] = implode(',', $signed_keys);
		$data = null;
		foreach($signed_keys as $key){
			$data .= self::keyValueFormEncode($key, $this->request->$key);
		}
		error_log('data ' . $data);
		error_log('secret ' . $this->request->secret);
		error_log('assoc_type ' .  $this->request->assoc_type);
		$sig = $this->sign($data, $this->request->secret, $this->request->assoc_type);
		return $sig === null ? null : $sig;
	}

	protected function sign($data, $secret, $assoc_type){
		$openid_sig = null;
		if($assoc_type == 'HMAC-SHA256'){
			$openid_sig = hash_hmac('sha256', $data, $secret);
		}else if($assoc_type == 'HMAC-SHA1'){
			$openid_sig = hash_hmac('sha1', $data, $secret);
		}
		return $openid_sig;
	}
	
}

class Openid_checkid_immediate extends OpenidCommand{
	public function __construct(){
		parent::__construct();
	}
	public function get($openid_mode){
		$request = new OpenidRequest(array('ns'=>$this->ns, 'mode'=>'id_res', 'identity'=>self::request('openid_identity'), 'claimed_id'=>self::request('openid_claimed_id'), 'assoc_handle'=>self::request('openid_assoc_handle'), 'return_to'=>self::request('openid_return_to'), 'realm'=>self::request('openid_realm')));
		
		if($request->identity === null){
			$request->identity = $request->claimed_id;
		}
		if($request->identity === 'http://specs.openid.net/auth/2.0/identifier_select'){
			$request->identity = FrontController::urlFor(null);
		}
		
		if($request->realm === null){
			$request->realm = $request->return_to;
		}
		
		
	}
}

class Openid_check_authentication extends OpenidCommand{
	public function __construct(){
		parent::__construct();
	}
	public function post($openid_mode){
		
	}
}
class Openid_associate extends OpenidCommand{
	public function __construct(){
		parent::__construct();
	}
	
	public static function generateEncryptedSharedKey($request){
		$shared_key = self::generateSharedKey($request);		
		if($request->session_type == 'DH-SHA256'){
			$shared_key = hash_hmac('sha256', $shared_key, $request->private_key);
		}else if($request->session_type == 'DH-SHA1'){
			$shared_key = hash_hmac('sha1', $shared_key, $request->private_key);
		}else{
			throw new Exception("no encription is not supported right now");
		}
		return base64_encode($shared_key);
	}
	public static function generateSharedKey($request){
 		$shared_key = pow($request->dh_consumer_public, $request->private_key) % $request->dh_modulus;
		return base64_encode($shared_key);
	}
	private static function generatePrivateKey($request){
		return self::generateRandomNumber(1, $request->dh_modulus - 1);
	}
	private static function generatePublicKey($request){
		return pow($request->dh_gen, $request->private_key) % $request->dh_modulus;
	}
	public function post($openid_mode){
		$session_type = self::request('openid_session_type');
		if(in_array($session_type, array('DH-SHA1', 'DH-SHA256'))){
			$request = new OpenidRequest(array('ns'=>$this->ns, 'mode'=>'id_res', 'assoc_handle'=>session_id(), 'session_type'=>$session_type, 'assoc_type'=>self::request('openid_assoc_type'), 'expires_in'=>$this->expires_in, 'dh_modulus'=>self::request('openid_dh_modulus'), 'dh_gen'=>self::request('openid_dh_gen'), 'dh_consumer_public'=>self::request('openid_dh_consumer_public')));
			$request->private_key = self::generatePrivateKey($request);
			$request->enc_mac_key = self::generateEncryptedSharedKey($request);
			$request->dh_server_public = self::generatePublicKey($request);
		}else if($session_type == 'no-encryption'){
			$request = new OpenidRequest(array('ns'=>$this->ns, 'mode'=>'id_res', 'assoc_handle'=>session_id(), 'session_type'=>$session_type, 'assoc_type'=>self::request('openid_assoc_type'), 'expires_in'=>$this->expires_in));
			$request->mac_key = self::generateSharedKey($request);
		}else{
			$request = new OPenidRequest(array('ns'=>$this->ns, 'mode'=>'id_res', 'error'=>"This session type is not supported", 'session_type'=>'DH-SHA256', 'assoc_type'=>'HMAC-SHA256', 'error_code'=>'unsupported-type'));
		}
		OpenidRequest::save($request);
		$request->dh_modulus = null;
		$request->dh_gen = null;
		$request->dh_consumer_public = null;
		return $this->sendDirectResponse($request);
	}
}
