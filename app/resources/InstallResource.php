<?php
	class_exists('AppResource') || require('AppResource.php');
    class_exists('Configuration') || require('models/Configuration.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('String') || require('lib/String.php');
	class_exists('Person') || require('models/Person.php');
    class InstallResource extends AppResource{
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

        public function get(){
			if(!array_key_exists('configuration', $_SESSION)){
				$_SESSION['configuration'] = serialize(new Configuration(array('user_name'=>'sixd', 'password'=>'get6d', 'host'=>'localhost', 'prefix'=>'sixd_', 'database'=>'get6d_development', 'theme'=>'default', 'db_type'=>'MySql', 'email'=>'graphite@joeyguerra.com')));
			}else if(file_exists('AppConfiguration.php')){
				class_exists('AppConfiguration') || require('AppConfiguration.php');
				$_SESSION['configuration'] = serialize(new AppConfiguration(null));
			}else{
				$this->configuration = new Configuration();
				$_SESSION['configuration'] = serialize($this->configuration);
			}
			$this->title = "Createa a Configuration File";
			$this->configuration = unserialize($_SESSION['configuration']);			
			$this->output = $this->render('install/config', null);
			return $this->render_layout('install', null);			
        }
    }
?>