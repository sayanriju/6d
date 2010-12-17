<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('Tag') || require('Tag.php');
	class_exists('Post') || require('Comment.php');
	class Author{
		public function __construct($attributes = null){
			if(array_key_exists('id', $attributes)){
				$this->id = $attributes['id'];
			}
			if(array_key_exists('parent_id', $attributes)){
				$this->parent_id = $attributes['parent_id'];
			}
			if(array_key_exists('name', $attributes)){
				$this->name = $attributes['name'];
			}
			if(array_key_exists('source', $attributes)){
				$this->source = $attributes['source'];
			}
			if(array_key_exists('photo_url', $attributes)){
				$this->photo_url = $attributes['photo_url'];
			}
		}
		public function __destruct(){}
		public $id;
		public $parent_id;
		public $name;
		public $source;
		public $photo_url;
	}
	class Post extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			$this->type = 'post';
			$this->is_published = false;
		}
		public function __destruct(){
			parent::__destruct();
		}
		public static $status = 'status';
		private $date;
		public function getDate(){
			return $this->date;
		}
		private $id;
		public function getId(){
			return $this->id;
		}
		public function setId($val){
			$this->id = $val;
		}

		private $person_post_id;
		public function getPerson_post_id(){
			return $this->person_post_id;
		}
		public function setPerson_post_id($val){
			$this->person_post_id = $val;
		}

		private $title;
		public function getTitle(){
			return $this->title;
		}
		public function setTitle($val){
			$this->title = $val;
		}

		private $type;
		public function getType(){
			return $this->type;
		}
		public function setType($val){
			$this->type = $val;
		}

		private $body;
		public function getBody(){
			return $this->body;
		}
		public function setBody($val){
			$this->body = $val;
		}

		private $source;
		public function getSource(){
			return $this->source;
		}
		public function setSource($val){
			$this->source = $val;
		}
		
		private $url;
		public function getUrl(){
			return $this->url;
		}
		public function setUrl($val){
			$this->url = $val;
		}

		private $description;
		public function getDescription(){
			return $this->description;
		}
		public function setDescription($val){
			$this->description = $val;
		}

		private $created;
		public function getCreated(){
			return $this->created;
		}
		public function setCreated($val){
			$this->created = $val;
		}
		private $post_date;
		public function getPost_date(){
			return $this->post_date;
		}
		public function setPost_date($val){
			$this->post_date = $val;
		}

		private $custom_url;
		public function getCustom_url(){
			return $this->custom_url;
		}
		public function setCustom_url($val){
			$this->custom_url = $val;
		}

		private $is_published;
		public function getIs_published(){
			return $this->is_published;
		}
		public function setIs_published($val){
			$this->is_published = $val;
		}

		private $tags;
		public function getTags(){
			return $this->tags;
		}
		public function setTags($val){
			$this->tags = $val;
		}
		
		public $password;
		public function getPassword(){
			return $this->password;
		}
		public function setPassword($val){
			if($val == null || strlen($val) == 0){
				$this->password = null;
			}else{
				$this->password = String::encrypt($val);
			}
		}

		private $owner_id;
		public function getOwner_id(){
			return $this->owner_id;
		}
		public function setOwner_id($val){
			$this->owner_id = $val;
		}
		private $conversation;
		public function getConversation(){
			return $this->conversation;
		}
		public function setConversation($val){
			$this->conversation = $val;
		}
		private $updated;
		public function getUpdated(){
			return $this->updated;
		}
		public function setUpdated($val){
			$this->updated = $val;
		}

		public function isHomePage($home_page_post_id){
			return $this->id > 0 && $this->id == $home_page_post_id;
		}
		// I need a way to tell the data storage whether or not to add the id in the sql statement
		// when inserting a new record. This is it. The data storage should default it to false, so
		// if this method doesn't exist, it'll default to false.
		public function should_insert_id(){
			return true;
		}
		public function will_add_field_to_save_list($name, $value){
			if($name === 'id' && ($this->id === null || strlen($this->id) === 0)){
				$this->id = uniqid(null, true);
				return $this->id;
			}
			return $value;			
		}
		
		private $author;
		public function get_author(){
			if($this->author !== null){
				return $this->author;
			}
			if($this->owner_id === null){
				return null;
			}
			$this->author = Person::findByUrlAndOwnerId($this->source, $this->owner_id);
			if($this->author === null){
				$this->author = Person::findById($this->owner_id);
			}
			$this->author->profile = unserialize($this->author->profile);
			return $this->author;
		}
		public static function get_excerpt($post, $include_html = true){
			$p_start = '<p>';
			$p_end = '</p>';
			$include_tags = '<a>';
			if(!$include_html){
				$p_start = null;
				$p_end = null;
				$include_tags = null;
			}
			if(!empty($post->description)) return $p_start . $post->description . $p_end;
			$body = String::stripHtmlTags(urldecode($post->body), $include_tags);
			$body = String::truncate($body, 400);
			$body = str_replace(PHP_EOL, $p_end . $p_start, $body);
			return $p_start . $body . $p_end;
		}
		public static function searchForPublished($q, $start = 0, $limit = 5, $sort_by = 'post_date', $sort_by_direction = 'desc', $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$q = '%' . $q . '%';
			$query = sprintf("(title like '%s' or description like '%s' or body like '%s') and is_published=1 and owner_id=%d", $q, $q, $q, $owner_id);
			$list = $db->find(new ByClause($query, $post->relationships, array($start, $limit), array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function search($q, $start, $limit, $sort_by, $sort_by_direction = 'desc', $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$q = '%' . $q . '%';
			$query = sprintf("(title like '%s' or description like '%s' or body like '%s') and owner_id=%d", $q, $q, $q, $owner_id);
			$list = $db->find(new ByClause($query, $post->relationsips, array($start, $limit), array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findTodaysPosts($start, $limit, $sort_by, $sort_by_direction, $is_authed, $owner_id){
			$list = null;
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.post_date';
			}else{
				$sort_by = $post->getTableName() . '.' . $sort_by;
			}
			if($sort_by_direction === null){
				$sort_by_direction = 'desc';
			}
			$clause = null;
			if($is_authed){
				$clause = new ByClause(sprintf("owner_id=%d and updated >= '%s'", $owner_id, date('Y/m/d', time())), null, $limit > 0 ? array($start, $limit) : 0, array($sort_by=>$sort_by_direction));
			}else{
				$clause = new ByClause(sprintf("is_published = 1 and owner_id=%d and updated >= '%s'", $owner_id, date('Y/m/d', time())), null, $limit > 0 ? array($start, $limit) : 0, array($sort_by=>$sort_by_direction));
			}
			$list = $db->find($clause, $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findMostRecentStatus($owner_id){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$clause = new ByClause(sprintf("is_published = 1 and owner_id=%d and type='status'", $owner_id), null, 1, array('post_date'=>'desc'));
			$list = $db->find($clause, new Post());
			$list = ($list == null ? new Post() : (is_array($list) ? $list[0] : $list));
			return $list;
		}
		public static function findAll(){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new All(null, null, 0, null), new Post());
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function get_total_published_posts($owner_id, $sort_by = array('post_date'=>'desc')){
			$owner_id = (int)$owner_id;
			$config = new AppConfiguration();
			$post = new Post(null);
			$post_count = (object) array('number'=>0);
			$db = Factory::get($config->db_type, $config);
			$post_count = $db->find(new All(sprintf("select count(*) as number from %s where is_published=1 and owner_id=%d and type not in ('status', 'page')", $post->getTableName(), $owner_id), $sort_by, 1, null), $post_count);
			$post_count->number = (int)$post_count->number;
			return $post_count;
		}
		public static function findPublished($start, $limit, $sort_by, $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null){
				$sort_by = array($post->getTableName() . '.id'=>'desc');
			}
			$list = $db->find(new ByClause(sprintf("is_published=1 and owner_id=%d", $owner_id), null, array($start, $limit), $sort_by), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findPublishedPosts($start, $limit, $sort_by, $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || count($sort_by) === 0){
				$sort_by = array($post->getTableName() . '.id'=>'desc');
			}
			$clause = new ByClause(sprintf("is_published=1 and type not in ('status', 'page') and owner_id=%d", $owner_id), null, array($start, $limit), $sort_by);
			$list = $db->find($clause, $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findByPerson(Person $person, $start, $limit, $sort_by, $sort_by_direction, $owner_id){
			$sort_by_direction = ($sort_by_direction !== null ? $sort_by_direction : 'desc');
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$start_limit = null;
			if($limit > 0){
				$start_limit = array($start, $limit);
			}else{
				$start_limit = $limit;
			}
			$list = $db->find(new ByClause(sprintf("source = %s and owner_id=%s", ($person->url === null ? "''" : "'" . $person->url . "'"), $owner_id), null, $start_limit, array($sort_by=>$sort_by_direction)), $post);
			
			$list = ($list == null ? array() : $list);
			return $list;
		}
		public static function findByTag($tag, $start, $limit, $sort_by, $sort_by_direction = 'desc', $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$tag->text = urlencode($tag->text);
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause("tags like '%{$tag->text}%' and owner_id={$owner_id}", null, $limit > 0 ? array($start, $limit) : null, array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : $list);
			return $list;
		}
		public static function findPublishedByTag($tag, $start, $limit, $sort_by, $sort_by_direction = 'desc', $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$tag->text = urlencode($tag->text);
			$owner_id = (int)$owner_id;
			$clause = new ByClause("tags like '%{$tag->text}%' and is_published=1 and owner_id={$owner_id}", null, array($start, $limit), array($sort_by=>$sort_by_direction));
			$list = $db->find($clause, $post);
			$list = ($list == null ? array() : $list);
			return $list;
		}

		public static function find($start, $limit, $sort_by, $sort_by_direction = 'desc', $owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if($sort_by === null || strlen($sort_by) === 0){
				$sort_by = $post->getTableName() . '.id';
			}
			$start_limit = null;
			if($limit > 0){
				$start_limit = array($start, $limit);
			}else{
				$start_limit = $limit;
			}
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause("owner_id={$owner_id}", null, $start_limit, array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findPublishedPages($owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause("type='page' and is_published=1 and owner_id={$owner_id}", null, 0, null), $post);
			return $list;
		}
		public static function findFriendsPublishedStatii($owner_id){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$list = $db->find(new ByClause("type='status' and is_published=1 and owner_id={$owner_id} and person_post_id <> null", null, 0, null), $post);
			return $list;
		}
		
		public static function findPublishedByCustomUrl($custom_url, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$cusomt_url = $db->sanitize($custom_url);
			$clause = new ByClause("is_published=1 and custom_url='{$custom_url}' and owner_id={$owner_id}", null, 1, array('post_date'=>'desc'));
			$post = $db->find($clause, new Post(null));
			return $post;
		}
		
		public static function findByAttribute($name, $value, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$name = $db->sanitize($name);
			$value = $db->sanitize($value);
			$post = $db->find(new ByClause(sprintf("%s='%s' and owner_id=%d", $name, $value, $owner_id), null, 1, null), new Post(null));
			return $post;
		}
	
		public static function findById($id, $owner_id){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$clause = new ByClause(sprintf("id='%s' and owner_id=%d", $id, $owner_id), null, 1, null);
			$post = $db->find($clause, new Post(null));
			return $post;
		}
		public static function findHomePage($id = null, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$post = $db->find(new ByClause(sprintf("id='%s' and is_published=1 and owner_id=%d", $id, $owner_id), null, 1, null), new Post(null));
			return $post;
		}
		
		public static function findByPersonPostId($id = 0, $owner_id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$owner_id = (int)$owner_id;
			$post = $db->find(new ByClause("person_post_id = '{$id}' and owner_id={$owner_id}", null, 1, null), new Post(null));
			return $post;
		}
		public function canModify($user){
			return $this->owner_id === $user->person_id;
		}
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'posts';
		}
		public static function delete(Post $post){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			return $db->delete(null, $post);
		}
		public static function save(Post $post){
			$errors = self::canSave($post);
			$config = new AppConfiguration();
			$tags = $post->tags == null ? array() : $post->tags;
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				if(is_array($post->tags)) $post->tags = implode(',', $post->tags);
				$db->save(null, $post);
				$existing_tags = Tag::findAllForPost($post->id, $post->owner_id);
				if($existing_tags != null){
					foreach($tags as $tag){
						Tag::delete($tag);
					}
				}
				$post->tags = String::explodeAndTrim($post->tags);
				foreach($post->tags as $tag_text){
					Tag::save(new Tag(array('parent_id'=>$post->id, 'type'=>'post', 'text'=>$tag_text, 'owner_id'=>$post->owner_id)));
				}
				self::notify('post_was_saved', $post, $post);
			}
			return array($post, $errors);
		}

		public static function canSave(Post $post){
			$errors = array();
			return $errors;
		}
		public function install($config){
			$message = '';
			$db = Factory::get($config->db_type, $config);
			try{
				$table = new Table($this->getTableName($config), $db);
				$table->addColumn('id', 'string', array('is_nullable'=>false, 'size'=>255));
				$table->addColumn('person_post_id', 'string', array('is_nullable'=>true, 'size'=>255));
				$table->addColumn('title', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('type', 'string', array('is_nullable'=>true, 'default'=>'post', 'size'=>80));
				$table->addColumn('body', 'text', array('is_nullable'=>true, 'default'=>''));
				$table->addColumn('source', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('url', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('description', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('post_date', 'datetime', array('is_nullable'=>true, 'default'=>null));
				$table->addColumn('created', 'datetime', array('is_nullable'=>false));
				$table->addColumn('custom_url', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('tags', 'text', array('is_nullable'=>true));
				$table->addColumn('is_published', 'boolean', array('is_nullable'=>true, 'default'=>false));
				$table->addColumn('password', 'string', array('is_nullable'=>true, 'size'=>255));
				$table->addColumn('owner_id', 'biginteger', array('is_nullable'=>false));
				$table->addColumn('conversation', 'text', array('is_nullable'=>true));
				$table->addColumn('updated', 'timestamp', array('is_nullable'=>false, 'default'=>'CURRENT_TIMESTAMP', 'extra'=>'on update CURRENT_TIMESTAMP'));
				
				$table->addKey('primary', 'id');
				$table->addKey('key', array('owner_id_key'=>'owner_id'));
				$table->addKey('key', array('title_key'=>'title'));
				$table->addKey('key', array('custom_url_key'=>'custom_url'));
				$table->addKey('key', array('is_published_key'=>'is_published'));
				$table->addOption('ENGINE=MyISAM DEFAULT CHARSET=utf8');
				$errors = $table->save();
				if(count($errors) > 0){
					foreach($errors as $error){
						$message .= $error;
					}
					throw new Exception($message);
				}
			}catch(Exception $e){
				$db->deleteTable($this->getTableName($config));
				throw $e;
			}
		}
	}
?>