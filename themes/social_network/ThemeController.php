<?php

class ThemeController{
	public function __construct($resource){
		$this->resource = $resource;
		$this->name = 'The Social Network';
	}
	public function __destruct(){}
	public $name;
	public $resource;
}