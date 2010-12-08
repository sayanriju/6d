<?php
	class_exists('AppResource') || require('AppResource.php');
    class_exists('Configuration') || require('models/Configuration.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('String') || require('lib/String.php');
	class_exists('Person') || require('models/Person.php');
    class ConfigurationResource extends AppResource{
        public function __construct($attributes = null){
            parent::__construct($attributes);
			if($this->config != null && $this->config->installed){
				$this->redirect_to(App::url_for(null));
			}
        }
        
        public function __destruct(){
            parent::__destruct();
        }
        
        public $configuration;

		public function put(Configuration $config, $should_overwrite_htaccess = false){
			if(!$should_overwrite_htaccess && file_exists('.htaccess')){
				Resource::setUserMessage("6d requires the ability to rewrite URLs. It creates a file called .htaccess in the application's folder to do this. This file exists. 6d needs to overwrite this file. Do you want to continue the installation and overwrite this file?");
				return $this->redirect_to(App::url_for('install'), array('overwrite'=>'false'));
			}
			$errors = array();
			$_SESSION['configuration'] = serialize($config);
			$this->configuration = $_SESSION['configuration'];
			$db = Factory::get($config->db_type, $config);
			$path = App::get_root_path();
			if(!is_writable($path)){
				self::setUserMessage("I was unable to create an httaccess file. I need write access to the folder that you're trying to install 6d.");
				$this->redirect_to(App::url_for('install'));
				return null;
			}
			$this->createHttaccessFile();
			try{
				$db->testConnection();
			}catch(Exception $e){
				$errors[] = $e->getCode() . ':' . $e->getMessage();
			}

			if(count($errors) > 0){
				try{
					$db->createDatabase($config->database);
					$errors = array();
				}catch(Exception $e){
					error_log('error message ' . $db->errorMessage);
				}
			}

			$errors = array_merge($config->validate(), $errors);
			try{
				if(count($errors) == 0){
					$config->site_password = $config->site_password;
					$config->installed = true;
					$config->save(App::get_root_path('AppConfiguration.php'));
					class_exists('AppConfiguration') || require('AppConfiguration.php');
				}
			}catch(Exception $e){
				$errors[] = $e->getMessage();
			}

			if(count($errors) == 0){
				error_log('installing...');
				$errors = $this->createTables($db, $config);
				error_log('done!');
			}
			if(count($errors) == 0){
				$person = Person::findByEmail($config->email);
				if($person == null){
					$member = new Member(null);
					$member->person->email = $config->email;
					$member->person->password = String::encrypt($config->site_password);
					$member->person->confirmation_password = String::encrypt($config->password);
					$member->person->name = $config->site_user_name;
					$member->person->is_approved = true;
					$member->person->is_owner = true;
					$member->person->uid = uniqid(null, true);
					$member->person->session_id = session_id();
					$member->person->do_list_in_directory = true;
					$member->person->url = str_replace('http://', '', String::replace('/\/$/', '', App::url_for(null)));
					$errors = Person::canSave($member->person);
					if(count($errors) == 0){
						$member->person->owner_id = 0;
						$member->member_name = $config->site_user_name;
						$member = Member::saveAsPerson($member);
						$super_admin = new SuperAdmin(array('person_id'=>$member->person_id));
						$super_admin = SuperAdmin::save($super_admin);
					}
				}
			}
			if(count($errors) > 0){
				$config->installed = false;
				$config->save(App::get_root_path('AppConfiguration.php'));
				$message = $this->render('install/error', array('message'=>"The following errors occurred when saving the configuration file. Please resolve and try again.", 'errors'=>$errors));					
				self::setUserMessage($message);
			}else{
				unset($_SESSION['configuration']);
				Resource::setUserMessage('Installation is complete! Sign in.');
			}
			$this->redirect_to(App::url_for('login'));
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
						if($reflector->hasMethod('upgrade')){
							$model = $reflector->newInstanceArgs(array(null, null));
							try{$model->upgrade($config);}catch(Exception $e){$errors[] = $e->getMessage();}
						}
					}
				}
				
				$folder->close();
				
				if(count($errors) == 0){
					
				}
								
			}
			return $errors;
		}
		private function createHttaccessFile(){
			$htaccess_file = 'htaccess.php';
			require($htaccess_file);
			$virtual_path = String::replace('/\/$', '', App::get_virtual_path());
			echo $virtual_path;
			$htaccess = String::replace('/6d/', $virtual_path, $htaccess);
			$did_write = file_put_contents(App::get_root_path('.htaccess'), $htaccess);
			$media_folder = 'media';
			if(!file_exists($media_folder)){
				mkdir($media_folder, 0777, true);
			}
			$media_htaccess = String::replace('/\#file_check_start(.*)\#file_check_end/', 'RewriteRule ^(.*)$ index.php?r=media/$1 [QSA,L]', $htaccess);
			$did_write_media_access_file = file_put_contents(App::get_root_path('media/.htaccess'), $media_htaccess);
			return $did_write && $did_write_media_access_file;
		}        

    }
?>