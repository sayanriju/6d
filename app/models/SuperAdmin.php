<?php
class_exists('Object') || require('lib/Object.php');
class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');

class SuperAdmin extends Object{
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
	private $person_id;
	public function getPerson_id(){
		return $this->person_id;
	}
	public function setPerson_id($val){
		$this->person_id = $val;
	}
	public function getTableName($config = null){
		if($config == null){
			$config = new AppConfiguration();
		}
		return $config->prefix . 'super_admins';
	}
	
	public static function findByEmail($email){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$person = new Person();
		$person = $db->find(new ByClause(sprintf("%s.email='%s'", $person->getTableName(), urlencode($email)), array(new BelongsTo(array('withWhom'=>$person, 'through'=>'person_id'))), 1, null), new SuperAdmin(null));
		return $person;
	}
	
	public static function findAll(){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$list = $db->find(new All(null, null, 0, null), new SuperAdmin());
		return $list;
	}
	
	public static function save(SuperAdmin $super_admin){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);		
		$new_person = $db->save(null, $super_admin);
		$super_admin->id = $new_person->id;
		self::notify('didSaveSuperAdmin', $super_admin, $super_admin);
		return $super_admin;
	}
	
	public function install(Configuration $config){
		$message = '';
		$db = Factory::get($config->db_type, $config);
		try{
			$table = new Table($this->getTableName($config), $db);
			$table->addColumn('id', 'biginteger', array('is_nullable'=>false, 'auto_increment'=>true));
			$table->addColumn('person_id', 'biginteger', array('is_nullable'=>false));
			
			$table->addKey('primary', 'id');
			$table->addKey('key', array('person_id_key'=>'person_id'));
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