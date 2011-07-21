<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Comment extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->owner_id = 0;
		$this->post_id = 0;
		$this->date = date();
		$this->date_gmt = gmmktime();
		$this->karma = 0;
		$this->approved = 0;
		$this->parent = 0;
		$this->member_id = 0;
		parent::__construct($values);
	}
	public $id;
	public $owner_id;
	public $post_id;
	public $date;
	public $date_gmt;
	public $karma;
	public $approved;
	public $parent;
	public $member_id;
	public static function install(){
		$query = "create table if not exists comments (owner_id integer, post_id integer, date integer, date_gmt integer, karma integer, approved integer, parent integer, member_id integer)";
		$db = Repo::get_provider();
		$result = $db->query($query);
		return $result;
	}
	
}