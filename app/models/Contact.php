<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Contact extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->owner_id = 0;
		parent::__construct($values);
		$this->sql = array("select contacts.ROWID as id, contacts.* from contacts");
	}
	public $id;
	public $owner_id;
	public $name;
	public $url;
	public $photo_url;
	public $email;
	public $json;
	
	public static function find_tagged($tag, $owner_id){
		$owner_id = (int)$owner_id;		
		$sql = "select contacts.*, contacts.ROWID as id from contacts inner join tags on tags.object_id = contacts.ROWID and tags.object_type='contact' where tags.name='$tag' and contacts.owner_id=:owner_id";
		$query = new Query(new Contact(array("owner_id"=>$owner_id)), $sql);
		$contacts = $query->execute(Repo::get_provider(), new Contact(), $sql);
		return $contacts;
	}
	public static function find_owned_by($owner_id){
		$owner_id = (int)$owner_id;
		$contacts = find_by::execute("owner_id=:owner_id", new Contact(array("owner_id"=>$owner_id)), new Contact());
		return $contacts;
	}
	public function owned_by($id){
		$this->sql[] = "contacts.owner_id=:owner_id";
		$this->owner_id = $id;
		return $this;
	}
	public static function find_by_id($id, $owner_id){
		return find_one_by::execute("ROWID=:id and owner_id=:owner_id", new Contact(array("owner_id"=>$owner_id, "id"=>(int)$id)));
	}
	public static function find_by_ids($ids, $owner_id){
		return find_by::execute("ROWID in (" . implode(",", $ids) . ") and owner_id=:owner_id", new Contact(array("owner_id"=>(int)$owner_id)));
	}

}