<?php
class_exists('AppResource') || require('AppResource.php');
class BookmarkletsResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function get(){
		$view = 'bookmarklet/index';
		$this->title = 'List of Bookmarklets';
		if(count($this->url_parts) > 1 && strlen($this->url_parts[1]) > 0){
			$view = 'bookmarklet/' . $this->url_parts[1];
			$this->title = ucwords($this->url_parts[1]) . ' Bookmarklet';
		}

		$this->output = $this->render($view);
		return $this->render_layout('default');
	}
	public function get_delicious(){
		$this->title = 'Delicious Bookmarklet';
		$view = 'bookmarklet/delicious';
		$this->output = $this->render($view, null);
		return $this->render_layout('home', null);
	}
	public function get_twitter(){
		$this->title = 'Twitter Translator Bookmarklet';
		$view = 'bookmarklet/twitter_translate';
		$this->output = $this->render($view, null);
		return $this->render_layout('home', null);
	}
}