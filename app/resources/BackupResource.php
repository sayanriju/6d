<?php
class_exists('UserResource') || require('UserResource.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('FriendRequest') || require('models/FriendRequest.php');
class_exists('Member') || require('models/Member.php');
class_exists('Notification') || require('models/Notification.php');
class_exists('Setting') || require('models/Setting.php');
class_exists('SuperAdmin') || require('models/SuperAdmin.php');
class_exists('Tag') || require('models/Tag.php');
class_exists('Model') || require('models/Model.php');

class BackupResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		if(!AuthController::isSuperAdmin()){
			throw new Exception(FrontController::UNAUTHORIZED, 401);
		}
		$this->files = $this->getBackupFiles();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public static $model_names = array('Post', 'Person', 'FriendRequest', 'Member', 'Notification', 'Setting', 'SuperAdmin', 'Tag');

	public $files;
	public function get(){
		$this->title = "Backup your data";
		$this->output = $this->renderView('backup/index', null);
		return $this->renderView('layouts/default', null);
	}
	public function post(){
		$this->archiveAll();
		$this->files = $this->getBackupFiles();
		$this->title = "Backup your data";
		$this->output = $this->renderView('backup/index', null);
		return $this->renderView('layouts/default', null);
	}
	public function put($file_name){
		$this->title = "Restoring your $file_name backup";
		$archive = new ZipArchive();
		if($matches = String::find('/b_\d+\.zip/', $file_name) !== false && file_exists($file_name)){
			$temp_file = $this->archiveAll('temp_b');
			$this->restore($file_name);
			$this->delete($temp_file);
		}else{
			self::setUserMessage("That backup doesn't exist.");
		}
		$this->output = $this->renderView('backup/index', null);
		return $this->renderView('layouts/default', null);
	}
	public function delete($file_name){
		unlink($file_name);
		self::redirectTo('backup');
	}
	private function archiveAll($prefix = 'b'){
		$archive = new ZipArchive();
		$file_name = sprintf("%s_%s.zip", $prefix, date('Ymdh', time()));
		if($archive->open($file_name, ZIPARCHIVE::CREATE) === false){
			self::setUserMessage("Unable to create the archive. You need to give write permissions to the root folder.");
		}else{
			foreach(self::$model_names as $name){
				$this->archive($name, $archive);
			}
			self::setUserMessage($archive->status == ZIP_ER_OK ? sprintf('<a href="%s%s">Backup</a> has been created.', FrontController::urlFor(null), $file_name) : sprintf('Failed archiving your data: %s - %s', $archive->GetStatusString(), $archive->status));
			$archive->close();
			$this->deleteFiles(self::$model_names);
		}
		return $file_name;
	}
	private function restore($file_name){
		$archive = new ZipArchive();
		$status = null;
		$errors = array();
		if($archive->open($file_name) !== false){
			$archive->extractTo('b');
			$status = $archive->status;
			$archive->close();
			foreach(self::$model_names as $name){
				$text = file_get_contents(sprintf("b/%s.serialized", $name));
				$list = unserialize($text);
				$class_name = null;
				if($list !== null && count($list) > 0){
					$class_name = get_class($list[0]);
					try{
						$errors = Model::deleteTable($class_name, $this->config);
					}catch(Exception $e){
						$errors[] = $e;
					}
				}
				foreach($list as $obj){
					$obj = Model::map($obj, new $class_name());
					$obj->id = null;
					if(method_exists($obj, 'getOwner_id')){
						$obj->owner_id = 1;
					}
					if(method_exists($obj, 'getUid') && $obj->uid == null){
						$obj->uid = uniqid(null, true);
					}
					if(method_exists($obj, 'getSession_id') && $obj->session_id == null){
						$obj->session_id = session_id();
					}

					console::log('restoring: ' . $class_name);
					$obj->install($this->config);
					try{
						$class_name::save($obj);
					}catch(Exception $e){
						console::log($e);
					}
				}
				
				if(count($error_messages) > 0){
					self::setUserMessgae(implode('<br />', $error_messages));
				}
			}
			$this->deleteRecursively('b');
			unlink($file_name);
		}
		return $status;
	}
	private function deleteRecursively($dir){
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir"){
						$this->deleteRecursively($dir."/".$object);
					}else{
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects); 
			rmdir($dir); 
		}else{
			unlink($dir);
		}
	}
	private function getBackupFiles(){
		$folder = dir('./');
		$files = array();
		while (false !== ($entry = $folder->read())){
			if($entry != '.' && $entry != '..' && file_exists($entry)){
				if(strpos($entry, 'b_') !== false){
					$files[] = $entry;
				}
			}
		}
		$folder->close();
		return $files;
	}
	private function deleteFiles($names){
		foreach($names as $name){
			unlink($name . '.serialized');
		}
	}
	private function archive($name, $archive){
		$list = $name::findAll();
		$text = serialize($list);
		$file_name = $name . '.serialized';
		file_put_contents($file_name, $text);
		$archive->addFile($file_name);
	}
}

?>