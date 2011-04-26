<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Setting extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->owner_id = 0;
		parent::__construct($values);
	}
	public $id;
	public $owner_id;
	public $key;
	public $value;

	public static function find($key, $owner_id){
		$value = Repo::find("select value from settings where key=:key and owner_id=:owner_id", (object)array("owner_id"=>$owner_id, "key"=>$key))->first(new Setting())->value;
		return $value;
	}
}