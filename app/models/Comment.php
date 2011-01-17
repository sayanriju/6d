<?php
class Comment extends Object{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		$this->author = new Person(null);
	}
	public function __destruct(){
		parent::__destruct();
	}
	
	private static $comment_select_statement = 'select c.id, c.post_id, c.body, c.created, c.source, c.owner_id, p.url, p.name, p.email, p.profile from %s c inner join %s p on p.url = c.source';
	
	private $id;
	public function getId(){
		return $this->id;
	}
	public function setId($val){
		$this->id = $val;
	}
	
	private $author;
	public function getAuthor(){
		return $this->author;
	}
	public function setAuthor(Person $val){
		$this->author = $val;
	}
	private $created;
	public function getCreated(){
		return $this->created;
	}
	public function setCreated($val){
		$this->created = $val;
	}
		
	private $post_id;
	public function getPost_id(){
		return $this->post_id;
	}
	public function setPost_id($val){
		$this->post_id = $val;
	}
	private $source;
	public function getSource(){
		return $this->source;
	}
	public function setSource($val){
		$this->source = $val;
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
	
	public function should_insert_id(){
		return true;
	}
	public function will_add_field_to_save_list($name, $value){
		if($name === 'id' && ($this->id === null || strlen($this->id) === 0)){
			$this->id = uniqid(null, true);
			return $this->id;
		}
		return $value;			
	}
	
	public static function find_by_post_id($id, $owner_id){
		$config = new AppConfiguration();				
		$db = Factory::get($config->db_type, $config);
		$owner_id = (int)$owner_id;
		$comment = new Comment();
		$person = new Person();
		$id = String::sanitize($id);
		$clause = new All(sprintf(self::$comment_select_statement . " where c.post_id='%s' and c.owner_id=%d", $comment->getTableName($config), $person->getTableName($config), $id, $owner_id), null, 0, array('c.created'=>'desc'));
		$comments = $db->find($clause, new Comment(null));
		return $comments;
	}
	public function getTableName($config = null){
		if($config == null){
			$config = new AppConfiguration();
		}
		return $config->prefix . 'comments';
	}
	public static function canSave(Comment $comment){
		if($comment->post_id == null) return array('post_id'=>"This comment needs a post.");
		return array();
	}
	public static function save(Comment $comment){
		$errors = self::canSave($comment);
		$config = new AppConfiguration();
		if(count($errors) == 0){
			$db = Factory::get($config->db_type, $config);
			$comment = $db->save(null, $comment);
			self::notify('comment_was_saved', $comment, $comment);
		}
		return array($comment, $errors);
	}
	
	public function install($config){
		$message = '';
		$db = Factory::get($config->db_type, $config);
		try{
			$table = new Table($this->getTableName($config), $db);
			$table->addColumn('id', 'string', array('is_nullable'=>false, 'size'=>255));
			$table->addColumn('post_id', 'string', array('is_nullable'=>true, 'size'=>255));
			$table->addColumn('body', 'text', array('is_nullable'=>true, 'default'=>''));
			$table->addColumn('created', 'datetime', array('is_nullable'=>false));
			$table->addColumn('source', 'string', array('is_nullable'=>false, 'size'=>255));
			$table->addColumn('owner_id', 'biginteger', array('is_nullable'=>false));
			
			$table->addKey('primary', 'id');
			$table->addKey('key', array('owner_id_key'=>'owner_id'));
			$table->addKey('key', array('source_key'=>'source'));
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
