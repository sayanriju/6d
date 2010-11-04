<?php

class HomePage{
	
	public function __construct(){
		$this->limit = 30;
	}
	public function __destruct(){}
	private $limit;
	public function getLimit(){
		return $this->limit;
	}

}