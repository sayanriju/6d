<?php
	class_exists('MySqlRelationship') || require('MySqlRelationship.php');
	class MySqlIsA extends MySqlRelationship{
		public function __construct($args){
			parent::__construct($args);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public function joinStatement($obj){
			$joinToTableName = $obj->getTableName();
			if(method_exists($this->withWhom, 'getTableName')){
				$tableName = $this->withWhom->getTableName();
			}else{
				throw new Exception("Cannot create a join statement if the object doesn't specify it's table name by implementing getTableName().");
			}
			$key = method_exists($this->withWhom, 'getPrimaryKey') ? $this->withWhom->getPrimaryKey() : 'id';
			return sprintf("inner join %s on %s.%s=%s.%s", $tableName, $tableName, $key, $joinToTableName, $this->through);			
		}
	}
?>