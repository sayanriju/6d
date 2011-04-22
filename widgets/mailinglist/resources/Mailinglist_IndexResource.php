<?php
class_exists("AppResource") || require("AppResource.php");
class Mailinglist_IndexResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function get(){
		$view = "widgets/mailinglist/views/index";
		return View::render_absolute($view, $this);
	}
}