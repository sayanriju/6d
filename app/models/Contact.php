<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Contact extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->owner_id = 0;
		parent::__construct($values);
	}
	public $id;
	public $owner_id;
	public $name;
	public $url;
	public $photo_url;
	public $email;
	public $json;
}