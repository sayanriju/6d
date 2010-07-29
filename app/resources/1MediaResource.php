<?php

class MediaResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
		array_shift($this->url_parts);
		if(count($this->url_parts) > 0){
			$this->id = $this->url_parts[0];
			$file_type = explode('.', $this->id);
			if(count($file_type) > 0){
				$this->id = $file_type[0];
				$this->file_type = $file_type[1];
			}
		}
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $id;
	public $file_type;
	public function get(){
		return $this->id;
	}
}