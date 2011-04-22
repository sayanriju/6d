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
	public static function find_by_id($id){
		return find_one_by::execute("ROWID=:id", new Post(array("id"=>(int)$id)));
	}
	public static function find_owned_by($owner_id, $page, $limit){
		return find_by_with_limit::execute("owner_id=:owner_id"
			, new Post(array("owner_id"=>(int)$owner_id))
			, (int)$page, (int)$limit, "order by posts.post_date desc");
	}
	public static function find_by_id_and_owned_by($id, $owner_id){
		return find_one_by::execute("ROWID=:id", new Post(array("id"=>(int)$id, "owner_id"=>(int)$owner_id)));
	}
	public static function find_public_with_limit($owner_id, $page, $limit){
		return find_by_with_limit::execute("status='public' and owner_id=:owner_id"
			, new Post(array("owner_id"=>(int)$owner_id))
			, (int)$page, (int)$limit, "order by posts.post_date desc");
	}
	public static function find_public_count($owner_id){
		return find_count_by::execute("status='public' and owner_id=:owner_id", new Post(array("owner_id"=>$owner_id)));
	}
	public static function find_public_page($name, $owner_id){
		return find_one_by::execute("owner_id=:owner_id and status=:status and name=:name", new Post(array("status"=>"public", "name"=>$name, "owner_id"=>(int)$owner_id)), new Post());
	}
	public static function find_page_by_name($name, $owner_id){
		return find_one_by::execute("type='page' and name=:name and owner_id=:owner_id", new Post(array("owner_id"=>(int)$owner_id, "name"=>$name)));
	}
	public static function find_public_attachments_owned_by($owner_id, $page, $limit){
		return find_by_with_limit::execute("status='public' and owner_id=:owner_id and type=:type"
			, new Post(array("type"=>"attachment", "owner_id"=>$owner_id))
			, (int)$page, (int)$limit, "order by posts.post_date desc");
	}
}