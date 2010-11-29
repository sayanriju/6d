<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('Post') || require('Post.php');
	class Photo extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			$this->is_published = false;
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		private $id;
		public function getId(){
			return $this->id;
		}
		public function setId($val){
			$this->id = $val;
		}

		private $post_id;
		public function getPostId(){
			return $this->post_id;
		}
		public function setPostId($val){
			$this->post_id = $val;
		}

		private $src;
		public function getSrc(){
			return $this->src;
		}
		public function setSrc($val){
			$this->src = $val;
		}

		private $title;
		public function getTitle(){
			return $this->title;
		}
		public function setTitle($val){
			$this->title = $val;
		}

		private $description;
		public function getDescription(){
			return $this->description;
		}
		public function setDescription($val){
			$this->description = $val;
		}

		private $album;
		public function getAlbum(){
			return $this->album;
		}
		public function setAlbum($val){
			$this->album = $val;
		}

		private $tags;
		public function getTags(){
			return $this->tags;
		}
		public function setTags($val){
			$this->tags = $val;
		}

		private $timestamp;
		public function getTimestamp(){
			return $this->timestamp;
		}
		public function setTimestamp($val){
			$this->timestamp = $val;
		}
		private $owner_id;
		public function getOwner_id(){
			return $this->owner_id;
		}
		public function setOwner_id($val){
			$this->owner_id = $val;
		}
		
		// I need a way to tell the data storage whether or not to add the id in the sql statement
		// when inserting a new record. This is it. The data storage should default it to false, so
		// if this method doesn't exist, it'll default to false.
		public function should_insert_id(){
			return true;
		}
		public function will_add_field_to_save_list($name, $value){
			
			if($name == 'id' && ($this->id === null || strlen($this->id) === 0)){
				return uniqid(null, true);
			}
			return $value;
		}
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'photos';
		}
		private static function deleteEmptyFolder($file_name_with_path){
			$parts = explode(DIRECTORY_SEPARATOR, $file_name_with_path);
			array_pop($parts);
			$folder = implode(DIRECTORY_SEPARATOR, $parts);
			$files = scandir($folder);
			if(count($files) === 2){
				do{
					rmdir(implode(DIRECTORY_SEPARATOR, $parts));
					$name = array_pop($parts);
				}while(is_numeric($name));
			}
		}
		public static function delete($file_name_with_path){
			$file_name_with_path = str_replace('/', DIRECTORY_SEPARATOR, $file_name_with_path);
			self::notify('will_delete_photo', new Photo(), $file_name_with_path);
			$did_delete = false;
			
			if(file_exists($file_name_with_path)){
				$did_delete = unlink($file_name_with_path);
			}
			self::deleteEmptyFolder($file_name_with_path);
			return $did_delete;
		}
		public static function findAll($path = null){
			$root = ($path == null ? 'media' : $path);
			self::$images = array();
			if(file_exists($root)){
				self::traverse($root);
			}
			return self::$images;
		}
		
		private static $images;
		private static function traverse($path){
			$root = ($path == null ? 'media' : $path);
			if(!file_exists($root)){
				mkdir($root, 0777);
			}
			$folder = dir($root);
			if($folder != null){
				while (false !== ($entry = $folder->read())){
					if(strpos($entry, '.') !== 0){
						$file_name = $folder->path .'/'. $entry;					
						if(is_dir($file_name)){
							self::traverse($file_name);						
						}else{						
							self::$images[] = new Photo(array('src'=>$file_name));
						}
					}
				}
				$folder->close();
			}
		}		
	}
?>