<?php
	class_exists('FindCommand') || require('FindCommand.php');
	class ById extends FindCommand{
		public function __construct($id, $relationships = null){
			$this->id = $id;
			parent::__construct($relationships, 1, null);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $id;
	}
?>