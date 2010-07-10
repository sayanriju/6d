<?php

class ThemeController{
	public function __construct($resource){
		$this->resource = $resource;
	}
	public function __destruct(){}
	public $resource;
}