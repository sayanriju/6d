<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Member extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->is_owner = false;
		$this->expiry = time();
		$this->in_directory = false;
		parent::__construct($values);
	}
	public $in_directory;
	public $name;
	public $id;
	public $password;
	public $is_owner;
	public $hash;
	public $expiry;
	public $signin;
	public $display_name;
}