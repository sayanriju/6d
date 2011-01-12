<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
class_exists('UserResource') || require('UserResource.php');
class QueryResource extends AppResource{
	public function __construct($attributes = null){
		if(!AuthController::is_authorized() || !Application::$current_user->person->is_owner){
			$this->set_unauthorized();
			return;
		}
		parent::__construct($attributes);
		$this->db = Factory::get($this->config->db_type, $this->config);
		$this->host = $this->config->host;
	}
	public function __destruct(){
		parent::__destruct();
	}
	private $db;
	public $databases;
	public $tables;
	public $field_name;
	public $db_name;
	public $host;
	public function post($query, $db_name){
		$query = str_replace('\\', '', $query);
		$this->db->useDatabase($db_name);
		$this->db->execute($query);
		$rows = $this->db->getRows();
		$this->output = $this->render('db/results', array('rows'=>$rows));
		return $this->render_layout('db', null);
	}	
}

?>