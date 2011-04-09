<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class Post extends ChinObject{
	public function __construct($values = array()){
		$this->owner_id = 0;
		$this->id = 0;
		$this->post_date = time();
		$this->post_date_gmt = gmmktime();
		$this->modified = time();
		$this->modified_gmt = gmmktime();
		$this->parent = 0;
		$this->comment_count = 0;
		parent::__construct($values);
	}
	public $id;
	public $title;
	public $body;
	public $owner_id;
	public $post_date;
	public $post_date_gmt;
	public $excerpt;
	public $status;
	public $comment_status;
	public $ping_status;
	public $password;
	public $name;
	public $to_ping;
	public $pinged;
	public $modified;
	public $modified_gmt;
	public $content_filtered;
	public $parent;
	public $url;
	public $type;
	public $mime_type;
	public $comment_count;
	public function get_excerpt(){
		if($this->excerpt !== null) return $this->excerpt;
		$lines = explode(PHP_EOL, $this->body);
		if(count($lines) > 0) return $lines[0];
		return $this->body;
	}
}