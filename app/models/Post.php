<?php
class_exists("ModelFactory") || require("ModelFactory.php");
class_exists("Post_meta") || require("Post_meta.php");
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
	
	private $post_meta;
	public function post_meta(){
		return $this->post_meta;
	}
	public function set_post_meta($value){
		$this->post_meta = $value;
	}
	public function get_excerpt(){
		if($this->excerpt !== null) return $this->excerpt;
		$lines = explode(PHP_EOL, $this->body);
		if(count($lines) > 0) return $lines[0];
		return $this->body;
	}
	public function extract_id($post){
		return $post->id;
	}
	public static function find_by_id($id){
		$post = Repo::find("select ROWID as id, * from posts where ROWID=:id", (object)array("id"=>(int)$id))->first(new Post());		
		if($post === null) return null;
		$meta = Post_meta::find_by_id($post->id);
		if($meta !== null){
			$meta->add_to_post($post);
		}
		return $post;
	}
	public static function find_owned_by($owner_id, $page, $limit){
		$posts = Repo::find("select ROWID as id, * from posts where owner_id=:owner_id order by post_date desc limit :page, :limit", (object)array("owner_id"=>AuthController::$current_user->id, "page"=>$page, "limit"=>$limit))->to_list(new Post());
		return $posts;
	}
	public static function find_type_owned_by($owner_id, $type, $page, $limit){
		$posts = Repo::find("select ROWID as id, * from posts where owner_id=:owner_id and type=:type order by post_date desc limit :page, :limit", (object)array("owner_id"=>$owner_id, "type"=>$type, "page"=>$page, "limit"=>$limit))->to_list(new Post());
		$ids = array_map(array("Post", "extract_id"), $posts);
		$meta = Post_meta::find_by_ids($ids);
		if($meta !== null){
			foreach($meta as $m){
				array_map(array($m, "add_to_post"), $posts);
			}
		}
		return $posts;
	}
	
	public static function find_by_id_and_owned_by($id, $owner_id){
		$post = Repo::find("select ROWID as id, * from posts where ROWID=:id and owner_id=:owner_id", (object)array("id"=>(int)$id, "owner_id"=>(int)$owner_id))->first(new Post);
		return $post;
	}
	public static function find_public_with_limit($owner_id, $page, $limit){
		$post = Repo::find("select ROWID as id, * from posts where owner_id=:owner_id order by post_date desc limit :page, :limit", (object)array("owner_id"=>(int)$owner_id, "page"=>(int)$page, "limit"=>(int)$limit))->to_list(new Post());
		return $post;
	}
	
	public static function find_public_count($owner_id){
		$count = Repo::find("select count(1) as total from posts where status='public' and owner_id=:owner_id", (object)array("owner_id"=>(int)$owner_id))->first(new Post());
		return $count;
	}
	public static function find_public_page($name, $owner_id){
		$page = Repo::find("select ROWID as id, * from posts where type='page' and status='public' and name=:name and owner_id=:owner_id and post_date<=:post_date", (object)array("name"=>$name, "owner_id"=>(int)$owner_id, "post_date"=>time()))->first(new Post());
		return $page;
	}
	public static function find_page_by_name($name, $owner_id){
		$page = Repo::find("select ROWID as id, * from posts where name=:name and type='page' and owner_id=:owner_id", (object)array("name"=>$name, "owner_id"=>(int)$owner_id))->first(new Post());
		return $page;
	}
	public static function find_public_attachments_owned_by($owner_id, $page, $limit){
		$attachments = Repo::find("select ROWID as id, * from posts where status='public' and owner_id=:owner_id and type='attachment' order by post_date desc limit :page, :limit", (object)array("owner_id"=>(int)$owner_id, "page"=>(int)$page, "limit"=>(int)$limit))->to_list(new Post());
		return $attachments;
	}
	public static function can_save($post){
		return array();
	}
	public static function save($post){
		return Repo::save($post);
	}
}