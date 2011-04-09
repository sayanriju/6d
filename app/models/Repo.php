<?php
class Repo{
	public function __construct(){}
	public function __destruct(){
		self::$db = null;
	}
	public static $db;
	public static function get_provider(){
		if(self::$db != null) return self::$db;
		self::$db = new PDO("sqlite:" . Settings::$storage_provider->path);
		return self::$db;
	}
}

class Query{
	public function __construct($obj){
		$this->obj = $obj;
	}
	public $obj;
	public $error_info;
	public function bind($cmd, $query, $properties){
		foreach($properties as $property){
			if($property->isPublic()){
				$name = $property->getName();
				if(strpos($query, ":$name") !== false){
					$value = $this->obj->{$name};
					if(is_bool($value)){
						$value = $value ? 1 : 0;
					}
					// If an error occurred here, it's likely that a property is misspelled.
					$cmd->bindValue(":$name", $value);						
				}
			}
		}
		return $cmd;
	}
	public function execute($db, $query = null){
		$class = null;
		$properties = null;
		$list = array();
		if($this->obj !== null){
			$class = new ReflectionClass($this->obj);			
			$table_name = strtolower(String::pluralize($class->getName()));
			$properties = $class->getProperties();
			$query = $query === null ? $this->build_query($properties, $table_name) : $query;
			$cmd = $db->prepare($query);
			$cmd = $this->bind($cmd, $query, $properties);
			$result = $cmd->execute();
			$this->error_info = $db->errorInfo();
			if($this->error_info[0] !== "00000"){
				throw new Exception($this->error_info[0] . ":" . $this->error_info[2], $this->error_info[1]);
			}
			if($class !== null){
				$class_name = $class->getName();
				while($this->obj = $cmd->fetchObject()){
					$list[] = ModelFactory::populate_single((object)$this->obj, new $class_name());
				}
			}		
			$cmd = null;
			return $list;
		}
		
		$result = $db->query($query);
		$this->error_info = $db->errorInfo();
		if($this->error_info[0] !== "00000"){
			throw new Exception($this->error_info[0] . ":" . $this->error_info[2], $this->error_info[1]);
		}
		while($row = $result->fetchObject()){
			$list[] = $row;
		}
		return $list;
	}
}
class UpdateQuery extends Query{
	public function __construct($obj){
		parent::__construct($obj);
	}
	public function build_query($properties, $table_name){
		$property = array_pop($properties);
		$name = $property->getName();
		$query = "update $table_name set $name=:$name";		
		while($property = array_pop($properties)){
			$name = $property->getName();
			if($name == "id") continue;
			$query .= ",$name=:$name";
		}		
		$query .= " where ROWID=:id";
		return $query;
	}
}
class InsertQuery extends Query{
	public function __construct($obj){
		parent::__construct($obj);
	}
	public function build_query($properties, $table_name){
		$property = array_pop($properties);
		$name = $property->getName();		
		$query = "insert into $table_name (%s) values (%s)";
		$keys = $name;
		$values = ":$name";
		while($property = array_pop($properties)){
			$name = $property->getName();
			if($name == "id") continue;
			$keys .= ",$name";
			$values .= ",:$name";
		}		
		$query = sprintf($query, $keys, $values);
		return $query;
	}
}
class DeleteQuery extends Query{
	public function __construct($obj){
		parent::__construct($obj);
	}
	public function build_query($properties, $table_name){
		$query = "delete from $table_name where ROWID=:id";
		return $query;
	}
}

class FindQuery extends Query{
	public function __construct($obj, $by){
		parent::__construct($obj);
		$this->by = $by;
	}
	public $by;
	public function build_query($by, $table_name){
		$query = "select ROWID as id, * from $table_name" . ($this->by != null ? " where " . $this->by : null);
		return $query;
	}
	public function execute($db){
		$class = new ReflectionClass($this->obj);
		$table_name = strtolower(String::pluralize($class->getName()));
		$properties = $class->getProperties();
		$query = $this->build_query($this->by, $table_name);
		$cmd = $db->prepare($query);
		if($cmd === false){
			$cmd = NotificationCenter::post("query_failed", $this, (object)array("db"=>$db, "query"=>$query, "obj"=>$this->obj, "cmd"=>$cmd));
		}
		$cmd = $this->bind($cmd, $query, $properties);
		$result = $cmd->execute();
		$this->error_info = $db->errorInfo();
		if($this->error_info[0] !== "00000"){
			throw new Exception($this->error_info[0] . ":" . $this->error_info[2], $this->error_info[1]);
		}
		$list = array();
		$class_name = $class->getName();
		while($this->obj = $cmd->fetchObject()){
			$list[] = ModelFactory::populate_single((object)$this->obj, new $class_name());
		}
		$cmd = null;
		if(count($list) === 0) return null;
		//if(count($list) === 1) return $list[0];
		return $list;
	}
}

class FindQueryWithLimit extends Query{
	public function __construct(FindQuery $query, $page, $limit){
		$this->find_query = $query;
		$this->page = $page;
		$this->limit = $limit;
	}
	private $find_query;	
	private $page;
	private $limit;
	public function build_query($by, $table_name){
		$query = $this->find_query->build_query($by, $table_name);
		$query .= " limit {$this->page}, {$this->limit}";
		return $query;
	}
	public function execute($db){
		$class = new ReflectionClass($this->find_query->obj);
		$table_name = strtolower(String::pluralize($class->getName()));
		$properties = $class->getProperties();
		$query = $this->build_query($this->find_query->by, $table_name);
		$cmd = $db->prepare($query);		
		$this->error_info = $db->errorInfo();
		if($this->error_info[0] !== "00000"){
			throw new Exception($this->error_info[0] . ":" . $this->error_info[2], $this->error_info[1]);
		}
		$cmd = $this->find_query->bind($cmd, $query, $properties);
		$result = $cmd->execute();
		$list = array();
		$class_name = $class->getName();
		while($this->find_query->obj = $cmd->fetchObject()){			
			$list[] = ModelFactory::populate_single((object)$this->find_query->obj, new $class_name());
		}
		$cmd = null;
		return $list;
	}
	
}
class Count{
	public $total;
}
class CountQuery extends Query{
	public function __construct($obj, $by){
		parent::__construct($obj);
		$this->by = $by;
	}
	public $by;
	public function build_query($by, $table_name){
		$query = "select count(1) as total from $table_name where " . $this->by;		
		return $query;
	}
	public function execute($db){
		$class = new ReflectionClass($this->obj);
		$table_name = strtolower(String::pluralize($class->getName()));
		$properties = $class->getProperties();
		$query = $this->build_query($this->by, $table_name);
		$cmd = $db->prepare($query);
		$cmd = $this->bind($cmd, $query, $properties);
		$result = $cmd->execute();
		$this->error_info = $db->errorInfo();
		if($this->error_info[0] !== "00000"){
			throw new Exception($this->error_info[0] . ":" . $this->error_info[2], $this->error_info[1]);
		}
		$list = array();
		$class_name = $class->getName();
		$this->obj = $cmd->fetchObject();
		$count = ModelFactory::populate_single((object)$this->obj, new Count());
		$cmd = null;
		return $count;
	}
}


class save_object extends Repo{
	public static function execute($obj){
		$db = self::get_provider();
		$class = new ReflectionClass($obj);
		$properties = $class->getProperties();
		if((int)$obj->id > 0){
			$query = new UpdateQuery($obj);
		}else{
			$query = new InsertQuery($obj);
		}
		$result = $query->execute($db);
		return $result;
	}
}

class delete_object extends Repo{
	public static function execute($obj){
		$id = (int)$obj->id;
		if($id == 0) return null;
		$db = self::get_provider();
		$query = new DeleteQuery($obj);
		$result = $query->execute($db);
		return $id;
	}
}
class find_count_by extends Repo{
	public static function execute($by, $obj){
		$db = self::get_provider();
		$query = new CountQuery($obj, $by);
		$list = $query->execute($db);
		return $list;
	}
}
class find_by_with_limit extends Repo{
	public static function execute($by, $obj, $page, $limit){
		$db = self::get_provider();
		$query = new FindQueryWithLimit(new FindQuery($obj, $by), $page, $limit);
		$list = $query->execute($db);
		return $list;
	}
}
class find_one_by extends Repo{
	public static function execute($by, $obj){
		$db = self::get_provider();
		$query = new FindQuery($obj, $by);
		$list = $query->execute($db);
		if(count($list) === 0) return null;
		if(count($list) >= 1) return $list[0];
	}
}
class find_by extends Repo{
	public static function execute($by, $obj){
		$db = self::get_provider();
		$query = new FindQuery($obj, $by);
		$list = $query->execute($db);
		return $list;
	}
}
