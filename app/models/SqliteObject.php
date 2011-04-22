<?php

class SqliteObject extends ChinObject{
	public function __construct($values){
		parent::__construct($values);
		$table_name = strtolower(String::pluralize(get_class($this)));
		$this->sql = array("from $table_name");
	}
	protected $sql;
	public function aand(){
		$this->sql[] = "and";
		return $this;
	}
	public function where(){
		$this->sql[] = "where";
		return $this;
	}
	public function select($fields){
		array_unshift($this->sql, "select " . implode(",", $fields));
		return $this;
	}
	public function group_by($fields){
		$this->sql[] = "group by " . implode(",", $fields);
		return $this;
	}
	public function to_one(){
		$query = implode(" ", $this->sql);
		$cmd = Repo::get_provider()->prepare($query);
		if($cmd === false){
			$cmd = NotificationCenter::post("query_failed", $this, (object)array("db"=>Repo::get_provider(), "query"=>$query, "obj"=>$this, "cmd"=>$cmd));
		}
		$class = new ReflectionClass($this);
		$cmd = $this->bind($cmd, $query, $this, $class->getProperties());
		$error_info = Repo::get_provider()->errorInfo();
		if($cmd === false) throw new RepoException($error_info[0] . ":" . $error_info[2], $error_info[1]);
		$result = $cmd->execute();
		$error_info = Repo::get_provider()->errorInfo();
		if($error_info[1] !== null) throw new RepoException($this->error_info[0] . ":" . $error_info[2], $error_info[1]);
		$list = array();
		$obj = $class->newInstance();
		while($obj = $cmd->fetchObject()){
			$list[] = ModelFactory::populate_single((object)$obj, $class->newInstance());
		}
		$cmd = null;
		if(count($list) === 0) return null;
		if(count($list) === 1) return $list[0];
		return null;
	}
	
	public function to_list(){
		$query = implode(" ", $this->sql);
		$cmd = Repo::get_provider()->prepare($query);
		if($cmd === false){
			$cmd = NotificationCenter::post("query_failed", $this, (object)array("db"=>Repo::get_provider(), "query"=>$query, "obj"=>$this, "cmd"=>$cmd));
		}
		$class = new ReflectionClass($this);
		$cmd = $this->bind($cmd, $query, $this, $class->getProperties());
		$error_info = Repo::get_provider()->errorInfo();
		if($cmd === false) throw new RepoException($error_info[0] . ":" . $error_info[2], $error_info[1]);
		$result = $cmd->execute();
		$error_info = Repo::get_provider()->errorInfo();
		if($error_info[1] !== null) throw new RepoException($this->error_info[0] . ":" . $error_info[2], $error_info[1]);
		$list = array();
		$obj = $class->newInstance();
		while($obj = $cmd->fetchObject()){
			$list[] = ModelFactory::populate_single((object)$obj, $class->newInstance());
		}
		$cmd = null;
		if(count($list) === 0) return null;
		//if(count($list) === 1) return $list[0];
		return $list;
	}
	private function bind($cmd, $query, $obj, $properties){
		foreach($properties as $property){
			if($property->isPublic()){
				$name = $property->getName();
				if(strpos($query, ":$name") !== false){
					$value = $obj->{$name};
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
	
}