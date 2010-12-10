<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('Post') || require('Post.php');
	class FriendRequest extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
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

		private $name;
		public function getName(){
			return $this->name;
		}
		public function setName($val){
			$this->name = $val;
		}

		private $email;
		public function getEmail(){
			return $this->email;
		}
		public function setEmail($val){
			$this->email = $val;
		}
		
		private $url;
		public function getUrl(){
			return $this->url;
		}
		public function setUrl($val){
			$this->url = $val;
		}
		private $owner_id;
		public function getOwner_id(){
			return $this->owner_id;
		}
		public function setOwner_id($val){
			$this->owner_id = $val;
		}
		
		private $created;
		public function getCreated(){
			return $this->created;
		}
		public function setCreated($val){
			$this->created = $val;
		}
		
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'friend_requests';
		}
		public static function delete(FriendRequest $request){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			return $db->delete(null, $request);
		}
		public static function save(FriendRequest $request){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$new_request = $db->save(null, $request);
			$request->id = $new_request->id;
			self::notify('didSaveFriendRequest', $request, $request);
			return $request;
		}
		public static function get_total_friend_requests($owner_id){
			$owner_id = (int)$owner_id;
			$config = new AppConfiguration();
			$request = new FriendRequest(null);
			$request_count = (object) array('number'=>0);
			$db = Factory::get($config->db_type, $config);
			$request_count = $db->find(new All(sprintf("select count(*) as number from %s where owner_id=%u", $request->getTableName(), $owner_id), null, 1, null), $request_count);
			$request_count->number = (int)$request_count->number;
			return $request_count;
		}
		
		public static function findAllForOwner($owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause(sprintf("owner_id=%u", $owner_id), null, 0, null), new FriendRequest());
			return $list;
		}
		public static function findAll(){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new All(null, null, 0, null), new FriendRequest());
			return $list;
		}
		
		public static function findByIdAndOwnerId($id, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause(sprintf("id=%d and owner_id=%d", $id, $owner_id), null, 1, null), new FriendRequest());
			return $list;
		}
		public static function findByUrlAndOwnerId($url, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause(sprintf("url='%s' and owner_id=%d", $url, $owner_id), null, 1, null), new FriendRequest());
			return $list;
		}
		
		public static function findByEmail($email){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new ByAttribute('email', $email, 1, null), new FriendRequest());
			return $list;
		}
		
		public static function canSave(FriendRequest $request){
			$errors = array();

			if(empty($request->name)){
				$errors[] = 'Name is required.';
			}
			if(empty($request->url)){
				$errors[] = 'Url is required.';
			}
			
			return $errors;
		}
		
		public function install(Configuration $config){
			$message = '';
			$db = Factory::get($config->db_type, $config);
			error_log('installing friend_requests');
			try{
				$table = new Table($this->getTableName($config), $db);
				$table->addColumn('id', 'biginteger', array('is_nullable'=>false, 'auto_increment'=>true));
				$table->addColumn('email', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('name', 'string', array('is_nullable'=>false, 'size'=>255));
				$table->addColumn('url', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('owner_id', 'biginteger', array('is_nullable'=>false));
				$table->addColumn('created', 'datetime', array('is_nullable'=>false));
				$table->addKey('primary', 'id');
				$table->addKey('key', array('url'=>'url'));
				$table->addKey('key', array('email'=>'email'));
				$table->addKey('key', array('owner_id_key'=>'owner_id'));
				
				$table->addOption('ENGINE=MyISAM DEFAULT CHARSET=utf8');
				$errors = $table->save();
				if(count($errors) > 0){
					foreach($errors as $error){
						$message .= $error;
					}
					throw new Exception($message);
				}
			}catch(Exception $e){
				$db->deleteTable($this->tableName);
				throw $e;
			}
		}
		
	}
?>