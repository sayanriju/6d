<?php
class_exists('Person') || require('Person.php');
class Member extends Object{
	public function __construct(Person $person = null, $attributes = null){
		parent::__construct($attributes);
		$this->person = $person === null ? new Person() : $person;
	}
	public function __destruct(){
		parent::__destruct();
	}
	private static $person_select_statement = 'select m.member_name, m.id, m.person_id, p.uid, p.url, p.session_id, p.public_key, p.name, p.email, p.password, p.is_approved, p.is_owner, p.do_list_in_directory, p.profile, p.owner_id, m.member_name from %s m inner join %s p on p.id = m.person_id';
	
	private $person;
	public function getPerson(){
		return $this->person;
	}
	public function setPerson(Person $val){
		$this->person = $val;
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
	
	private $member_name;
	public function getMember_name(){
		return $this->member_name;
	}
	public function setMember_name($val){
		$this->member_name = $val;
	}
	
	public function getTableName($config = null){
		if($config == null){
			$config = new AppConfiguration();
		}
		return $config->prefix . 'members';
	}
	public static function delete(Member $member){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		if($member->person_id > 0){
			Person::delete(new Person(array('id'=>$member->person_id)));
		}
		return $db->delete(null, $member);
	}
	public static function save(Member $member){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$new_member = $db->save(null, $member);
		$member->id = $new_member->id;
		self::notify('didSaveMember', $member, $member);
		return $member;
	}
	
	public static function saveAsPerson(Member $member){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$existing_member = self::findByMemberName($member->member_name);
		if($existing_member !== null && $existing_member->id !== $member->id){
			$member->errors['member_name'] = 'Member already exists';
		}else{
			list($person, $member->errors) = Person::save($member->person);
			$member->person_id = $person->id;
			$new_member = $db->save(null, $member);
			$member->id = $new_member->id;
			self::notify('didSaveMember', $member, $member);
		}
		return $member;
	}
	public static function findPersonByMemberId($id){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$person = new Person();
		$member = new Member();
		$id = (int)$id;
		$clause = new All("select m.member_name, m.id, m.person_id, p.uid, p.url, p.session_id, p.public_key, p.name, p.email, p.password, p.is_approved, p.is_owner, p.do_list_in_directory, p.profile, p.owner_id, m.member_name from {$member->getTableName()} m inner join {$person->getTableName()} p on p.id = m.person_id where m.id = {$id}", null, 1, null);
		$member = $db->find($clause, new Member(null));
		$member = self::unserialize_profile($member);
		return $member;
	}
	public static function findByEmail($email){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$person = new Person();
		$member = new Member();
		$email = String::sanitize($email);
		$clause = new All("select m.id, m.member_name, m.person_id, p.uid, p.url, p.session_id, p.public_key, p.name, p.email, p.password, p.is_approved, p.is_owner, p.do_list_in_directory, p.profile, p.owner_id, m.member_name from {$member->getTableName()} m inner join {$person->getTableName()} p on p.id = m.person_id where p.email = '{$email}'", null, 1, null);
		$member = $db->find($clause, new Member(null));
		$member = self::unserialize_profile($member);
		return $member;
	}
	
	public static function findById($id){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$member = new Member();
		$member = $db->find(new ById($id, null), new Member(null));
		$member = self::unserialize_profile($member);
		return $member;
	}
	
	public static function findByEmailAndPassword($email, $password){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$person = new Person();
		$member = new Member();
		$clause = new All(sprintf(sprintf(self::$person_select_statement, $member->getTableName(), $person->getTableName()) . " where p.email='%s' and p.password='%s'", String::sanitize($email),String::encrypt($password)), null, 1, null);
		$member = $db->find($clause, $member);
		$member = self::unserialize_profile($member);
		return $member;
	}
	
	public static function findByMemberName($member_name){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$person = new Person();
		$member_name = String::sanitize($member_name);
		$member = new Member();
		$clause = new All(sprintf(sprintf(self::$person_select_statement, $member->getTableName(), $person->getTableName()) . " where m.member_name = '%s'", $member_name), null, 1, null);
		$member = $db->find($clause, $member);
		$member = self::unserialize_profile($member);
		return $member;
	}
	public static function findAll(){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$list = $db->find(new All(null, null, 0, null), new Member());
		return $list;
	}
	public static function findAllAsPerson($in_directory){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$member = new Member();
		$person = new Person();
		$clause = null;
		if($in_directory !== null){
			$clause = new All(sprintf(sprintf(self::$person_select_statement, $member->getTableName(), $person->getTableName()) . " where p.do_list_in_directory=%c and p.is_owner = 0", $in_directory), null, 0, null);			
		}else{
			$clause = new All(sprintf(sprintf(self::$person_select_statement, $member->getTableName(), $person->getTableName()) . " where p.is_owner = 0"), null, 0, null);
		}
		$members = $db->find($clause, $member);
		return $members;
	}
	protected static function unserialize_profile($member){
		if($member !== null && $member->person !==null && $member->person->profile !== null){
			$member->person->profile = unserialize($member->person->profile);
		}
		return $member;
	}
	public static function findOwner(){
		$config = new AppConfiguration();
		$db = Factory::get($config->db_type, $config);
		$person = new Person();
		$member = new Member();
		$clause = new All(sprintf(sprintf(self::$person_select_statement, $member->getTableName(), $person->getTableName()) . " where p.is_owner = 1"), null, 1, null);
		$member = $db->find($clause, $member);
		$member = self::unserialize_profile($member);
		return $member;
	}
	
	public function install(Configuration $config){
		$message = '';
		$db = Factory::get($config->db_type, $config);
		try{
			$table = new Table($this->getTableName($config), $db);
			$table->addColumn('id', 'biginteger', array('is_nullable'=>false, 'auto_increment'=>true));
			$table->addColumn('person_id', 'biginteger', array('is_nullable'=>false));
			$table->addColumn('member_name', 'string', array('is_nullable'=>true, 'size'=>255));
			
			$table->addKey('primary', 'id');
			$table->addKey('key', array('person_id_key'=>'person_id'));
			$table->addKey('key', array('member_name_key'=>'member_name'));
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