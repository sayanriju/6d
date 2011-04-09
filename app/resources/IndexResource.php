<?php
class_exists("AppResource") || require("AppResource.php");
class IndexResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function get(){
		$this->title = "A RESTful framework in PHP";
		$view = "index/index";
		if($html = $this->get_page_content_for_member($this->resource_name)) $this->output = $html;
		if($this->output === null) $this->output = View::render($view, $this);
		return View::render_layout("default", $this);
	}
}