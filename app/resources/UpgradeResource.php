<?php
	class_exists('AppResource') || require('AppResource.php');
    class_exists('Configuration') || require('models/Configuration.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('String') || require('lib/String.php');
	class_exists('Person') || require('models/Person.php');
    class UpgradeResource extends AppResource{
        public function __construct($attributes = null){
            parent::__construct($attributes);
			if(! AuthController::is_authorized()){
				Resource::redirect_to::setRequestedUrl('upgrade');
				throw new Exception(Resource::redirect_to::UNAUTHORIZED, 401);
			}
        }
        
        public function __destruct(){
            parent::__destruct();
        }
       
        public function get_upgrade(){
			$this->title = "App Upgrade";
			$this->output = $this->render('upgrade/index', null);
			return $this->render('layouts/install', null);
        }

		public function post_upgrade(){
			$db = Factory::get($this->config->db_type, $this->config);
			$errors = $this->createTables($db, $this->config);
			if(count($errors) > 0){
				$message = $this->render('install/error', array('message'=>"The following errors occurred when saving the configuration file. Please resolve and try again.", 'errors'=>$errors));					
				self::setUserMessage($message);
				$this->redirect_to('upgrade/index');
			}else{
				self::setUserMessage('Upgrade was successful');
				$this->redirect_to('install/done');
			}
		}
        
		private function createTables($db, Configuration $config){
			$didCreate = true;
			$errors = array();
			if(!$db->exists($config->database)){
				error_log('creating db...');
				$didCreate = $db->createDatabase($config->database);				
			}

			if(!$didCreate){
				$errors[] = 'Failed to create the database.';
			}else{
				$root = str_replace('resources', '', dirname(__FILE__));
				$folder = dir($root . 'models');
				$className = null;
				$reflector = null;
				error_log('installing schema...');
				while(($file = $folder->read()) !== false){
					error_log($file);
					if(preg_match('/^\./', $file) == 0){
						$className = str_replace('.php', '', $file);
						class_exists($className) || require('models/' . $file);
						$reflector = new ReflectionClass($className);
						if($reflector->hasMethod('install')){
							$model = $reflector->newInstanceArgs(array(null, null));
							try{$model->install($config);}catch(Exception $e){$errors[] = $e->getMessage();}
						}
					}
				}
				
				$folder->close();
				
				if(count($errors) == 0){
					
				}
								
			}
			return $errors;
		}        
    }
?>