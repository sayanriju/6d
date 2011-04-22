<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Member extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->is_owner =  false;
		$this->expiry = time();
		$this->in_directory = false;
		parent::__construct($values);
	}
	public $signin;
	public $in_directory;
	public $name;
	public $id;
	public $password;
	public $is_owner;
	public $hash;
	public $expiry;
	public $display_name;
	public $email;
	public static function find_by_name($name){
		return find_one_by::execute("name=:name", new Member(array("name"=>$name)));
	}
	public static function find_owner(){
		return find_one_by::execute("is_owner", new Member());
	}
	public static function find_by_id($id){
		return find_one_by::execute("ROWID=:id", new Member(array("id"=>(int)$id)));
	}
	public static function find_existing_by_signin($signin, $id){
		return find_one_by::execute("signin=:signin and ROWID != :id", new Member(array("signin"=>$signin, "id"=>(int)$id)));
	}
	public static function find_all($page, $limit){
		return find_by_with_limit::execute(null, new Member(), $page, $limit, "order by members.name");
	}
	public static function find_in_directory($page, $limit){
		return find_by_with_limit::execute("in_directory=:in_directory", new Member(array("in_directory"=>1)), $page, $limit, "order by members.name");
	}
	public static function find_by_signin_and_password($signin, $password){
		return find_one_by::execute("signin=:signin and password=:password", new Member(array("signin"=>$signin, "password"=>$password)));
	}
}