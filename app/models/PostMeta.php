<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class PostMeta extends ChinObject{
	public function __construct($values = array()){
		$this->id = 0;
		$this->post_id = 0;
		parent::__construct($values);
	}
	public $id;
	public $post_id;
	public $key;
	public $value;
}