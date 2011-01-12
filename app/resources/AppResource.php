<?php
	class_exists('Resource') || require('lib/Resource.php');
	class_exists('UserResource') || require('resources/UserResource.php');
	class_exists('Post') || require('models/Post.php');
	class_exists('Person') || require('models/Person.php');
	class_exists('Setting') || require('models/Setting.php');
	class_exists('NotificationCenter') || require('lib/NotificationCenter.php');
	class_exists('Photo') || require('models/Photo.php');
	class_exists('PluginController') || require('lib/PluginController.php');
	class_exists('Context') || require('lib/Context.php');
	class AppResource extends Resource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			$this->lang = Context::server('HTTP_ACCEPT_LANGUAGE');
			$this->charset = 'utf-8';
			$this->display_date = time();
			Post::add_observer($this, 'will_return_value_for_key', 'Post');
			$resource_name = strtolower(str_replace('Resource', '', $this->name));
			$this->resource_css = $resource_name . '.css';
			$this->resource_js = $resource_name . '.js';
			$admin_js = $resource_name . '_admin.js';
			$this->resource_js = $this->get_resource_js($this->resource_js);
			$this->resource_js .= AuthController::is_authorized() ? $this->get_resource_js($admin_js) : null;
			
			$this->resource_css = $this->get_resource_css($this->resource_css);
			$theme_path = App::get_theme_path('/ThemeController.php');
			if(file_exists($theme_path)){
				class_exists('ThemeController') || require($theme_path);
				$this->theme = new ThemeController($this);
			}			

			if(!class_exists('AppConfiguration')){
				if(!in_array(get_class($this), array('InstallResource', 'ConfigurationResource'))){
					$this->redirect_to(App::url_for('install'), null);
				}
			}else{
				$this->config = new AppConfiguration();
				try{
					$this->settings = Setting::findAll();
					// I was running into situations where the session was still set, but the 
					// logged in user had been deleted from the database. So I added code
					// to automatically log the user out if they're not found in the db.
					if(AuthController::authKey() !== null){
						Application::$current_user = Member::findByEmail(AuthController::authKey());
						if(Application::$current_user === null){
							AuthController::logout();
							$this->redirect_to(null);
						}
					}
				}catch(Exception $e){
					echo $e;
				}
			}
		}
		
		public function __destruct(){
			parent::__destruct();
		}
		public $lang;
		public $charset;
		public $member;
		public $show_notes;
		public $notes;
		public $theme;
		public $resource_css;
		public $resource_js;
		protected $settings;
		protected $config;
		public $q;
		public $current_user;
		public $display_date;
		protected function get_resource_css($file_name){
			$output = null;
			if(file_exists(App::get_theme_path('css/' . $file_name))){
				$output = App::url_for_theme('css/' . $file_name);
				$output = $this->to_link_tag('stylesheet', 'text/css', $output, 'screen,projection');
			}elseif(file_exists(App::get_root_path('css/' . $file_name))){
				$output = App::url_for('css/'. $file_name);
				$output = $this->to_link_tag('stylesheet', 'text/css', $output, 'screen,projection');
			}
			return $output;
		}
		protected function get_resource_js($file_name){
			$output = null;
			if(file_exists(App::get_theme_path('js/' . $file_name))){
				$output = App::url_for_theme('js/' . $file_name);
				$output = $this->to_script_tag('text/javascript', $output);
			}elseif(file_exists(App::get_root_path('/js/' . $file_name))){
				$output = App::url_for('js/' . $file_name);
				$output = $this->to_script_tag('text/javascript', $output);
			}
			return $output;
		}
		
		protected function set_unauthorized(){
			$this->status = new HttpStatus(401);
			$this->headers[] = new HttpHeader(array('location'=>App::url_for('login')));
		}
		protected function set_not_found(){
			$this->status = new HttpStatus(404);
		}
		public function will_return_value_for_key($key, $obj, $val){
			return $val;
		}
		public function to_link_tag($rel, $type, $url, $media){
			return sprintf('<link rel="%s" type="%s" href="%s" media="%s" />', $rel, $type, $url, $media);
		}
		public function to_script_tag($type, $url){
			return sprintf('<script type="%s" src="%s"></script>', $type, $url);
		}

		public function getHome_page_post_id(){
			if($this->settings != null){
				foreach($this->settings as $setting){
					if($setting->name == 'home_page_post_id'){
						return $setting->value;
					}
				}
			}
			return 0;
		}
		
		public function did_finish_dispatching(){
			parent::did_finish_dispatching();
		}
		public function did_render_layout($layout, $output){			
			if(class_exists('AppConfiguration')){
				$output = $this->filter_header($output);
				$output = $this->filter_footer($output);
			}
			return $output;
		}
		protected function filter_text($text){
			$post_filters = $this->get_plugins('filters', 'PostFilter');
			foreach($post_filters as $filter){
				$text = $filter->execute($text);
			}
			return $text;
		}
		private function filter_header($output){
			$filters = PluginController::get_plugins('filters', 'HeaderFilter');
			foreach($filters as $filter){
				$output = $filter->execute($output);
			}
			return $output;
		}
		private function filter_footer($output){
			$filters = $this->get_plugins('filters', 'FooterFilter');
			foreach($filters as $filter){
				$output = $filter->execute($output);
			}
			if(count(console::$messages) > 0){
				$output = str_replace('</body>', '<pre id="__6d_console">' . implode('', console::$messages) . '</pre></body>', $output);
			}
			return $output;
		}
		
		protected function get_plugins($folder_name, $name){
			$files = $this->get_files($folder_name, $name);
			$plugins = array();
			foreach($files as $file){
				$parts = explode('/', $file);
				$class_name = array_pop($parts);
				$class_name = str_replace('.php', '', $class_name);
				class_exists($class_name) || require($file);
				$plugins[] = new $class_name();
			}
			return $plugins;
		}

		private function get_files($folder_name, $name){
			$root = App::get_root_path('/' . $folder_name);
			$folders = $this->get_folders($root);
			$plugin_paths = array();
			foreach($folders as $folder){
				$dir = dir($folder);
				while(($entry = $dir->read()) !== false){
					if(strpos($entry, '.') !== 0){
						$file_name = $dir->path . '/' . $entry;
						if(!is_dir($file_name) && stripos($entry, $name . '_') !== false){
							$plugin_paths[] = $file_name;
						}
					}
				}
			}
			return $plugin_paths;
		}
		private function get_folders($path){
			$folders = array();
			$folder = dir($path);
			if($folder !== false){
				while(($entry = $folder->read()) !== false){
					if(strpos($entry, '.') !== 0){
						$file_name = $folder->path .'/'. $entry;
						if(is_dir($file_name)){
							$folders[] = $file_name;
						}
					}
				}
			}
			return $folders;
		}
		public function get_title_from_output($output){
			$matches = array();
			preg_match( '/\<h1\>.*\<\/h1\>/' , $output, $matches);
			if(count($matches) > 0){
				return String::stripHtmlTags($matches[0]);
			}else{
				return null;
			}
		}		
		public static function get_random_index_with_weights($weights) {
		    $r = mt_rand(1,1000);
		    $offset = 0;
		    foreach ($weights as $k => $w) {
		        $offset += $w*1000;
		        if ($r <= $offset) {
		            return $k;
		        }
		    }
		}
	}
?>