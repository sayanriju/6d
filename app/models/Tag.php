<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Tag extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->object_id = 0;
		parent::__construct($values);
	}
	public $id;
	public $name;
	public $object_id;
	public $object_type;
}