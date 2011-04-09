<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("Page") || require("models/Page.php");
class PagesResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public $state;
	public $page;
	
	public function get($name){
		$this->title = "Edit a Page";
		$this->page = find_one_by::execute("name=:name and owner_id=:owner_id", new Page(array("owner_id"=>AuthController::$current_user->id, "name"=>$name)));
		$this->output = View::render("page/index", $this);
		return View::render_layout("default", $this);			
	}
	public function post(Page $page){
		$this->page = find_one_by::execute("name=:name and owner_id=:owner_id", new Page(array("owner_id"=>AuthController::$current_user->id, "name"=>$name)));

	}
	public function put(Page $page){

	}
}

?>