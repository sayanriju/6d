<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Message extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->owner_id = 0;
		$this->date = time();
		parent::__construct($values);
	}
	public $id;
	public $owner_id;
	public $email;
	public $body;
	public $date;
	public $sent;
	public $delivered;
}