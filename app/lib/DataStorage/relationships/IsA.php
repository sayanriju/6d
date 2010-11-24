<?php
	class_exists('Relationship') || require('Relationship.php');
	class IsA extends Relationship{
		public function __construct($args){
			parent::__construct($args);
		}
		public function __destruct(){
			parent::__destruct();
		}		
	}
?>