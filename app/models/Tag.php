<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Tag extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->object_id = 0;
		$this->owner_id = 0;
		parent::__construct($values);
	}
	public $id;
	public $name;
	public $object_id;
	public $object_type;
	public $owner_id;
	public static function find_for_contacts($owner_id){
		$owner_id = (int)$owner_id;		
		$sql = "select tags.*, tags.ROWID as id from tags where tags.object_type='contact' and tags.owner_id=:owner_id group by tags.name";
		$query = new Query(new Tag(array("owner_id"=>$owner_id)));
		$contacts = $query->execute(Repo::get_provider(), new Tag(), $sql);
		return $contacts;
	}
}