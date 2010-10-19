<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class Comment extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		private $date;
		public function getDate(){
			return $this->date;
		}
		private $id;
		public function getId(){
			return $this->id;
		}
		public function setId($val){
			$this->id = $val;
		}

		private $post_id;
		public function getPost_id(){
			return $this->post_id;
		}
		public function setPost_id($val){
			$this->post_id = $val;
		}
		
		private $body;
		public function getBody(){
			return $this->body;
		}
		public function setBody($val){
			$this->body = $val;
		}
		private $owner_id;
		public function getOwner_id(){
			return $this->owner_id;
		}
		public function setOwner_id($val){
			$this->owner_id = $val;
		}
		

		private $source;
		public function getSource(){
			return $this->source;
		}
		public function setSource($val){
			$this->source = $val;
		}

		private $created;
		public function getCreated(){
			return $this->created;
		}
		public function setCreated($val){
			$this->created = $val;
		}
		private $comment_date;
		public function getComment_date(){
			return $this->comment_date;
		}
		public function setComment_date($val){
			$this->comment_date = $val;
		}
		
		// I need a way to tell the data storage whether or not to add the id in the sql statement
		// when inserting a new record. This is it. The data storage should default it to false, so
		// if this method doesn't exist, it'll default to false.
		public function shouldInsertId(){
			return true;
		}
		public function willAddFieldToSaveList($name, $value){			
			if($name === 'id' && ($this->id === null || strlen($this->id) === 0)){
				$this->{$name} = uniqid(null, true);
				return $this->{$name};
			}
			return $value;			
		}
		
		public static function findAll(){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new All(null, null, 0, null), new Post());
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}

		public static function find($start, $limit, $sort_by, $sort_by_direction = 'desc', $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$start_limit = null;
			if($limit > 0){
				$start_limit = array($start, $limit);
			}else{
				$start_limit = $limit;
			}
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause("owner_id={$owner_id}", null, $start_limit, array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findById($id, $owner_id){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$clause = new ByClause(sprintf("id='%s' and owner_id=%d", $id, $owner_id), null, 0, null);
			$post = $db->find($clause, new Post(null));
			return $post !== null && count($post) > 0 ? $post[0] : $post;
		}
		
		public static function findByPostId($id, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$comments = $db->find(new ByClause("post_id = '{$id}' and owner_id={$owner_id}", null, 1, null), new Comment(null));
			$comments = $comments !== null && is_object($comments) ? array($comments) : $comments;
			return $comments;
		}
		public function canModify($user){
			return $this->owner_id === $user->person_id;
		}
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'comments';
		}
		public static function delete(Comment $comment){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			return $db->delete(null, $comment);
		}
		public static function save(Comment $comment){
			$errors = self::canSave($comment);
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$db->save(null, $comment);
			self::notify('didSaveComment', $comment, $comment);
			return array($comment, $errors);
		}

		public static function canSave(Comment $comment){
			$errors = array();
			return $errors;
		}
		public function install(Configuration $config){
			$message = '';
			$db = Factory::get($config->db_type, $config);
			try{
				$table = new Table($this->getTableName($config), $db);
				$table->addColumn('id', 'string', array('is_nullable'=>false, 'size'=>255));
				$table->addColumn('post_id', 'string', array('is_nullable'=>true, 'size'=>255));
				$table->addColumn('body', 'text', array('is_nullable'=>true, 'default'=>''));
				$table->addColumn('source', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('comment_date', 'datetime', array('is_nullable'=>true, 'default'=>null));
				$table->addColumn('created', 'datetime', array('is_nullable'=>false));
				$table->addColumn('owner_id', 'biginteger', array('is_nullable'=>false));
				
				$table->addKey('primary', 'id');
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
				$db->deleteTable($this->getTableName($config));
				throw $e;
			}
		}
		
	}
?>