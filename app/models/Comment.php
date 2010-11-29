<?php
class Comment extends Object{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		$this->author = new Person(null);
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
	
	private $author;
	public function getAuthor(){
		return $this->author;
	}
	
	private $created;
	public function getCreated(){
		return $this->created;
	}
	public function setCreated($val){
		$this->created = $val;
	}
	
	private $updated;
	public function getUpdated(){
		return $this->updated;
	}
	public function setUpdated($val){
		$this->updated = $val;
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
		$id = (float)$id;
		$owner_id = (int)$owner_id;
		$clause = new ByClause(sprintf("post_id='%s' and owner_id=%d", $id, $owner_id), null, 0, null);
		$comments = $db->find($clause, new Comment(null));
		return $comments;
	}
	public function install(Configuration $config){
		$message = '';
		$db = Factory::get($config->db_type, $config);
		try{
			$table = new Table($this->getTableName($config), $db);
			$table->addColumn('id', 'string', array('is_nullable'=>false, 'size'=>255));
			$table->addColumn('post_id', 'string', array('is_nullable'=>true, 'size'=>255));
			$table->addColumn('body', 'text', array('is_nullable'=>true, 'default'=>''));
			$table->addColumn('created', 'datetime', array('is_nullable'=>false));
			$table->addColumn('owner_id', 'biginteger', array('is_nullable'=>false));
			$table->addColumn('updated', 'datetime', array('is_nullable'=>false, 'default'=>'CURRENT_TIMESTAMP', 'extra'=>'on update CURRENT_TIMESTAMP'));
			
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
